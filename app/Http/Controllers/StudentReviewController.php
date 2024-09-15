<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class StudentReviewController extends Controller
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


    public function studentReviewTeacher(Request $request){
        try {
            $all_subjects = [];
            $teacher_ids = [];

            // Loop through each subject_id within subject_ids
            foreach ($request->subject_id as $subject_id) {
                // Fetch teacher data using the subject_id
                $teacher = $this->callTeahcerReview($subject_id, $request->bearerToken());

                // Extract teacher_id from the teacher data
                $teacher_id = $teacher['data'][0]['teacher']['id'];

                // Check if this teacher_id has already been added to the array
                if (!in_array($teacher_id, $teacher_ids)) {
                    // If not, add the teacher_id to the list of processed teacher_ids
                    $teacher_ids[] = $teacher_id;

                    // Prepare the subject data, including teacher information
                    $subject_data = [
                        'review' => $teacher,
                    ];

                    // Add the prepared subject data to the all_subjects array
                    $all_subjects[] = $subject_data;
                }
            }

            // Prepare the final response
            $final_response = [
                'status' => 200,
                'message' => 'Subjects categorized successfully',
                'subjects' => $all_subjects, // Single array with all subjects including teacher data
            ];

            // Return the response as JSON
            return response()->json($final_response, 200);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }

    }

   
    private function callTeahcerReview($data,$access_token)
    {
        
         $http = new Client();
        $response = $http->get("$this->ServiceUrl/teacher-review", [
                'headers' => [
                    'Authorization' => $access_token ? 'Bearer ' . $access_token : null,
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                        'subject_id' => $data,
                ],
        ]);
       
        return json_decode((string) $response->getBody(), true);
    }
}
