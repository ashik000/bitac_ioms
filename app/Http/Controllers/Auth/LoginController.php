<?php

namespace App\Http\Controllers\Auth;

use App\Data\Models\Role;
use App\Data\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator;
use Exception;
use Log;

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

//    use AuthenticatesUsers;

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
//        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request, UserRepository $userRepository) {
        $http = new Client();
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        $data = $request->all();

        if ($validator->fails()) {
            return response()->json("Invalid input", 400);
        }

        try {
            $scope = '';
            $user = $userRepository->findByEmail($data['username']);
            $roles = [];
            if (!empty($user)) {
                $user->roles->each(function ($role) use(&$roles) {
                    $roles[] = $role->name;
                });
                $scope = implode(' ', $roles);
            }
            $response = $http->post(config('app.oauth_login_endpoint', ''), [
                'form_params' => [
                    'grant_type'    => 'password',
                    'client_id'     => config('app.oauth_client_id'),
                    'client_secret' => config('app.oauth_client_secret'),
                    'username'      => $data['username'],
                    'password'      => $data['password'],
                    'scope'         => $scope
                ],
                'headers' => [
                    'Accept'     => 'application/json',
                ]
            ]);
            $responseData = json_decode($response->getBody(), true);
            $responseData['role'] = $scope;
            return response()->json($responseData);
        } catch (BadResponseException $ex) {
            if ($ex->getCode() == 400) {
                return response()->json('Invalid request', 400);
            }
            else if($ex->getCode() == 401) {
                return response()->json('Bad credentials', 401);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return response()->json('Something went wrong', 500);
        }
    }

    public function logout(Request $request) {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}
