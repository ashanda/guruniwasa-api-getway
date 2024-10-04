<?php

namespace App\Http\Controllers\Globle;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class GradeController extends Controller
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
        try {
            $method = 'get';
            $slug = 'grades';
            $response = $this->callService($request->all(), $method, $slug);
            return response()->json($response);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    public function create()
    {
        try {
            return response()->json(['message' => 'Create form loaded.'], 200);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'gname' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $method = 'post';
            $slug = 'store_grade';
            $response = $this->callService($request->all(), $method, $slug);
            return response()->json($response, 200);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    public function show($grade)
    {
        try {
            $method = 'get';
            $slug = "grades/{$grade}";
            $response = $this->callService([], $method, $slug);
            return response()->json($response, 200);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    public function edit($grade)
    {
        try {
            $method = 'get';
            $slug = "grades/{$grade}/edit";
            $response = $this->callService([], $method, $slug);
            return response()->json($response, 200);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    public function update(Request $request)
    {
 
        try {
            $validator = Validator::make($request->all(), [
                'grade_id' => 'required',
                'gname' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $method = 'post';
            $slug = "update_grade";
            $response = $this->callService($request->all(), $method, $slug);
            return response()->json($response, 200);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $method = 'post';
            $slug = "remove_grade";
            $response = $this->callService($request->all(), $method, $slug);
            return response()->json($response, 200);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    private function callService($data, $method, $slug)
    {
        try {
            $http = new Client();
            $response = $http->$method("{$this->ServiceUrl}/{$slug}", [
                'headers' => ['API-Key' => $this->apiKey],
                'json' => $data
            ]);
            return json_decode((string) $response->getBody(), true);
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
