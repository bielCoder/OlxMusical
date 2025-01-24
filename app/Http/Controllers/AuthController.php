<?php

namespace App\Http\Controllers;

use App\Classes\Utilities\Response;
use App\Http\Requests\{AuthLoginRequest, AuthRegisterRequest};
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Socialite\Facades\Socialite;
use PDOException;

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
             // Verifica se as senhas conferem
             if (!$request->password_confirmation) {
                 return $this->response->error("users", "application/json", "post", "Senhas não conferem.", 401);
             }
     
             // Verifica se o e-mail já está cadastrado
             $find = $this->users->where('email', $request->email)->first();
     
             if ($find) {
                 // Atualiza a senha do usuário existente
                 $find->update([
                     "password" => Hash::make($request->password),
                 ]);
     
                 return $this->response->format("users", "application/json", "post", null, null, "Usuário registrado com sucesso", 200);
             } else {
                 // Cria um novo usuário
                 $saved = $this->users->create([
                     "image" => $request->image,
                     "fullname" => $request->fullname,
                     "main_phone" => $request->main_phone,
                     "optional_phone" => $request->optional_phone,
                     "email" => $request->email,
                     "password" => Hash::make($request->password),
                     "password_confirmation" => $request->password_confirmation,
                 ]);
     
                 return $this->response->format("users", "application/json", "post", $saved, null, "Usuário registrado com sucesso", 201);
             }
         } catch (\Exception $e) {
             return $this->response->error("users", "application/json", "post", $e->getMessage(), 500);
         } catch (\PDOException $e) {
             return $this->response->error("users", "application/json", "post", $e->getMessage(), 500);
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
             try {
                 $user = User::where('email', $request->email)->first();
                 if (!is_null($user) && Hash::check($request->password, $user->password)) {
                     // Verifica se fullname está definido
                     $fullname = $user->fullname ?? 'userLogin'; // Defina um nome padrão se fullname for null
         
                     Auth::login($user);
         
                     return $this->response->format(
                         "users",
                         "application\json",
                         "post",
                         $user,
                         $user->createToken($fullname)->plainTextToken,
                         $fullname . ' is logged',
                         202
                     );
                 } else {
                     return $this->response->error(
                         "users",
                         "application\json",
                         "post",
                         "Credenciais Inválidas.",
                         401
                     );
                 }
             } catch (\Exception $e) {
                 return $this->response->error(
                     "users",
                     "application\json",
                     "post",
                     $e->getMessage(),
                     500
                 );
             } catch (\PDOException $e) {
                 return $this->response->error(
                     "users",
                     "application\json",
                     "post",
                     $e->getMessage(),
                     500
                 );
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
    
        if (!$requestToken) {
            return response()->json([
                'message' => 'Authorization token not provided.'
            ], 400);
        }
    
        try {
            $token = str_replace('Bearer ', '', $requestToken);
    
            $personalAccessToken = PersonalAccessToken::findToken($token);
    
            if ($personalAccessToken) {
                $personalAccessToken->delete();
                Auth::logout();
    
                return response()->json([
                    'message' => 'Usuário deslogado com sucesso.'
                ], 202);
            }
    
            return response()->json([
                'message' => 'Token not found.'
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
 * @OA\Get(
 *     path="/auth/google/redirect",
 *     summary="Redirect to Google OAuth",
 *     description="Redirects the user to Google OAuth for authentication.",
 *     operationId="redirectOAuth",
 *     tags={"User - OAuth"},
 *     @OA\Response(
 *         response=302,
 *         description="Redirect to Google OAuth",
 *         @OA\Header(
 *             header="Location",
 *             description="Redirect location",
 *             @OA\Schema(type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error message")
 *         )
 *     )
 * )
 */

    public function redirectOAuth()
    {
        try {
            return Socialite::driver('google')->redirect();
        } catch(\Exception $e)
        {
            return $this -> response -> error("oauth","application\json","get",$e -> getMessage(), 500);
        } catch(PDOException $e)
        {
            return $this -> response -> error("oauth","application\json","get",$e -> getMessage(), 500);
        }
    }

    /**
 * @OA\Get(
 *     path="/auth/google/callback",
 *     summary="Handle Google OAuth Callback",
 *     description="Handles the callback from Google after authentication and logs in the user.",
 *     operationId="oAuth",
 *     tags={"User - OAuth"},
 *     @OA\Response(
 *         response=302,
 *         description="Redirect to the dashboard after successful login",
 *         @OA\Header(
 *             header="Location",
 *             description="Redirect location",
 *             @OA\Schema(type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error message")
 *         )
 *     )
 * )
 */

   
 public function oAuth()
 {
     try {
         $googleUser = Socialite::driver('google')->user();

         $user = User::updateOrCreate([
             'email' => $googleUser->email,
         ], [
             'name' => $googleUser->name,
             'google_id' => $googleUser->id,
             'google_token' => $googleUser->token,
             'google_refresh_token' => $googleUser->refreshToken
         ]);

         Auth::login($user);

         // Redireciona para a rota dashboard
         return redirect()->route('api.users');
         
     } catch (\Exception $e) {
         return $this->response->error("oauth", "application/json", "get", $e->getMessage(), 500);
     } catch (\PDOException $e) {
         return $this->response->error("oauth", "application/json", "get", $e->getMessage(), 500);
     }
 }
    



}
