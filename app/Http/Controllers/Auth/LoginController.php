<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    { 
        if(isset($_SESSION['alert-fail-login'])) {
           $ret = $_SESSION['alert-fail-login'];
           var_dump($ret);
            return view('adminlte::auth.login')->with('login_message', 'Victoria');
        } else {
            return view('adminlte::auth.login');    
        }
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(!isset($_SESSION['current_user_id'])) {
            $client = new Client([
                'base_uri' => 'http://default-environment.e9r9uw3qmg.eu-west-1.elasticbeanstalk.com/',
                'timeout'  => 2.0,
            ]);

            try {
                $login_request = $client->request('GET', 'api/v1/users' , 
                    [
                        'auth' => [$request->request->all()['email'], $request->request->all()['password']],
                        'future' => true
                    ]
                );
                if (session_status() == PHP_SESSION_NONE) {
                    session_start(); 
                    Session::put('current_user_id', $login_request->getHeaders()['current_user_id']);
                    Session::save();
                } else {
                    Session::put('current_user_id', $login_request->getHeaders()['current_user_id']);
                    Session::save();
                }
                return redirect()->route('home.index');
                //return view('adminlte::home');
            } catch(\Exception $e) {
                var_dump($e);
                $status = $e->getResponse()->getStatusCode();
                if ($status == 401) {

                    return view('adminlte::auth.login')->with('login_message','Unauthorized access.');
                }

            }
        } else {
            return redirect('/login');
        }

        /*$this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);*/
    }    


    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate(array $guards)
    {
        echo 'test';
            return $this->auth->authenticate();
        
    }    
}
