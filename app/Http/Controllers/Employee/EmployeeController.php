<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\AuthToken;
use App\Traits\HandlesHTTPRequests;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
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

    public function index()
    {
        try {
            $response = $this->sendHttpRequest('GET', "$this->ServiceUrl/staff", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Employees Listed.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function show($id)
    {
        try {
            $response = $this->sendHttpRequest('GET', "$this->ServiceUrl/staff/$id", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Employee Details.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $response = $this->sendHttpRequest('POST', "$this->ServiceUrl/staff", $request->bearerToken(), $request->all());
            return response()->json(['status' => 'success', 'message' => 'Employee Created.', 'data' => $response], 201);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $response = $this->sendHttpRequest('PUT', "$this->ServiceUrl/staff/$id", $request->bearerToken(), $request->all());
            return response()->json(['status' => 'success', 'message' => 'Employee Updated.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $response = $this->sendHttpRequest('DELETE', "$this->ServiceUrl/staff/$id", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Employee Deleted.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function Staffregister(Request $request)
    {
        $tag = 'student-register';
        $response = $this->callRegisterService($request->all(), $tag);

        if ($response['status'] == 200) {
            // Save the token in the gateway's database
            $this->saveToken($response['data']['access_token'], $response['data']['expires_at']);
        }

        return response()->json($response);
    }

    private function callRegisterService($data, $tag)
    {
        Log::info($data);
        $http = new Client;
        $response = $http->post("$this->ServiceUrl/$tag", [
            'headers' => [
                'API-Key' => $this->apiKey,
            ],
            'form_params' => [
                'username' => $data['username'],
                'subject' => $data['subject_list'],
                'password' => $data['password'],
                'full_name' => $data['full_name'],
                'student_code' => $data['student_code'],
                'birthday' => $data['birthday'],
                'gender' => $data['gender'],
                'address' => $data['address'],
                'school' => $data['school'],
                'district' => $data['district'],
                'city' => $data['city'],
                'parent_phone' => $data['parent_phone'],
                'grade' => $data['grade'],
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    // private function callAuthService($data, $tag)
    // {
    //     $http = new Client();
    //     $response = $http->post("$this->ServiceUrl/$tag", [
    //         'headers' => [
    //             'API-Key' => $this->apiKey,
    //         ],
    //         'form_params' => [
    //             'email' => $data['username'],
    //             'password' => $data['password'],
    //         ],
    //     ]);

    //     return json_decode((string) $response->getBody(), true);
    // }

    private function saveToken($accessToken, $expiresAt)
    {
        AuthToken::create([
            'access_token' => $accessToken,
            'expires_at' => $expiresAt,
        ]);
    }
}
