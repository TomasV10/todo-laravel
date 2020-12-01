<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\BadResponseException;

class AuthController extends Controller
{
    public function login(Request $request){

        $http = new \GuzzleHttp\Client;
    
        try {
            $response = $http->post(config('services.passport.login_endpoint'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                    'username' => $request->username,
                    'password' => $request->password,
                ]
            ]);
            return $response->getBody();
        } catch (BadResponseException $e) {
            switch ($e->getCode()) {
                case 400:
                case 401:
                    return response()->json('Your credentials are incorrect. Please try again', $e->getCode());
                break;
                default:
                    return response()->json('Something went wrong on the server', $e->getCode());
            }
        }
    }

    public function index()
    {
        return User::all();
    }

    public function update(Request $request, User $user)
    {
        // if($todo->user_id !== auth()->user()->id){
        //     return response()->json('Unauthorized', 401);
        // }
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        $user->update($data);
        return response($user, 200);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
    

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response("Selected user was deleted", 200);
    }
    


    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json('Logged out successfully', 200);
    }
}
