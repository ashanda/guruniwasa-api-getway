<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use App\Traits\S3UploadTrait;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClassNoteController extends Controller
{
    use HandlesHTTPRequests;
    use S3UploadTrait;
    private $ServiceUrl;
    private $CoreServiceUrl;
    private $apiKey;

    public function __construct()
    {
        $this->ServiceUrl = env('USER_SERVICE'); // Assign the environment variable to the property
         $this->CoreServiceUrl = env('CORE_SERVICE'); // Assign the environment variable to the property
        $this->apiKey = env('API_KEY');
    }


    public function teacherNoteList(Request $request){
        try {
            $http = new Client();
            $response = $http->get("$this->CoreServiceUrl/class-notes-list-teacher", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                    'subject_id' => $request->subject_id,
                    'teacher_id' => $request->teacher_id,
                ],
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
                'data' => [],
            ], 400);
        }
    }

    public function studentNoteCount(Request $request){

        try {
            $http = new Client();
            $response = $http->get("$this->CoreServiceUrl/class-notes-count", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                    'subject_id' => $request->subject_id,
                    'teacher_id' => $request->teacher_id,
                ],
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
                'data' => [],
            ], 400);
        }
    }
    public function teacherNoteStore(Request $request){
        try {
            Log::info($request->all());
            $http = new Client();
            $response = $http->post("$this->CoreServiceUrl/class-notes-store", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'form_params' => [
                    'subject_id' => $request->input('subject_id'),
                    'teacher_id' => $request->input('teacher_id'),
                    'title' => $request->input('title'),
                ],
            ]);
            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
                'data' => [],
            ], 400);
        }
    }


    public function teacherNoteUpdate(Request $request){
        try {
            $http = new Client();
            $id = $request->input('id');

            $response = $http->put("$this->CoreServiceUrl/class-notes-update/{$id}", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'form_params' => [
                    'title' => $request->input('title'),
                ],
            ]);
            
            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
                'data' => [],
            ], 400);
        }

    }


    public function teacherNoteDestroy(Request $request){
        try {
            $http = new Client();
            Log::alert($request->input('id'));
            $response = $http->delete("$this->CoreServiceUrl/class-notes-destroy/{$request->input('id')}", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
            ]);
            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
                'data' => [],
            ], 400);
        }
    }


    public function studentNoteList(Request $request){
        try {
            $http = new Client();
            $response = $http->get("$this->CoreServiceUrl/class-notes-list", [
                'headers' => [
                    'API-Key' => $this->apiKey,
                ],
                'query' => [
                    'subject_id' => $request->subject_id,
                    'student_id' => $request->student_id,
                ],
            ]);
            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
                'data' => [],
            ], 400);
        }
    }

    public function studentNotePaperUpload(Request $request){
         Log::info($request->all());
            // First, upload the file using the uploadservice method
            $foldername = 'class_note_papers'; // Example folder name where files will be stored
            $filePathResponse = $this->uploadservice($request, $foldername);
           
           
            // Check if the file upload was successful
            

            // Get the uploaded file path from the response
            $filePath = json_decode($filePathResponse->getContent(), true)['path'];
            
            try {
            // Send HTTP requests
                $response_serviceCall = $this->classTuteUpload($request, $filePath);
                
                return response()->json($response_serviceCall, 200);
            }catch (Exception $exception) {
                return response()->json([
                    'status' => 400,
                    'message' => $exception->getMessage(),
                    'data' => [],
                ], 400);
            }
    }

        private function classTuteUpload($data, $filePath)
        {
            // Make a request to auth-service to authenticate and get token

            $http = new Client();
                $response = $http->post("$this->CoreServiceUrl/class-notes-upload", [
                    'headers' => [
                        'API-Key' => $this->apiKey,
                    ],
                    'json' => [
                        'note_id' => $data['note_id'],
                        'student_id' => $data['student_id'] ,
                        'teacher_id' => $data['teacher_id'],
                        'subject_id' => $data['subject_id'],
                        'grade_id' => $data['grade_id'],
                        'directory' => $filePath,
                    ],    
                ]);
                
            return json_decode((string) $response->getBody(), true);
        } 

       private function uploadservice($data, $foldername)
        {
            
            // Use the Validator facade to perform validation
            $validator = Validator::make($data->all(), [
                'document' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
                'old_file_path' => 'nullable|string',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Proceed with file handling if validation passes
            $file = $data->file('document');
            $oldFilePath = $data->input('old_file_path');

            // Use the trait method to upload the new file and delete the old one
            $filePath = $this->uploadToS3($file, $foldername, $oldFilePath);

            if ($filePath) {
                return response()->json(['path' => $filePath], 200);
            }

            return response()->json(['error' => 'File upload failed.'], 500);
        }
}
