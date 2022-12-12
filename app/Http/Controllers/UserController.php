<?php

namespace App\Http\Controllers;

use App\Http\Requests\SigInRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!$this->check("user-show")) {
            return response()->json(["message" => env('MESSAGE_FORBIDDEN')], 403);
        }
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!$this->check("user-edit")) {
            return response()->json(["message" => env('MESSAGE_FORBIDDEN')], 403);
        }
        $user =  User::find($id);
        $user->first_name = $request->first_name ?? $user->first_name;
        $user->middle_name = $request->middle_name ?? $user->middle_name;
        $user->surname = $request->surname ?? $user->surname;
        $user->phone = $request->phone ?? $user->phone;
        $user->password = $request->password ? bcrypt($request->password) : $user->password;

        return response()->json(['message'=>'успешный'],200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!$this->check("user-delete")) {
            return response()->json(["message" => env('MESSAGE_FORBIDDEN')], 403);
        }
        $user =  User::find($id)->delete();

        return response()->json(['message'=>'успешный'],200);
    }

    public function signIn(SigInRequest $request)
    {
        $username = $request->get("username");

        $user = User::where("username", $username)->where("active", true)->first();

        if(!$user){
            return response()->json(["message" => "пользователь не активен"], 400);
        }
        if (!Auth::attempt($request->validated())) return response()->json(["message" => "Unauthorized ..."], 401);

        $user?->tokens()?->delete();

        $token = $user->createToken(env("APP_NAME") ?? "site-for-out")->plainTextToken;

        return response()->json(["token" => $token], 200);
    }
}
