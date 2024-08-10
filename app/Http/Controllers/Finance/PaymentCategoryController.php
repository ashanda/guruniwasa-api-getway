<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Traits\HandlesHTTPRequests;
use Exception;
use Illuminate\Http\Request;

class PaymentCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
            $response = $this->sendHttpRequest('GET', "$this->CoreServiceUrl/payment-category", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Payment Categories Listed.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $response = $this->sendHttpRequest('POST', "$this->CoreServiceUrl/payment-category", $request->bearerToken(), $request->all());
            return response()->json(['status' => 'success', 'message' => 'Payment Category API End Point Calling Success', 'data' => $response], 201);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function show($id)
    {
        try {
            $response = $this->sendHttpRequest('GET', "$this->CoreServiceUrl/payment-category/$id", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Payment Category API End Point Calling Success', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function edit($id)
    {
        try {
            $response = $this->sendHttpRequest('GET', "$this->CoreServiceUrl/payment-category/$id/edit", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Payment Category API End Point Calling Success.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function update(Request $request, $id)
    {
        
        try {
            $response = $this->sendHttpRequest('PUT', "$this->CoreServiceUrl/payment-category/$id", $request->bearerToken(), $request->all());
            return response()->json(['status' => 'success', 'message' => 'Payment Category API End Point Calling Success.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $response = $this->sendHttpRequest('DELETE', "$this->CoreServiceUrl/payment-category/$id", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Payment Category API End Point Calling Success.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }

    public function restore($id)
    {
        try {
            $response = $this->sendHttpRequest('POST', "$this->CoreServiceUrl/payment-category/$id/restore", request()->bearerToken());
            return response()->json(['status' => 'success', 'message' => 'Payment Category API End Point Calling Success.', 'data' => $response], 200);
        } catch (Exception $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage(), 'data' => []], 400);
        }
    }
}
