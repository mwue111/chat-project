<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User\ProfileUserGeneralResource;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100|unique:users,email',
            'password' => 'required|string|confirmed|min:8|max:50',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = User::create($request->all());

        return response()->json([
            'message' => '¡Usuario registrado correctamente!',
        ], 200);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if(!$token = auth('api')->attempt($validator->validated())) {
            if(User::where('email', $request->email)->first()){
                return response()->json(['error' => 'Password'], 401);
            }
            else{
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        return $this->respondWithToken($token);
    }

    public function logout() {
        auth('api')->logout();

        return response()->json(['message' => 'Has cerrado sesión. ¡Hasta pronto!']);
    }

    public function refresh() {
        return $this->respondWithToken(auth('api')->refresh());
    }

    public function profile() {
        return response()->json(auth('api')->user());
    }

    protected function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => ProfileUserGeneralResource::make(auth('api')->user()),    //uso del resource en lugar de hacer el array a mano
            // 'user' => [
            //     'name' => auth('api')->user()->name,
            //     'surname' => auth('api')->user()->surname,
            //     'email' => auth('api')->user()->email,
            // ]
        ]);
    }
}
