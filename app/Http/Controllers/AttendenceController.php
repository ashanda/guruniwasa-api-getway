<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

use function Pest\Laravel\json;

class AttendenceController extends Controller
{
    use HandlesHTTPRequests;
    private $ServiceUrl;
    private $CoreServiceUrl;
    private $apiKey;

    public function __construct()
    {
        $this->ServiceUrl = env('USER_SERVICE'); // Assign the environment variable to the property
        $this->CoreServiceUrl = env('CORE_SERVICE'); // Assign the environment variable to the property
        $this->apiKey = env('API_KEY');
    }
    public function StudentAttendence(Request $request)
{
    
    

    try {
        // Send HTTP requests
         $response_serviceCall = $this->callService($request->all(), $request->bearerToken());

        // Prepare and return the response
        return response()->json($response_serviceCall, 200);

    } catch (\Exception $exception) {
        return response()->json([
            'status' => 400,
            'message' => $exception->getMessage(),
            'data' => [],
        ], 400);
    }
}



    private function callService($data,$accessToken)
    {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->post("$this->ServiceUrl/attendence", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                    'Authorization' => $accessToken ? 'Bearer ' . $accessToken : null,
                    
                ],
                'query' => [
                        'subject_id' => $data['subject_id'],
                         'user_id' => $data['user_id'],
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }
}
