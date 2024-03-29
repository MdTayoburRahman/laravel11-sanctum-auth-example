<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    //Register api 
    //POST[name,email,password]
    public function register(Request $request){
       
        try{
             //validation
        $request->validate([
            'name' =>'required|string',
            'email' =>'required|string|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $name = $request->name;
        $email = $request->email;
        $password = $request->password;


        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password)
        ]);

        if($user){
            return response([
                "status" =>"success",
                "message" => "Registered Successfully",
            ],200);
        }else{
            return response([
                "status" =>"error",
                "message" => "Something went wrong",
            ],500);
        }

        }catch(Exception $e){
           Log::error($e->getMessage());
            return response()->json([
               'status'=>false,
               'message'=>$e->getMessage()
            ]);
        }
       
        
        
    }

    //login api
    //POST[email,password]
    public function login(Request $request){
        try {
            $request->validate([
                "email"=>"required|email",
                "password"=>"required|min:6",
            ]);


            $user = User::where('email', $request->email)->first();
            if (!empty($user)) {
                if (Hash::check($request->password,$user->password)) {

                    $token = $user->createToken('myToken')->plainTextToken;
                    return response(
                        [
                            "status" => "success",
                            "message" => "Login Successfully",
                            "token" => $token,
                            "data" =>[]
                        ],
                        200
                    );
                 
                }else{
                    return response([
                        "status" =>"error",
                        "message" => "Invalid Credentials",
                    ],401);
                }

            }else {
                return response([
                    "status" =>"error",
                    "message" => "Invalid Credentials",
                ],401);
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());
            return response()->json([
               'status'=>false,
               'message'=>$th->getMessage()
            ]);
        }

    }
    

    //profile api
    //GET[Auth:Token]
    public function profile(Request $request){
        try {
        
            $userData = auth()->user();
            if (!empty($userData)) {
                return response()->json([
                    "status"=>"success",
                    "message" => "User Data",
                    "data" => $userData
                ],200);
            }

            
            

        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());
            return response()->json([
               'status'=>false,
               'message'=>$th->getMessage()
            ],401);
        }
    }

    //logout api
    //GET[Auth:Token]
    public function logout(Request $request){

        if (auth()->user()->tokens()->delete()) {
            return response()->json([
                "status"=>"success",
                "message" => "Logout Successfully",
            ],200);
        }else {
            return response()->json([
                "status"=>"error",
                "message" => "Something went wrong",
            ],500);
        }

              

               
    }

}
