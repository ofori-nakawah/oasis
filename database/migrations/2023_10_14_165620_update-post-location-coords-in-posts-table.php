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
            \Illuminate\Support\Facades\Log::debug("OLD COORDS >>>>>>>>>>>>>>>>> " . $post->coords);
            $latitude = $coordinates[0];
            $longitude = $coordinates[1];
            $post->coords = json_encode([
                "longitude" => $longitude,
                "latitude" => $latitude
            ]);
            \Illuminate\Support\Facades\Log::debug("NEW COORDS >>>>>>>>>>>>>>>>> " . $post->coords);
            $post->update();
        }

        $users = App\Models\User::all();
        foreach ($users as $user) {
            $coordinates = explode(",", $user->location_coords);
            \Illuminate\Support\Facades\Log::debug("OLD COORDS >>>>>>>>>>>>>>>>> " . $user->location_coords);
            $latitude = $coordinates[0];
            $longitude = $coordinates[1];
            $user->location_coords = json_encode([
                "longitude" => $longitude,
                "latitude" => $latitude
            ]);
            \Illuminate\Support\Facades\Log::debug("NEW COORDS >>>>>>>>>>>>>>>>> " . $user->location_coords);
            $user->update();
        }
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
