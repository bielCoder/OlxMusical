<?php

namespace App\Http\Controllers;

use App\Classes\Utilities\Response;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="OLX Musical",
 *     description="Sistema de comunicação e venda de serviços musicais"
 * )
 * )
 */


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


        /**
         * @OA\Post(
         *     path="/api/users/register",
         *     summary="Registrar um novo usuário",
         *     description="Cria um novo usuário na aplicação.",
         *     tags={"Autenticação"},
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             required={"fullname","main_phone","optional_phone","email", "password","password_confirmed"},
         *             @OA\Property(property="fullname", type="string", format="fullname", example="João Pé de Feijão"),
         *             @OA\Property(property="main_phone", type="integer", format="main_phone", example="61999999999"),
         *             @OA\Property(property="optional_phone", type="integer", format="optional_phone", example="6177777777"),            
         *             @OA\Property(property="email", type="string", format="email", example="joao@email.com.br"),
         *             @OA\Property(property="password", type="string", format="password", example="G@bri&L123"),
         *             @OA\Property(property="password_confirmed", type="boolean", format="password", example="true")
         *         )
         *     ),
         *     @OA\Response(
         *         response=201,
         *         description="Usuário registrado com sucesso."
         *     ),
         *     @OA\Response(
         *         response=422,
         *         description="Senhas não conferem."
         *     ),
         *     @OA\Response(
         *         response=409,
         *         description="Usuário já cadastrado."
         *     )
         * )
         */


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
                "password" => Hash::make($request -> password),
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
