<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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

        if (isset($data['password'])){
            $validate = Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
            if ($validate->fails()){
                return $validate->errors();
            }
            $user->password = Hash::make($data['password']);
        }else{
            $validate = Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            ]);
            if ($validate->fails()){
                return $validate->errors();
            }
            $user->name = $data['name'];
            $user->email = $data['email'];
        }

        if (isset($data['image'])){

            Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
                $explode = explode(',', $value);
                $allow = ['png', 'jpg', 'svg','jpeg'];
                $format = str_replace(
                    [
                        'data:image/',
                        ';',
                        'base64',
                    ],
                    [
                        '', '', '',
                    ],
                    $explode[0]
                );
                // check file format
                if (!in_array($format, $allow)) {
                    return false;
                }
                // check base64 format
                if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                    return false;
                }
                return true;
            });

            $valiacao = Validator::make($data, [
                'image' => 'base64image',

            ],['base64image'=>'Imagem inválida']);

            if($valiacao->fails()){
                return $valiacao->errors();
            }


            $time = time();
            $diretorioPai = 'perfils';
            $diretorioImagem = $diretorioPai.DIRECTORY_SEPARATOR.'perfil_id'.$user->id;
            $ext = substr($data['image'], 11, strpos($data['image'], ';') -11 );

            $urlImagem = $diretorioImagem.DIRECTORY_SEPARATOR.$time.'.'.$ext;

            $file = str_replace('data:image/'.$ext.';base64', '', $data['image']);
            $file = base64_decode($file);

            if (!file_exists($diretorioPai)){
                mkdir($diretorioPai, 0700);
            }

            if ($user->image){
                if (file_exists($user->image)){
                    unlink($user->image);
                }
            }

            if (!file_exists($diretorioImagem)){
                mkdir($diretorioImagem, 0700);
            }

            file_put_contents($urlImagem, $file);

            $user->image = $urlImagem;
        }

        $user->save();

        $user->image = asset($user->image);
        $user->token = $user->createToken($user->email)->plainTextToken;

        return $user;
    }
}
