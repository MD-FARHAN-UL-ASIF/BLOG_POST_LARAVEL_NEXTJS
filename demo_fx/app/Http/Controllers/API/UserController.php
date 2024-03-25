<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
     public function Register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'password' => 'required|min:6',
        ]);

        if($validator ->fails()){
            return response()->json([
                'status' => 422,
                'error' => $validator->messages()
            ],422);

        }else {

            $user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => bcrypt($request->password), // Corrected field name to password
]);



            if($user){
                return response()->json([
                    'status' => 200,
                    'message' => "User Created Successfully"
                ],200);
            }else
            {
                return response()->json([
                    'status' => 500,
                    'message' => "hey...You are in a long troble"
                ],500);
            }
        }
    }
   public function loginUser(Request $request): Response
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return Response([
                'errors' => $validator->errors(),
            ], 422);
        }

        if (Auth::attempt($input)) {
            $user = Auth::user();
            $token = $user->createToken('Token Name')->accessToken;

            return Response([
                'status' => 200,
                'token' => $token,
            ], 200);
        } else {
            return Response([
                'errors' => ['auth' => 'Login failed. Please check your credentials.'],
            ], 422);
        }
    }

    public function getUserDetail(): Response
    {
        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            return Response(['data' => $user], 200);
        }

        return Response(['data' => 'Unauthorized'], 401);
    }

    public function index(){
        $user = User::all();

        if($user ->count() >0){
            return response() -> json([
                'status' => 200,
                'users' => $user
            ], 200);
        }else{
            return response() -> json([
                'status' => 404,
                'message' => 'No users found'
            ], 404);
        }
    }

   public function show($id)
{
    $user = User::find($id);
    if($user){
        return response()->json([
            'status' => 200,
            'user' => $user
        ],200);

    } else {
        return response()->json([
            'status' => 404,
            'message' => "User not found"
        ],404);
    }
}


    public function userLogout(): Response
    {
        if (Auth::guard('api')->check()) {
            $accessToken = Auth::guard('api')->user()->token();

            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $accessToken->id)
                ->update(['revoked' => true]);
            $accessToken->revoke();

            return Response(['data' => 'Logout', 'message' => 'Successfully logged out...'], 200);
        }

        return Response(['data' => 'Unauthorized'], 401);
    }
}
