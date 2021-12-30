<?php

namespace App\Http\Controllers\System;

use App\Helpers\ApiWeatherHelper;
use App\Helpers\AppConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\CheckWeatherRequest;
use App\Services\ApiWeatherService;

use Illuminate\Http\Request;

class CheckWeatherController extends Controller
{
    use ApiWeatherService;

    public function __invoke(CheckWeatherRequest $request): \Illuminate\Http\JsonResponse
    {

        $httpResponse = $this->requestApiWeather($request->city_name);


        /***** check estatus Request *******/
       if($httpResponse->status() == 200){

         // return response()->json($httpResponse->json());
           $apiWeatherProcess = new ApiWeatherHelper($httpResponse, $request);

           return response()->json([
               'check' =>  $apiWeatherProcess->criteriaSameValues(),
               'criteria' => ['naming' => $apiWeatherProcess->oddNumberOfLetters(),
                   'daytemp' => $apiWeatherProcess->calculateDayTemp(),
                   'rival' => $apiWeatherProcess->getInfoRival()]
           ]);
       }

        return response()->json($httpResponse->json('message'), 404);
    }
}
