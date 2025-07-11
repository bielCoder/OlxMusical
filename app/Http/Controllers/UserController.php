<?php

namespace App\Http\Controllers;

use App\Classes\Utilities\Response;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private $response;
    private $users;

    public function __construct(Response $response, User $users)
    {
        $this -> response = $response;
        $this -> users = $users;
    } 

    public function index()
    {
        try {
            return $this -> response -> format("users","application\json","get",$this -> users -> paginate($request -> per_page ?? 10),null,null,200);
        } catch(\Exception $e)
        {
            return $this -> response -> error("users","application\json","get",$e -> getMessage(),500);
        } catch(\PDOException $e)
        {
            return $this -> response -> error("users","application\json","get",$e -> getMessage(),500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
