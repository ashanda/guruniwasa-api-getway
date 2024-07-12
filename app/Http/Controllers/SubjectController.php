<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
        private $ServiceUrl;
        private $apiKey;

    public function __construct()
    {
        $this->ServiceUrl = env('CORE_SERVICE'); // Assign the environment variable to the property
        $this->apiKey = env('API_KEY');
    }    
    public function index(Request $request)
    {
         $response = $this->callService($request->all());
         return response()->json($response);
    }


    private function callService($data)
    {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->get("$this->ServiceUrl/subjects", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'grade' => $data['gid'],
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }
}
