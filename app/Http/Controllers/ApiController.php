<?php

namespace App\Http\Controllers;

use JWTAuth;
use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;


class ApiController extends Controller
{   
    public function register(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:10'
        ]);
        if ($validator->fails()) {
           return $this->errorJsonResponse('Email id already exists!', null, 401);
        }
        else{
            try {
                if ($request->expectsJson()) {
                    $user           = new User();
                    $user->name     = $request->name;
                    $user->email    = $request->email;
                    $user->password = bcrypt($request->password);
                    $user->save();
                    return $this->successJsonResponse('Registration Created Successfully!', $user, 200);
                }else{
                    return $this->errorJsonResponse('Reuested data is not valid!!', null, 422);
                }
              } catch (Throwable $e) {
                    Log::info($e);
                    return $this->errorJsonResponse('Something went wrong!', null, 422);
             }
        }
    }
 
    public function login(Request $request)
    {
        $input = $request->only('email', 'password');
        $jwt_token = auth()->guard('api')->attempt($input);
        
        if (!$jwt_token = auth()->guard('api')->attempt($input)) {
            return $this->errorJsonResponse('Invalid Email or Password!', null, 401);
        }
 
        $user = DB::table((new User)->getTable())
                        ->whereEmail($request->email)
                        ->first();

        return $this->respondWithToken($jwt_token, $user);
    }
 
    public function logout(Request $request)
    {  
        try {
            auth()->guard('api')->logout();
            return $this->successJsonResponse('User logged out successfully!', null, 401);
        } catch (Throwable $exception) {
            Log::info($exception);
            return $this->errorJsonResponse('Sorry, something went wrong!', null, 401);
        }
    }
 
    public function getAuthenticatedUser(Request $request)
    {
        if( ! auth()->guard('api')->check() ){
            return $this->errorJsonResponse('You are not authorized!!', null, 401);
        }

        return $this->successJsonResponse(
            '',
            auth()->guard('api')->user(),
            200
        );
    }
}
