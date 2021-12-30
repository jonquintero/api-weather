<?php

namespace Tests\Feature;

use App\Helpers\ApiWeatherHelper;
use App\Http\Requests\System\CheckWeatherRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ExampleTest extends TestCase
{

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->rules     = (new CheckWeatherRequest())->rules();
        $this->validator = $this->app['validator'];
        $this->request = new Request([
               'city_name'   => 'caracas',
         ]);
        $this->url = "http://api.openweathermap.org/data/2.5/weather?q={$this->request->city_name}&appid=8ca1bf554fe26dff41d635d4e2f866ed";
    }


    public function test_input_city_name_required()
    {
        $response = $this->postJson('api/check', ['city_name' => '']);
        $response->assertStatus(422)
            ->assertJsonFragment([
                     'city_name' => ['The city name field is required.'],
                 ]);

    }

    public function test_validate_city_name_does_not_contain_number()
    {
        $this->withoutExceptionHandling();

        $attributes = ['city_name' => 'caracas4'];
        $request = new CheckWeatherRequest();
        $rules = $request->rules();
        $validator = Validator::make($attributes, $rules);
        $fails = $validator->fails();

        $this->assertEquals(true, $fails);
    }

    public function test_connection_api_weather()
    {
        Http::fake();

        Http::post($this->url);

        Http::assertSent(function ($request) {
            return  $request->url() == $this->url;

        });

    }

    public function test_criteria_same_values()
    {
        $response = Http::post($this->url);

        $apiWeatherProcess = new ApiWeatherHelper($response, $this->request);

        $this->assertIsBool($apiWeatherProcess->criteriaSameValues(), 'Is a boolean value');

    }

    public function test_odd_number_of_letters()
    {
        $response = Http::post($this->url);

        $apiWeatherProcess = new ApiWeatherHelper($response, $this->request);

        $this->assertIsBool($apiWeatherProcess->oddNumberOfLetters(), 'Is a boolean value');

    }

    public function test_calculate_day_temp()
    {
        $response = Http::post($this->url);

        $apiWeatherProcess = new ApiWeatherHelper($response, $this->request);

        $this->assertIsBool($apiWeatherProcess->calculateDayTemp(), 'Is a boolean value');

    }

    public function test_get_info_rival()
    {
        $response = Http::post($this->url);

        $apiWeatherProcess = new ApiWeatherHelper($response, $this->request);

        $this->assertIsBool($apiWeatherProcess->getInfoRival(), 'Is a boolean value');

    }

}
