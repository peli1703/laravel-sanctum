<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    // Register: di method ini, kita menambahkan validasi untuk name, email dan password
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        //Jika data POST request gagal divalidasi, maka akan mengirimkan response error dari validasi tersebut. 
        // if($validator->fails()){
        //     return response()->json($validator->errors());
        // }

        //Tapi, jika POST request berhasil divalidasi, maka data dari POST request akan disimpan di table users dan akan membuat token baru, 
        //serta akan mengirimkan response json yang berisikan detail dari data yang telah ditambahkan beserta token yang telah berhasil dibuat.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->name),
        ]);

        // $token = $user->createToken('auth_token')->plainTextToken;

       if($user){
        return response()
        ->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer', ]);
       }else{
        return response()->json(["failed"]);
       }
        
    }

    public function login(Request $request){

        // if (!Auth::attempt($request->only('email', 'password')))
        // {
        //     return response()
        //         ->json(['message' => 'Unauthorized'], 401);
        // }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['message' => 'Hi '.$user->name.', welcome to home','access_token' => $token, 'token_type' => 'Bearer']);
    }


    // method for user logout and delete token
    public function logout()
    //Method ini akan menghapus user session dengan menghapus semua token milik user tersebut di table personal_access_token.
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ];
    }   
}
