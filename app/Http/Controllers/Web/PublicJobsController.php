<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\Post;
use App\Models\Skill;
use App\Traits\Responses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class PublicJobsController
 * @package App\Http\Controllers\Web
 *
 * Handles public job listings for unauthenticated users
 */
class PublicJobsController extends Controller
{
    const DEFAULT_SEARCH_RADIUS = 100; // Wider radius for public users without location

    use Responses;

    /**
     * Show public landing page with modern homepage
     */
    public function index(Request $request)
    {
        // If there's a search query, show job listings instead
        if ($request->has('search')) {
            return $this->listByType($request, 'quick-job');
        }

        // Skills for category browsing
        $topSkills = Skill::orderBy('name')->take(12)->get();
        $allSkills = Skill::orderBy('name')->get();

        // Live post counts per type
        $quickJobCount  = Post::where('status', 'active')->where('type', 'QUICK_JOB')->whereNull('deleted_at')->count();
        $fixedTermCount = Post::where('status', 'active')->where('type', 'FIXED_TERM_JOB')->whereNull('deleted_at')->count();
        $permanentCount = Post::where('status', 'active')->where('type', 'PERMANENT_JOB')->whereNull('deleted_at')->count();
        $p2pCount       = Post::where('status', 'active')->where('type', 'P2P')->whereNull('deleted_at')->count();
        $volunteerCount = Post::where('status', 'active')->where('type', 'VOLUNTEER')->whereNull('deleted_at')->count();
        $totalJobs      = $quickJobCount + $fixedTermCount + $permanentCount + $p2pCount + $volunteerCount;

        // Platform stats
        $totalUsers = \App\Models\User::count();

        // Recent featured posts (excluding VOLUNTEER — no public show() view for it)
        $recentPosts = Post::with(['user', 'industry'])
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->whereIn('type', ['QUICK_JOB', 'FIXED_TERM_JOB', 'PERMANENT_JOB', 'P2P'])
            ->orderByDesc('created_at')
            ->take(6)
            ->get()
            ->map(function (Post $post) {
                $post['createdOn']     = $post->created_at->diffForHumans();
                $post['industry_name'] = $post->industry ? $post->industry->name : null;
                $post['display_title'] = $post->category ?? $post->title ?? 'Untitled';
                return $post;
            });

        return view('public.home', compact(
            'topSkills', 'allSkills',
            'quickJobCount', 'fixedTermCount', 'permanentCount', 'p2pCount', 'volunteerCount',
            'totalJobs', 'totalUsers', 'recentPosts'
        ));
    }

