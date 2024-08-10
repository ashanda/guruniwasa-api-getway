<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Traits\HandlesHTTPRequests;
use Exception;
use Illuminate\Http\Request;

class IncomeTaxController extends Controller
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
            $response = $this->sendHttpRequest('GET', "$this->CoreServiceUrl/income-tax", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Income Tax Payments Listed.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function create()
    {
        try {
            $response = $this->sendHttpRequest('GET', "$this->CoreServiceUrl/income-tax/create", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Create Income Tax Payment Form Data.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $response = $this->sendHttpRequest('POST', "$this->CoreServiceUrl/income-tax", $request->bearerToken(), $request->all());
            return response()->json(['status' => 'success', 'message' => 'Income Tax Payment Created.', 'data' => $response], 201);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function show($id)
    {
        try {
            $response = $this->sendHttpRequest('GET', "$this->CoreServiceUrl/income-tax/$id", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Income Tax Payment Details.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function edit($id)
    {
        try {
            $response = $this->sendHttpRequest('GET', "$this->CoreServiceUrl/income-tax/$id/edit", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Edit Income Tax Payment Form Data.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $response = $this->sendHttpRequest('PUT', "$this->CoreServiceUrl/income-tax/$id", $request->bearerToken(), $request->all());
            return response()->json(['status' => 'success', 'message' => 'Income Tax Payment Updated.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $response = $this->sendHttpRequest('DELETE', "$this->CoreServiceUrl/income-tax/$id", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Income Tax Payment Deleted.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function restore($id)
    {
        try {
            $response = $this->sendHttpRequest('POST', "$this->CoreServiceUrl/income-tax/$id/restore", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Income Tax Payment Restored.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }
}
