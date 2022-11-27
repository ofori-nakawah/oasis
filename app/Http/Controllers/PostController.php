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

    public function get_user_posts(Request $request)
    {
        $posts = auth()->user()->posts;
        return $this->success_response($posts, "Posts fetched successfully.");
    }
}
