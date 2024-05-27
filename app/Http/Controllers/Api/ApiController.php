<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    public function register(Request $request){
      $request ->validate([
        "name" => "required|string",
        "email" => "required|string|email|unique:users",
        "password" => "required|confirmed"
      ]); 

      User::create([
        "name" => $request->name,
        "email" => $request->email,
        "password" => bcrypt($request->password)
      ]);
      return response()->json([
        "status" => true,
        "message" => "Done",
        "data" => []
      ]);
    }

    public function login(Request $request){
      $request ->validate([
          "email" => "required|string|email",
          "password" => "required"
      ]);

      $user = User::where("email",$request->email)->first();
      if(!empty($user)){
        if(Hash::check($request->password,$user->password)){
          $token = $user->createToken("mytoken")->accessToken;
          return response()->json([
            "status" => true,
            "message" => "Login Successful",
            "token" => $token,
            "data" => []
          ]);
        } else {
          return response()->json([
            "status" => false,
            "message" => "Password did not matched",
            "data" => []
          ]);  
        }
      } else {
        return response()->json([
          "status" => false,
          "message" => "Invalid email value",
          "data" => []
        ]);
      }        
    }

    public function profile(Request $request){
      $userdata = auth()->user();
      return response()->json([
        "status" => true,
        "message" => "Profile information",
        "data" => $userdata
      ]);
    }

    public function logout(Request $request){
      $token = auth()->user()->token();
      $token->revoke();

      return response()->json([
        "status" => true,
        "message" => "Logout Successfully"
      ]);
    }
}
