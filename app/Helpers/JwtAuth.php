<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class JwtAuth
{
    public $key;

    public function __construct()
    {
        $this->key = "Ramones1977";
    }

    public function signup($email, $pwd, $gettoken = null)
    {
        // buscar al usuario
        $user = User::where([
            'email' => $email,
            'password' => $pwd,
        ])->first();

        // Comprobar las credenciales
        $signup = false;
        if (is_object($user)) {
            $signup = true;
        }
        // Generar el token
        if($signup) {
            $token = [
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'iat' => time(),
                'exp' => time()+3600,
            ];

            $jwt = JWT::encode($token, $this->key, 'HS256');
            $jwtdecoded = JWT::decode($jwt, $this->key, ['HS256']);

            if (is_null($gettoken)) {
                $data = $jwt;
            } else {
                $data = $jwtdecoded;
            }
        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'Login incorrecto'
            ];
        }

        // devolder los datos

        return $data;
    }
}