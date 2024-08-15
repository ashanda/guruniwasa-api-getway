<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class TeacherSubjectController extends Controller
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

    public function teacherSubjects(Request $request){
              
        try {
               
                $response_serviceCall = $this->callServiceTeacherSubject($request->all());
                // Filter the lessons based on subject_ids
                // Prepare the response
                $response = [
                    'status' => 200,
                    'message' => 'Current teacher subject retrieved successfully',
                    'data' => [
                        'subject' => $response_serviceCall,
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

public function teacherSubjectsCount(Request $request)
{
    try {
        // Call the service to get the subjects
        $response_serviceCall = $this->callServiceTeacherSubject($request->all());

        // Extract only the teacher subjects from the response
        $teacherSubjects = $response_serviceCall['data']['teacher_subjects'] ?? [];

        // Initialize an array to hold the final response with subjects and student counts
        $subjectWithStudents = [];

        // Iterate over each subject in the response
        foreach ($teacherSubjects as $subject) {
            $subjectID = $subject['id'];

            // Call the service for each subject individually to get the student count
            $response_studentCount = $this->callServiceTeacherSubjectStudentCount($subjectID, $request->bearerToken());

            // Combine the subject data with its student count
            $subject['students'] = [
                'status' => $response_studentCount['status'],
                'message' => $response_studentCount['message'],
                'data' => $response_studentCount['data'],
                'student_count' => $response_studentCount['student_count']
            ];

            // Add the combined subject data to the final response array
            $subjectWithStudents[] = $subject;
        }

        // Prepare the final response
        $response = [
            'status' => 200,
            'message' => 'Current teacher subject retrieved successfully',
            'data' => [
                'subject' => $subjectWithStudents
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




     private function callServiceTeacherSubject($data)
    {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->get("$this->CoreServiceUrl/teacher-subject", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'teacher_id' => $data['teacher_id'],
                        'subject_id' => $data['subjects'],
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }

     private function callServiceTeacherSubjectStudentCount($subjectID,$access_token)
    {
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->get("$this->ServiceUrl/student-subject-with-count", [
                'headers' => [
                    'Authorization' => $access_token ? 'Bearer ' . $access_token : null,
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'subject_id' => $subjectID,
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }

}
