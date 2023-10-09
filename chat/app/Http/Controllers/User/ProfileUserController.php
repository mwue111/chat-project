<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class ProfileUserController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function profile_user(Request $request) {
        $user = auth('api')->user();
        $userModel = User::findOrFail($user->id);
        $userModel->update($request->all());

        return response(['message'=> 200, 'user'=> []]);
    }
}
