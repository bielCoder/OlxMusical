<?php

namespace App\Http\Controllers;

use App\Classes\Utilities\Response;
use App\Http\Requests\{AuthLoginRequest, AuthRegisterRequest};
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="BandFy",
 *     description="Sistema de comunicação e venda de serviços musicais"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
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
     *             @OA\Property(property="fullname", type="string", example="João Pé de Feijão"),
     *             @OA\Property(property="main_phone", type="integer", example="61999999999"),
     *             @OA\Property(property="optional_phone", type="integer", example="6177777777"),
     *             @OA\Property(property="email", type="string", example="joao@email.com.br"),
     *             @OA\Property(property="password", type="string", example="G@bri&L123"),
     *             @OA\Property(property="password_confirmed", type="boolean", example=true)
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
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor."
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


        /**
         * @OA\Post(
         *     path="/api/users/login",
         *     summary="Logar no sistema",
         *     description="Usuário pode fazer login e entrar no sistema",
         *     tags={"Autenticação"},
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             required={"email", "password"},              
         *             @OA\Property(property="email", type="string", format="email", example="joao@email.com.br"),
         *             @OA\Property(property="password", type="string", format="password", example="******"),
         *         )
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Login realizado com sucesso."
         *     ),
         *     @OA\Response(
         *         response=401,
         *         description="Credenciais inválidas."
         *     ),
         *     @OA\Response(
         *         response=500,
         *         description="Erro interno do servidor."
         *     )
         * )
         */


    public function login(AuthLoginRequest $request)
    {
        try{
            $user = User::where('email', $request->email)->first();
            if(!is_null($user) && Hash::check($request -> password, $user -> password))
            {
                return $this -> response -> format("users","application\json","post",$user,$user -> createToken($user -> fullname) -> plainTextToken,$user -> fullname.' is logged',202);
            } else {
                return $this -> response -> error("users","application\json","post","Credenciais Inválidas.",401);
            }
        } catch(\Exception $e)
        {
          
            return $this -> response -> error("users","application\json","post",$e -> getMessage(),500);
        } catch(\PDOException $e)
        {
          
            return $this -> response -> error("users","application\json","post",$e -> getMessage(),500);
        }
    }

            /**
             * @OA\Post(
             *     path="/api/users/logout",
             *     summary="Deslogar do sistema",
             *     description="Nesse endpoint o usuário irá deslogar de sua conta no sistema.",
             *     tags={"Autenticação"},
             *     security={{"bearerAuth":{}}},
             *     @OA\Response(
             *         response=204,
             *         description="Logout realizado com sucesso. Nenhum conteúdo retornado."
             *     ),
             *     @OA\Response(
             *         response=401,
             *         description="Credenciais inválidas"
             *     ),
             *     @OA\Response(
             *         response=500,
             *         description="Erro interno do servidor."
             *     )
             * )
             */
            public function logout(Request $request)
            {
                $requestToken = $request->header('Authorization');
                $personalAccessToken = new PersonalAccessToken();
            
                try {
                    $token = $personalAccessToken->findToken(str_replace('Bearer', '', $requestToken));
            
                    if ($token !== null) {
                        $token->delete();
                        return $this->response->format("users", "application/json", "post", null, null, 'Usuário deslogado com sucesso.', 202);
                    } else {
                        return $this->response->error("users", "application/json", "post", "Token Not Found", 404);
                    }
            
                } catch (\Exception $e) {
                    return $this->response->error("users", "application/json", "post", $e->getMessage(), 500);
                } catch (\PDOException $e) {
                    return $this->response->error("users", "application/json", "post", $e->getMessage(), 500);
                }
            }






}
