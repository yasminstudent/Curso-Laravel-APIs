<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class AuthController extends Controller
{
    public function create(Request $request)
    {
        $array = ["error" => ""];

        //Validação
        $rules = [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->messages();
            return $array;
        }

        //Criando novo usuário
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $newUser = new User();
        $newUser->name = $name;
        $newUser->email = $email;
        $newUser->password = password_hash($password, PASSWORD_DEFAULT);
        $newUser->save();

        //Logar usuário recém criado

        return $array;
    }

    public function login(Request $request)
    {
        $array = ["error" => ""];

        $creds = $request->only('email', 'password');

        /*
        Auth::attempt([
            'email' => 'tal',
            'password' => 'tal'
        ])
        */

        if(Auth::attempt($creds)){
            $user = User::where('email', $creds['email'])->first();
            //cria um token baseado em uma string e pega o texto do token
            $item = time().rand(0, 9999);
            $token = $user->createToken($item)->plainTextToken;

            $array['token'] = $token;
        }
        else{
            $array["error"] = "E-mail e/ou senha incorretos";
        }

        return $array;
    }

    public function logout(Request $request){
        $array = ['error' => ''];

        $user = $request->user();
        $user->tokens()->delete();

        return $array;
    }

    public function loginJWT(Request $request){
        $array = ["error" => ""];

        $creds = $request->only('email', 'password');

        $token = Auth::attempt($creds);

        if($token){
            $array['token'] = $token;
        }
        else{
            $array["error"] = "E-mail e/ou senha incorretos";
        }

        return $array;
    }

    public function logoutJWT(){
        $array = ["error" => ""];

        Auth::logout();

        return $array;
    }

    public function me(){
        $array = ['error' => ''];

        $user = Auth::user();
        $array['email'] = $user->email;

        return $array;
    }
}
