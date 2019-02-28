<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimeTracerController extends HomeController
{
    public function get_time_tracer() {
        return view('backend.time_tracer');
    }
}
