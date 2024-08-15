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
    if (isset($response_serviceCall['data']['lessons']) && is_array($response_serviceCall['data']['lessons'])) {
    // Filter the lessons based on subject_ids
    $filteredLessons = array_filter($response_serviceCall['data']['lessons'], function ($lesson) use ($subject_ids) {
        // Ensure 'sid' key exists in each lesson
        return isset($lesson['subject_id']) && in_array($lesson['subject_id'], $subject_ids);
    });
    } else {
    // Handle the case where 'lessons' key is not set or not an array
    return response()->json([
        'status' => 400,
        'message' => 'Lessons data is not available or not in the expected format',
        'data' => []
    ]);
}
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
       
        $responseData = json_decode((string) $response->getBody(), true);
        
        // Check if the response data is valid
        if (isset($responseData['data']['lessons'])) {
        foreach ($responseData['data']['lessons'] as &$record) {
            $teacherId = $record['teacher_id'];
            $gradeIDd = $record['grade_id'];
            // Make a request to get the teacher's name using the teacher ID
            $responseTeacher = $this->sendHttpRequest('GET', "$this->ServiceUrl/teacher/data/$teacherId", $this->apiKey);
            $responseGrade = $this->sendHttpRequest('GET', "$this->CoreServiceUrl/grades/$gradeIDd", $this->apiKey);
            // Assuming sendHttpRequest returns an array, no need to call getBody()
            // If it already returns decoded JSON data
            if (isset($responseTeacher['data']['name'])) {
                // Bind the teacher's name to the video record
                $record['teacher_name'] = $responseTeacher['data']['name'];
            } else {
                // If the name is not found, bind a default value
                $record['teacher_name'] = 'Unknown';
            }

            if (isset($responseGrade['data']['gname'])) {
                // Bind the teacher's name to the video record
                $record['grade'] = $responseGrade['data']['gname'];
            } else {
                // If the name is not found, bind a default value
                $record['grade'] = 'Unknown';
            }
        }
    }

    return $responseData;
    }



    public function teacherliveLessons(Request $request)
    {
        
        try {
    // Send HTTP requests
    $response_userSubjects = $this->sendHttpRequest('GET', "$this->ServiceUrl/live-lesson-teacher", $request->bearerToken());
    
    $response_serviceCall = $this->callTeacherService($request->all());

    // Decode the subject_ids from userSubjects
    $subject_ids = json_decode($response_userSubjects['data'][0]['subject_ids'], true);

    // Filter the lessons based on subject_ids
    if (isset($response_serviceCall['data']['lessons']) && is_array($response_serviceCall['data']['lessons'])) {
    // Filter the lessons based on subject_ids
    $filteredLessons = array_filter($response_serviceCall['data']['lessons'], function ($lesson) use ($subject_ids) {
        // Ensure 'sid' key exists in each lesson
        return isset($lesson['subject_id']) && in_array($lesson['subject_id'], $subject_ids);
    });
    } else {
    // Handle the case where 'lessons' key is not set or not an array
    return response()->json([
        'status' => 400,
        'message' => 'Lessons data is not available or not in the expected format',
        'data' => []
    ]);
}
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


    private function callTeacherService($data)
    {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->get("$this->CoreServiceUrl/teacher-live-lesson", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'teacher_id' => $data['teacher_id'],
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }


    public function liveLessonsshow(Request $request){
             try {
                    // Send HTTP requests
                    $response_userSubjects = $this->sendHttpRequest('GET', "$this->ServiceUrl/live-lesson-teacher", $request->bearerToken());
                    
                    $response_serviceCall = $this->lessonShow($request->all());

                    // Decode the subject_ids from userSubjects
                    $subject_ids = json_decode($response_userSubjects['data'][0]['subject_ids'], true);

                    // Filter the lessons based on subject_ids
                    if (isset($response_serviceCall['data']['lessons']) && is_array($response_serviceCall['data']['lessons'])) {
                    // Filter the lessons based on subject_ids
                    $filteredLessons = array_filter($response_serviceCall['data']['lessons'], function ($lesson) use ($subject_ids) {
                        // Ensure 'sid' key exists in each lesson
                        return isset($lesson['subject_id']) && in_array($lesson['subject_id'], $subject_ids);
                    });
                    } else {
                    // Handle the case where 'lessons' key is not set or not an array
                    return response()->json([
                        'status' => 400,
                        'message' => 'Lessons data is not available or not in the expected format',
                        'data' => []
                    ]);
                }
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


    private function lessonShow($data)
    {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->get("$this->CoreServiceUrl/teacher-live-lesson-show", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'teacher_id' => $data['teacher_id'],
                        'lesson_id' => $data['lesson_id'],
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }

    public function liveLessonsUpdate(Request $request){

        try {
            // Send HTTP requests
            $response_serviceCall = $this->lessonUpdate($request->all());
            return response()->json($response_serviceCall, 200);
        }catch (Exception $exception) {
            return response()->json([
                'status' => 400,
                'message' => $exception->getMessage(),
                'data' => [],
            ], 400);
        }

        }

       private function lessonUpdate($data)
    {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->post("$this->CoreServiceUrl/teacher-live-lesson-update", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'lesson_id' => $data['lesson_id'],
                        'status' => $data['status'],
                        'zoom_link' => $data['zoom_link'],
                        'password' => $data['zoom_password'],
                        'special_note' => $data['special_note'],

                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    } 
    
}
