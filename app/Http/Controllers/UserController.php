<?php

namespace App\Http\Controllers;

use App\User;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\RegistersUsers;


class UserController extends Controller
{
    use RegistersUsers;

    public function __construct()
    {
        $this->middleware('guest');
    }

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
    
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'terms' => 'required',
        ]);
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        //return User::create([
            //'name' => $data['name'],
            //'email' => $data['email'],
            //'password' => bcrypt($data['password']),
        //]);
        
        $name = $data['name'];
        $email = $data['email'];
        // $pass = base64_encode($data['password']);
        $pass = $data['password'];
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://default-environment.e9r9uw3qmg.eu-west-1.elasticbeanstalk.com/api/v1/users");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);
        $n = array(
                'name' => $name,
                'email' => $email,
                'password' => $pass
            );

        $n = json_encode($n);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $n);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type: application/json"
        ));

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpcode;
    }
     /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function register(Request $request)
    {

        $this->validator($request->all())->validate();
        $reg = $this->create($request->all());
        //TODO Switch if to CASE IF
        if($reg == 201) {
            //Success, send auth and give acces
            $client = new Client([
                'base_uri' => 'http://default-environment.e9r9uw3qmg.eu-west-1.elasticbeanstalk.com/',
                'timeout'  => 2.0,
            ]);
            $login_request = $client->request('GET', 'api/v1/users', ['auth' => ['newuser@test.com', 'jordan23']]);
            $headers = $login_request->getHeaders();
            $_SESSION['current_user_id'] = $headers['current_user_id'];

            return redirect()->route('home.index');

        } else if ($reg == 500) {
            // User already exists


        }

    }

    protected function authenticate(array $guards)
    {
        echo 'test';
            return $this->auth->authenticate();
        
    }      
}
