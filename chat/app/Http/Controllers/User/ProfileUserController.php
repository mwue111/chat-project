<?php

namespace App\Http\Controllers\User;

use App\Http\Resources\User\ProfileUserGeneralResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileUserController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function profileUser(Request $request) {
        $user = auth('api')->user();
        $userModel = User::findOrFail($user->id);
        //recepción de archivos (avatar):
        if($request->hasFile('imagen')){
            if($userModel->avatar) {
                Storage::delete($userModel->avatar);
            }

            $path = Storage::putFile('user', $request->file('imagen'));
            $request->request->add(['avatar' => $path]);
        }
        $userModel->update($request->all());

        return response(['message'=> 200, 'user'=> ProfileUserGeneralResource::make($userModel)]);
    }

    public function contactUsers() {
        $users = User::where('id', '<>', auth('api')->user()->id)
                    ->orderBy('id','desc')
                    ->get();

        return response()->json(['users' => $users->map(function($user) {
            return ProfileUserGeneralResource::make($user);
        }),
        ]);
    }
}
