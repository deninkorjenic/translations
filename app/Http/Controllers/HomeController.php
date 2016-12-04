<?php

/*
 * Taken from
 * https://github.com/laravel/framework/blob/5.3/src/Illuminate/Auth/Console/stubs/make/controllers/HomeController.stub
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index(Request $request)
    {  
        $client = new Client([
            'base_uri' => 'http://default-environment.e9r9uw3qmg.eu-west-1.elasticbeanstalk.com/',
            'timeout'  => 2.0,
        ]);
        $user = $client->request('GET', '/api/v1/users/73', ['auth' => ['denin@testing.com', 'testing']]);
        var_dump($user);
        var_dump(Session::all());
        return view('adminlte::home');
    }
}