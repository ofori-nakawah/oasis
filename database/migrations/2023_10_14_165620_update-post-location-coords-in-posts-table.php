<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePostLocationCoordsInPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $posts = \App\Models\Post::all();
        \Illuminate\Support\Facades\Log::debug(">>>>>>>>>>>>>>>>>>>> LOCATION COORDINATES UPDATE FOR POSTS >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>");
        foreach ($posts as $post) {
            $coordinates = explode(",", $post->coords);
            \Illuminate\Support\Facades\Log::debug(json_encode($coordinates));
//            $latitude = explode(',', $post->coords)[0];
//            $longitude = explode(',', $post->coords)[1];
//            $post->coords = json_encode([
//                "longitude" => $longitude,
//                "latitude" => $latitude
//            ]);
//            $post->update();
        }

//        $users = App\Models\User::all();
//        foreach ($users as $user) {
//            $latitude = explode(',', $post->location_coords)[0];
//            $longitude = explode(',', $post->location_coords)[1];
//            $user->location_coords = json_encode([
//                "longitude" => $longitude,
//                "latitude" => $latitude
//            ]);
//            $user->update();
//        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            //
        });
    }
}
