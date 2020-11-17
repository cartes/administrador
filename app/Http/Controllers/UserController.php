<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(Request $request)
    {
        //Tomar datos string y decodificarlos como json

        $register = $request->input("register", null);
        $params = json_decode($register); //Object
        $params_array = json_decode($register, true); //Array

        // Validacion de datos
        if (!empty($params) && !empty($params_array)) {
            $params_array = array_map("trim", $params_array); // Limpia los datos de posibles caracteres basura

            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users', // Comprueba a usuario registrado.
                'password' => 'required',
                'role' => 'required|integer',
            ]);

            if ($validate->fails()) {
                // Validacion falló
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'No se ha creado',
                    'errors' => $validate->errors(),
                ];
            } else {
                //Validacion funciono perfecto
                $pwd = hash('sha256', $params->password);

                // *** Guardar el usuario en la base de datos
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->role = $params_array['role'];
                $user->password = $pwd;

                $user->save();

                $data = [
                    'status' => 'error',
                    'code' => 200,
                    'message' => 'El usuario ha sido creado'
                ];
            }


        } else {
            // Error en caso de qu los datos estén vacios
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'Hay un error en los datos'
            ];

        }
        return response()->json($data, $data['code']);
    }

    public function login(Request $request)
    {
        $jwtAuth = new JwtAuth();
        $login = $request->input('login');
        $params = json_decode($login); //Objeto


        $pwd = hash('sha256', $params->password);
        $email = $params->email;

        return response()->json($jwtAuth->signup($email, $pwd, true));
    }
}