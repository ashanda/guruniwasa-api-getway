<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class IncomeExpencesController extends Controller
{
    private $ServiceUrl;
    private $apiKey;

    public function __construct()
    {
        $this->ServiceUrl = env('CORE_SERVICE'); // Assign the environment variable to the property
        $this->apiKey = env('API_KEY');
    }

    public function summery(Request $request){
        $methode = 'get';
        $slug = 'income-expense-summery';
        $response = $this->callService($request->all(),$methode,$slug);
        return response()->json($response);
    }

    public function summeryChart(Request $request){
        $methode = 'get';
        $slug = 'income-expense-summery-chart';
        $response = $this->callService($request->all(),$methode,$slug);
        return response()->json($response);
    }



    private function callService($data,$methode,$slug)
    {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->$methode("$this->ServiceUrl/$slug", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ]
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }
}
