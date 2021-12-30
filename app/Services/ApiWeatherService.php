<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

trait ApiWeatherService
{
    public function requestApiWeather($city): \Illuminate\Http\Client\Response
    {
         return Http::get(env('API_WEATHER_BASE_URL')."{$city}&appid=".env('API_WEATHER_ID'));
    }


}
