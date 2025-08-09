<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use App\Http\Responses\ApiResponse;

class HomeController extends Controller
{
    public function homePage()
    {
        $slider = Slider::all();
        return ApiResponse::success([
            'slider' => $slider,
        ]);
    }
}
