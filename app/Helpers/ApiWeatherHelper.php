<?php

namespace App\Helpers;

use App\Services\ApiWeatherService;
use Carbon\Carbon;
use Illuminate\Support\Str;


class ApiWeatherHelper
{
    use ApiWeatherService;

    private $httpResponse;
    private $request;


    /**
     * @param $httpResponse
     * @param $request
     */
    public function __construct($httpResponse, $request)
    {
        $this->httpResponse = $httpResponse;
        $this->request = $request;
    }

    /**
     * @param $httpResponse
     * @return float
     */
    public function convertKelvinInCelcius(): float
    {
        return $this->httpResponse->json('main.temp') - AppConstants::KELVIN;
    }

    /**
     * @return array
     */
    public function sunriseAndSunsetTime(): array
    {

        return ['sunrise' => Carbon::createFromTimestamp($this->httpResponse->json('sys.sunrise'))->format('H:i'),
            'sunset' => Carbon::createFromTimestamp($this->httpResponse->json('sys.sunset'))->format('H:i')
            ];
    }

    /**
     * @return false|string
     */
    public function getCurrentHour()
    {
        return gmdate("H:i", time() + $this->httpResponse->json('timezone'));
    }

    /**
     * @return bool
     */
    public function oddNumberOfLetters(): bool
    {
         return (bool)((mb_strlen(str_replace(' ', '', $this->request->city_name))) % 2) != 0;
    }

    /**
     * @return bool
     */
    public function getInfoRival(): bool
    {
        $httpResponseRival = $this->requestApiWeather(AppConstants::RIVAL);

        return (bool)($this->convertKelvinInCelcius() > ($httpResponseRival->json('main.temp') - AppConstants::KELVIN));

    }

    public function calculateDayTemp(): bool
    {
        $time = time() + $this->httpResponse->json('timezone');
        $sunrise = $this->httpResponse->json('sys.sunrise');
        $sunset = $this->httpResponse->json('sys.sunset');


        if(($time <= $sunrise && $time >= $sunset) &&
            ($this->convertKelvinInCelcius() >= 10 && $this->convertKelvinInCelcius() <= 15)){
            return true;
        }elseif (($time >= $sunrise && $time <= $sunset) &&
            ($this->convertKelvinInCelcius() >= 17 && $this->convertKelvinInCelcius() <= 25)){
            return true;
        }else{
            return false;
        }

    }

    /**
     * @return bool
     */
    public function criteriaSameValues(): bool
    {
        $values =  [$this->oddNumberOfLetters(), $this->calculateDayTemp(), $this->getInfoRival()];

        return count(array_unique($values)) === 1;
    }
}
