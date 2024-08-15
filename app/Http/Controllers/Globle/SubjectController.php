<?php

namespace App\Http\Controllers\Globle;
use App\Http\Controllers\Controller;
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


    public function show($id){
        $http = new Client();
        $response = $http->get("$this->ServiceUrl/subjects/$id", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ], 
        ]);
       
        return json_decode((string) $response->getBody(), true);
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
                        'classType'=> $data['classType'],
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }
}
