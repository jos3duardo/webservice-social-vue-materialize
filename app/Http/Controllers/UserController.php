<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(Request $request){
        $data = $request->all();

        $validate = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validate->fails()){
            return $validate->errors();
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->token =  $user->createToken($user->email)->plainTextToken;

        return $user;
    }
    public function login(Request $request)
    {
        $data = $request->all();


        $valiacao = Validator::make($data, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);

        if($valiacao->fails()){
            return $valiacao->errors();
        }

        if(\Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){
            $user = auth()->user();
            $user->token = $user->createToken($user->email)->plainTextToken;
            return $user;
        }else{
            return ['status'=>false];
        }
    }

    public function profile(Request $request){
        $user = $request->user();
        $data = $request->all();

        return $data;
    }
}
