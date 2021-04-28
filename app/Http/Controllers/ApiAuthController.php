<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['login', 'signup']]);
    }

    /**
     * User Login
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email'    => 'required|email',
                'password' => 'required|string|min:6',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }

        if (!$token = \JWTAuth::attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        //return response()->json(auth()->user());

        return new UserResource((auth()->user()));
    }


    /**
     * User Register
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function signup(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'username' => 'required',
                    'email' => 'required',
                    'password' => 'required',

                ]
            );
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            DB::beginTransaction();
            //Create owner.
            $user = User::createUser($request->only(['first_name', 'last_name', 'username', 'email', 'password']));

            $user->save();

            DB::commit();
            return response()->json(['success' => true, 'data' => $user], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errmsg' => $e->getMessage()], 500);
        }
    }


    /**
     * User Logout
     * @return \Illuminate\Http\JsonResponse
     */

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User logged out successfully']);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */

    protected function respondWithToken($token)
    {
        return response()->json(
            [
                'access_token' => $token,
                'token_type'     => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'user' => auth()->user(),
//                'user_business_location' => auth()->user()->Business->location,

            ]
        );
    }
    public function refresh(Request  $request)
    {
        $this->auth->setRequest($request);
        $arr = $this->auth->getToken();
        $arr = $this->auth->refresh();
        $this->auth->setToken($arr);
        return response()->json([
            'success' => true,
            'data' => $request->user(),
            'refresh_token' => $arr
        ], 200);
    }
}
