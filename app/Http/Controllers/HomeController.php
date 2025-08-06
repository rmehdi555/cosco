<?php

namespace App\Http\Controllers;


use App\Models\Slider;

class HomeController extends Controller
{
    public function homePage()
    {
        $slider = Slider::all();
        return response()->json([
            'slider' => $slider,
        ]);
    }
}
