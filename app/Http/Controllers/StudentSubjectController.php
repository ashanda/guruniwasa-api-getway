<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentSubjectController extends Controller
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

    public function studentSubjectGet(Request $request)
    {
        try {
            $response_serviceCall = $this->callService($request->all());

            // Get previous subjects from the request
            $previous_subjects = $request->current_subjects;

            // Initialize a single array for all subjects with status and teacher data
            $all_subjects = [];

            // Loop through subjects from the response
            foreach ($response_serviceCall['data']['subjects'] as $subject) {
                // Fetch teacher data
                $teacher = $this->callTeahcerdata($subject['tid'], $request->bearerToken());

                // Check if the subject is in previous_subjects
                if (in_array($subject['id'], $previous_subjects)) {
                    // Mark as "already get"
                    $subject['subject_status'] = 'all ready get';
                } else {
                    // Mark as "not get"
                    $subject['subject_status'] = 'not get';
                }

                // Merge the teacher data into the subject array
                $subject['teacher'] = $teacher['data'];

                // Add the subject to the all_subjects array
                $all_subjects[] = $subject;
            }

            // Prepare the final response
            $final_response = [
                'status' => 200,
                'message' => 'subjects categorized successfully',
                'subjects' => $all_subjects, // Single array with all subjects including teacher data
            ];

            // Log and return the response
            return response()->json($final_response, 200);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
       private function callService($data)
    {
        
        // Make a request to auth-service to authenticate and get token
       
        $http = new Client();
        $response = $http->get("$this->CoreServiceUrl/student-subjects-grade-related", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'grade_id' => $data['grade_id'],
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }

    private function callTeahcerdata($data,$access_token)
    {
        
         $http = new Client();
        $response = $http->get("$this->ServiceUrl/get-teacher", [
                'headers' => [
                    'Authorization' => $access_token ? 'Bearer ' . $access_token : null,
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'teacher_id' => $data,
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }


    public function studentSubjectRemove(Request $request){

           try {
                // Create an instance of the HTTP client
                $http = new Client();

                // Send the GET request to the external service
                $response = $http->post("$this->ServiceUrl/student-subject-remove", [
                    'headers' => [
                        'Authorization' => $request->bearerToken() ? 'Bearer ' . $request->bearerToken() : null,
                        'API-Key' => $this->apiKey,
                    ],
                    'query' => [
                        'subject_id' => $request->subject_id,
                        'student_id' => $request->student_id,
                    ],
                ]);

                // Parse the response body (assuming it's JSON)
                $responseBody = json_decode($response->getBody(), true);

                // Return the response as JSON
                return response()->json($responseBody, $response->getStatusCode());

            } catch (Exception $exception) {
                // Handle exceptions and return an error response
                return response()->json(['error' => $exception->getMessage()], 400);
            }
    }

     public function studentSubjectAdd(Request $request){

           try {
                // Create an instance of the HTTP client
                $http = new Client();

                // Send the GET request to the external service
                $response = $http->post("$this->ServiceUrl/student-subject-add", [
                    'headers' => [
                        'Authorization' => $request->bearerToken() ? 'Bearer ' . $request->bearerToken() : null,
                        'API-Key' => $this->apiKey,
                    ],
                    'query' => [
                        'subject_id' => $request->subject_id,
                        'student_id' => $request->student_id,
                    ],
                ]);

                // Parse the response body (assuming it's JSON)
                $responseBody = json_decode($response->getBody(), true);

                // Return the response as JSON
                return response()->json($responseBody, $response->getStatusCode());

            } catch (Exception $exception) {
                // Handle exceptions and return an error response
                return response()->json(['error' => $exception->getMessage()], 400);
            }
    }

}
