<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;


class UserController extends Controller
{
    public function index()
    {
        $client = new Client();
        $response = $client->get('http://user-service/api/users');
        return $response->getBody();
    }
}