    /**
     * List jobs by type for public users
     *
     * @param Request $request
     * @param string $type - quick-job, fixed-term, permanent, p2p
     */
    public function listByType(Request $request, $type)
    {
        // Map URL-friendly type to database type
        $typeMap = [
            "quick-job" => "QUICK_JOB",
            "fixed-term" => "FIXED_TERM_JOB",
            "permanent" => "PERMANENT_JOB",
            "p2p" => "P2P"
        ];

        if (!isset($typeMap[$type])) {
            return back()->with("danger", "Invalid job type");
        }

        $dbType = $typeMap[$type];

        // Get user location from session (if set), otherwise use default
        $user_location = session('guest_location', null);
        $user_location_lat = null;
        $user_location_lng = null;

        if ($user_location) {
            $coords = json_decode($user_location);
            $user_location_lat = $coords->latitude ?? null;
            $user_location_lng = $coords->longitude ?? null;
        }

        // Get all active posts of this type
        $rawPosts = Post::with(['user', 'industry'])
            ->where("status", "active")
            ->where("type", $dbType)
            ->whereNull('deleted_at')
            ->orderByDesc("created_at")
            ->get();

        // Filter and enhance posts
        $filteredPosts = $rawPosts->map(function (Post $post) use ($user_location_lat, $user_location_lng, $request) {
            // Extract location coordinates
            $coords = json_decode($post->coords);
            $post_location_lat = isset($coords->latitude) ? $coords->latitude : explode(',', $post->coords)[0];
            $post_location_lng = isset($coords->longitude) ? $coords->longitude : explode(',', $post->coords)[1];

            // Calculate distance only if user has location
            $distance = 0;
            if ($user_location_lat && $user_location_lng) {
                $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
                $post["distance"] = number_format($distance, 2);
            } else {
                $post["distance"] = null; // No distance for users without location
            }

            // Add additional post data
            $post["organiser_name"] = $post->user->name ?? "Unknown";
            $post["createdOn"] = $post->created_at->diffForHumans();
            $post["industry"] = $post->industry ? $post->industry->name : null;

            // Calculate duration if dates are available
            if ($post->end_date && $post->start_date) {
                $post["duration"] = Carbon::parse($post->end_date)->diffInMonths(Carbon::parse($post->start_date));
            }

            // No "has_already_applied" for guests
            $post->has_already_applied = "no";

            return $post;
        });

        // Apply filters if present
        $filteredPosts = $filteredPosts->filter(function ($post) use ($request, $user_location_lat, $user_location_lng) {
            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = strtolower($request->search);
                $postTitle = strtolower($post->title ?? '');
                $postDescription = strtolower($post->description ?? '');
                $tags = json_decode($post->tags, true) ?? [];

                $titleMatch = str_contains($postTitle, $searchTerm);
                $descriptionMatch = str_contains($postDescription, $searchTerm);

                $tagMatch = false;
                foreach ($tags as $tag) {
                    if (str_contains(strtolower($tag), $searchTerm)) {
                        $tagMatch = true;
                        break;
                    }
                }

                if (!$titleMatch && !$descriptionMatch && !$tagMatch) {
                    return false;
                }
            }

            // Budget filtering
            if ($request->has('min_budget') && !empty($request->min_budget)) {
                $minBudget = (int)$request->min_budget;
                if ((int)$post->min_budget < $minBudget) {
                    return false;
                }
            }

            if ($request->has('max_budget') && !empty($request->max_budget)) {
                $maxBudget = (int)$request->max_budget;
                if ((int)$post->max_budget > $maxBudget) {
                    return false;
                }
            }

            // Distance/radius filtering (only if user has location)
            if ($user_location_lat && $user_location_lng) {
                if ($request->has('radius') && !empty($request->radius)) {
                    $searchRadius = (int)$request->radius;
                    $coords = json_decode($post->coords);
                    $post_location_lat = isset($coords->latitude) ? $coords->latitude : explode(',', $post->coords)[0];
                    $post_location_lng = isset($coords->longitude) ? $coords->longitude : explode(',', $post->coords)[1];

                    $distance = $this->get_distance(
                        $user_location_lat,
                        $user_location_lng,
                        $post_location_lat,
                        $post_location_lng,
                        "K"
                    );

                    if ($distance > $searchRadius) {
                        return false;
                    }
                }
            }

            // Skills filtering
            if ($request->has('skills') && !empty($request->skills)) {
                $requestSkills = is_array($request->skills) ? $request->skills : [$request->skills];
                $skillNames = Skill::whereIn('id', $requestSkills)->pluck('name')->toArray();
                $tags = json_decode($post->tags, true) ?? [];

                if ($tags && !array_intersect($tags, $skillNames)) {
                    if (!$post->category || !in_array($post->category, $skillNames)) {
                        return false;
                    }
                }
            }

            return true;
        });

        // Sort by created_at (most recent first)
        $sortedPosts = $filteredPosts->sortByDesc(function ($post) {
            return $post['created_at'];
        });

        // Paginate the sorted posts
        $perPage = 10;
        $page = $request->input('page', 1);

        $posts = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedPosts->forPage($page, $perPage),
            $sortedPosts->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $skills = Skill::all();
        $count = count($sortedPosts);

        // Select appropriate view
        $view = "work.quick_jobs.index";
        if ($dbType == "FIXED_TERM_JOB") {
            $view = "work.part_time_jobs.index";
        } elseif ($dbType == "PERMANENT_JOB") {
            $view = "work.permanent.index";
        }

