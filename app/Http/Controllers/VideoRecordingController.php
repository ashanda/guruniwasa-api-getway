<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class VideoRecordingController extends Controller
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

        public function videoRecordings(Request $request)
    {
        
        try {
    // Send HTTP requests
    $response_userSubjects = $this->sendHttpRequest('GET', "$this->ServiceUrl/live-lesson", $request->bearerToken());
    $response_serviceCall = $this->callService($request->all());
    
    // Decode the subject_ids from userSubjects
    $subject_ids = json_decode($response_userSubjects['data'][0]['subject_ids'], true);

    if (isset($response_serviceCall['data']['video_records']) && is_array($response_serviceCall['data']['video_records'])) {
    // Filter the lessons based on subject_ids
    $filteredVideo = array_filter($response_serviceCall['data']['video_records'], function ($lesson) use ($subject_ids) {
        // Ensure 'sid' key exists in each lesson
        return isset($lesson['subject_id']) && in_array($lesson['subject_id'], $subject_ids);
    });
     $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        // Get the month name
    $monthName = isset($request->month) ? $months[$request->month] : null;
    } else {
    // Handle the case where 'lessons' key is not set or not an array
    return response()->json([
        'status' => 400,
        'message' => 'Video Records is not available or not in the expected format',
        'data' => []
    ]);
}
    // Filter the lessons based on subject_ids
   

    // Prepare the response
    $response = [
        'status' => 200,
        'message' => 'Filtered Video Records retrieved successfully',
        'data' => [
            'video_records' => array_values($filteredVideo),
            'month' => $monthName,
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
        $response = $http->get("$this->CoreServiceUrl/video-recordings", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'grade' => $data['grade'],
                        'month' => $data['month'],
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }

    public function videoRecordingsTeacher(Request $request)
    {
        
        try {
    // Send HTTP requests
    $response_userSubjects = $this->sendHttpRequest('GET', "$this->ServiceUrl/live-lesson-teacher", $request->bearerToken());
    $response_serviceCall = $this->callServiceVideo($request->all());
    
    // Decode the subject_ids from userSubjects
    $subject_ids = json_decode($response_userSubjects['data'][0]['subject_ids'], true);

    if (isset($response_serviceCall['data']['video_records']) && is_array($response_serviceCall['data']['video_records'])) {
    // Filter the lessons based on subject_ids
    $filteredVideo = array_filter($response_serviceCall['data']['video_records'], function ($lesson) use ($subject_ids) {
        // Ensure 'sid' key exists in each lesson
        return isset($lesson['subject_id']) && in_array($lesson['subject_id'], $subject_ids);
    });
     $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        // Get the month name
    $monthName = isset($request->month) ? $months[$request->month] : null;
    } else {
    // Handle the case where 'lessons' key is not set or not an array
    return response()->json([
        'status' => 400,
        'message' => 'Video Records is not available or not in the expected format',
        'data' => []
    ]);
}
    // Filter the lessons based on subject_ids
   

    // Prepare the response
    $response = [
        'status' => 200,
        'message' => 'Filtered Video Records retrieved successfully',
        'data' => [
            'video_records' => array_values($filteredVideo),
            'month' => $monthName,
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


    private function callServiceVideo($data)
    {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->get("$this->CoreServiceUrl/video-recordings-teacher", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'teacher_id' => $data['teacher_id'],
                        'month' => $data['month'],
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }

    


    public function videoRecordingsUpdate(Request $request){

        try {
            // Send HTTP requests
            $response_serviceCall = $this->videoUpdate($request->all());
            return response()->json($response_serviceCall, 200);
        }catch (Exception $exception) {
            return response()->json([
                'status' => 400,
                'message' => $exception->getMessage(),
                'data' => [],
            ], 400);
        }

        }

       private function videoUpdate($data)
     {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
            $response = $http->put("$this->CoreServiceUrl/video-recordings/{$data['id']}", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'form_params' => [
                    'lesson_title' => $data['lesson_title'],
                    'video_url1' => isset($data['video_url1']) ? $data['video_url1'] : null,
                    'video_url2' => isset($data['video_url2']) ? $data['video_url2'] : null,
                ],    
            ]);
       
        return json_decode((string) $response->getBody(), true);
    } 

    

}

