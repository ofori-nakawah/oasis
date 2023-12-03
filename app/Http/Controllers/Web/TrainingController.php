<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index()
    {
        return view("training.index");
    }
}
