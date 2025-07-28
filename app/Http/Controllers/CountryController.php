<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Http\Resources\CountryResource;
use App\Http\Resources\ProvinceResource;
use App\Http\Resources\CityResource;

class CountryController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/countries/tree",
     *   summary="Get all countries with their provinces and cities",
     *   tags={"Country"},
     *   @OA\Response(
     *     response=200,
     *     description="List of countries with provinces and cities",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CountryResource"))
     *   )
     * )
     */
    public function tree()
    {
        $countries = Country::with(['provinces.cities'])->get();
        return CountryResource::collection($countries);
    }
} 