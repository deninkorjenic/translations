<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

/**
 * Class RegisterController
 * @package %%NAMESPACE%%\Http\Controllers\Auth
 */
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('adminlte::auth.register');
    }

    /**
     * Where to redirect users after login / registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
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

        } else if ($reg == 500) {
            // User already exists


        }

    }

}
