<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        require("ldap.php");
        header("Content-Type: text/html; charset=utf-8");

        $usr = $request["username"];
        $usuario = mailboxpowerloginrd($usr, $request["password"]);

        if ($usuario == "0" || $usuario == '') {
            throw ValidationException::withMessages([
                'username' => ['Las credenciales proporcionadas son incorrectas']
            ]);
        } else {
            $user = User::where('email', $request->username)->count();
            if ($user > 0) {
                $user = User::where('email', $request->username)->first();

                return response()->json([
                    "status"    => 'ok',
                    "token"     => $user->createToken('Auth Token')->accessToken
                ], 200);
            } else {
                
                //dd($usuario[0]['cn'][0]); //nombre completo

                User::create(
                    [
                        'email'     => $request->username,
                        'name'      => $usuario[0]['cn'][0],
                        'password' => bcrypt($request['password'])
                    ]
                );
                $user = User::where('email', $request->username)->first();
                return response()->json([
                    "status"    => 'ok',
                    "token"     => $user->createToken('Auth Token')->accessToken
                ], 200);
            }
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
    }
}
