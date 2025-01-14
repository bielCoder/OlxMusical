<?php

namespace App\Http\Controllers;

use App\Classes\Utilities\Response;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // User Register

    private $users;
    private $response;

    public function __construct(User $users, Response $response)
    {
        $this -> users = $users;
        $this -> response = $response;
    }

    public function register(AuthRegisterRequest $request)
    {
        try {

            if(!$request -> password_confirmed)
            {
                return $this -> response -> error("users","application\json","post","senhas não conferem.",401);
            }

            $saved = $this -> users -> create([
                "image" => $request -> image,
                "fullname" => $request -> fullname,
                "main_phone" => $request -> main_phone,
                "optional_phone" => $request -> optional_phone,
                "email" => $request -> email,
                "password" => $request -> password,
                "password_confirmed" =>  $request -> password_confirmed
            ]);
            return $this -> response -> format("users","application\json","post",$saved,null,"Usuário registrado com sucesso",201);
        } catch(\Exception $e) 
        {
            return $this -> response -> error("users","application\json","post",$e -> getMessage(), 500);
        } catch(\PDOException $e)
        {
            return $this -> response -> error("users","application\json","post",$e -> getMessage(), 500);
        }
    }
}