        return view($view, compact("posts", "skills", "count"));
    }

    /**
     * Show job details for public users
     *
     * @param string $type - quick-job, fixed-term, permanent, p2p
     * @param string $uuid - job post UUID
     */
    public function show($type, $uuid)
    {
        Log::info("Public job details - type: {$type}, uuid: {$uuid}");

        if (!$uuid) {
            return back()->with("danger", "Invalid request. Kindly try again.");
        }

        $post = Post::where("id", $uuid)->first();
        if (!$post) {
            return back()->with("danger", "Oops...something went wrong. We could not retrieve post details.");
        }

        // Check if post is deleted
        if ($post->deleted_at) {
            return back()->with("info", "This post has been removed by the issuer.");
        }

        // Get user location from session (if set)
        $user_location = session('guest_location', null);
        $user_location_lat = null;
        $user_location_lng = null;

        if ($user_location) {
            $coords = json_decode($user_location);
            $user_location_lat = $coords->latitude ?? null;
            $user_location_lng = $coords->longitude ?? null;
        }

        // Add post details
        $post->user;
        $post["industry"] = $post->industry ? $post->industry->name : null;
        $post["createdOn"] = $post->created_at->diffForHumans();

        // Calculate distance only if user has location
        if ($user_location_lat && $user_location_lng) {
            $post_location_lat = json_decode($post->coords)->latitude ?? explode(',', $post->coords)[0];
            $post_location_lng = json_decode($post->coords)->longitude ?? explode(',', $post->coords)[1];
            $post["distance"] = number_format($this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K"), 2);
        } else {
            $post["distance"] = null;
        }

        // Calculate duration for fixed term jobs
        if ($post->end_date && $post->start_date) {
            $post["duration"] = Carbon::parse($post->end_date)->diffInMonths(Carbon::parse($post->start_date));
        }

        // No "has_already_applied" for guests
        $post->has_already_applied = "no";

        // Get other related posts
        $otherPosts = Post::with(['user', 'industry'])
            ->where("status", "active")
            ->where("type", $post->type)
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->whereNull('deleted_at')
            ->orderByDesc("created_at")
            ->limit(3)
            ->get();

        $filteredPosts = $otherPosts->map(function (Post $otherPost) use ($user_location_lat, $user_location_lng) {
            // Calculate duration if dates are available
            if ($otherPost->end_date && $otherPost->start_date) {
                $otherPost["duration"] = Carbon::parse($otherPost->end_date)->diffInMonths(Carbon::parse($otherPost->start_date));
            }

            // Add additional post data
            $otherPost["organiser_name"] = $otherPost->user->name ?? "Unknown";
            $otherPost["industry"] = $otherPost->industry ? $otherPost->industry->name : null;
            $otherPost["createdOn"] = $otherPost->created_at->diffForHumans();

            // Calculate distance only if user has location
            if ($user_location_lat && $user_location_lng) {
                $post_location_lat = json_decode($otherPost->coords)->latitude ?? explode(',', $otherPost->coords)[0];
                $post_location_lng = json_decode($otherPost->coords)->longitude ?? explode(',', $otherPost->coords)[1];
                $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
                $otherPost["distance"] = number_format($distance, 2);
            } else {
                $otherPost["distance"] = null;
            }

            return $otherPost;
        });

        // Select appropriate view
        $view = "";
        switch ($post->type) {
            case "QUICK_JOB":
                $view = "work.quick_jobs.show";
                break;
            case "FIXED_TERM_JOB":
                $view = "work.part_time_jobs.show";
                break;
            case "PERMANENT_JOB":
                $view = "work.permanent.show";
                break;
            case "P2P":
                $view = "work.quick_jobs.show";
                break;
        }

        return view($view, compact("post", 'filteredPosts'));
    }

    /**
     * Calculate distance between two coordinates
     * (Copied from PostController)
     */
    private function get_distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = (float)$lon1 - (float)$lon2;
        if ($theta == 0) {
            return 0;
        }

        $dist = sin(deg2rad((float)$lat1)) * sin(deg2rad((float)$lat2)) + cos(deg2rad((float)$lat1)) * cos(deg2rad((float)$lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
}
