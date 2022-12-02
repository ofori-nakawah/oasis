<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Traits\Responses;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    use Responses;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * create a volunteer post
     */
    public function create_volunteer_post(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'date' => 'required',
            'time' => 'required',
            'location' => 'required',
            'coords' => 'required',
            'maximum_number_of_volunteers' => 'required',
            'volunteer_hours' => 'required',
            'other_relevant_information' => 'required'
        ]);

        if ($validation->fails()) {return $this->data_validation_error_response($validation->errors());}

        $post = new Post();
        $post->name = $request->name;
        $post->description = $request->description;
        $post->date = $request->date;
        $post->time = $request->time;
        $post->location = $request->location;
        $post->coords = $request->coords;
        $post->maximum_number_of_volunteers = $request->maximum_number_of_volunteers;
        $post->volunteer_hours = $request->volunteer_hours;
        $post->other_relevant_information = $request->other_relevant_information;
        $post->user_id = auth()->id();
        $post->type = "VOLUNTEER";

        try {
            $post->save();
            return $this->success_response($post, "Post has been published successfully.");
        } catch (QueryException $e) {
            Log::error("ERROR SAVING USER >>>>>>>>>>>>>>>>>>>>>>>> " . $e);
            return $this->db_operation_error_response([]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Get posts created by user
     */
    public function get_user_posts(Request $request)
    {
        $posts = auth()->user()->posts;
        return $this->success_response($posts, "Posts fetched successfully.");
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Get posts based on user location and category
     */
    public function get_posts_closest_to_me(Request $request)
    {
        $posts = [];

        //get user coordinates
        $user_location = auth()->user()->location;
        $user_location_lat = explode(',', $user_location)[0];
        $user_location_lng = explode(',', $user_location)[1];

        switch ($request->type) {
            case "VOLUNTEER":
                $posts = Post::where("user_id", "!=", auth()->id())->where("status", "active")->where("type", $request->type)->get();
                break;
        }

        $posts->map(function ($post) use ($user_location_lat, $user_location_lng) {
            //get post coordinates
            $post_location_lat = explode(',', $post->coords)[0];
            $post_location_lng = explode(',', $post->coords)[1];

            $distance = $this->get_distance($user_location_lat, $user_location_lng, $post_location_lat, $post_location_lng, "K");
            $post["organiser_name"] = $post->user->name;
            $post["distance"] = $distance;
            return $post;
        });

        return $this->success_response($posts, "Posts fetched successfully.");
    }

    private function get_distance($lat1, $lon1, $lat2, $lon2, $unit) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
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
