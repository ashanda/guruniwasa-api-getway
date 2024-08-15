<?php

namespace App\Http\Controllers;

use App\Traits\HandlesHTTPRequests;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClassNoteController extends Controller
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


    public function teacherNoteList(Request $request){
        try {
            $http = new Client();
            $response = $http->get("$this->CoreServiceUrl/class-notes-list", [
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
}
