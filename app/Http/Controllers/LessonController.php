<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\HandlesHTTPRequests;
use Exception;
use GuzzleHttp\Client;

class LessonController extends Controller
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

    public function liveLessons(Request $request)
    {
        
        try {
    // Send HTTP requests
    $response_userSubjects = $this->sendHttpRequest('GET', "$this->ServiceUrl/live-lesson", $request->bearerToken());
    $response_serviceCall = $this->callService($request->all());

    // Decode the subject_ids from userSubjects
    $subject_ids = json_decode($response_userSubjects['data'][0]['subject_ids'], true);

    // Filter the lessons based on subject_ids
    $filteredLessons = array_filter($response_serviceCall['data']['lessons'], function ($lesson) use ($subject_ids) {
        return in_array($lesson['sid'], $subject_ids);
    });

    // Prepare the response
    $response = [
        'status' => 200,
        'message' => 'Filtered lessons retrieved successfully',
        'data' => [
            'current_lessons' => array_values($filteredLessons),
            'sids' => $subject_ids,
        ],
    ];

    return response()->json($response, 200);

} catch (Exception $exception) {
    return response()->json([
        'status' => 400,
        'message' => $exception->getMessage(),
        'data' => [],
    ], 400);
}
    }


    private function callService($data)
    {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->get("$this->CoreServiceUrl/live-lesson", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'grade' => $data['grade'],
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }
}
