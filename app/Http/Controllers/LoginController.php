<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use MongoDB\Client as Mongo;
use App\Services\ConnectionDB;
class LoginController extends Controller
{
    function jwtToken($email,$passwor)
    {   
        $key = "uzair";
        $payload = array(
            "iss" => "localhost",
            "aud" => time(),
            "iat" => now(),
            "nbf" => 100000
        );
        $table = "users";
        $jwt = JWT::encode($payload, $key, 'HS256');
        $conn = new ConnectionDb();
        $collection = $conn->setConnection($table);
        $data = $collection->findOne(['email'=>$email]);
        $pass = $data["password"];
        if (Hash::check($passwor, $pass)) 
        {
            $collection->updateOne(array("email"=>$email), array('$set'=>array("remember_token"=>$jwt)));
            $collection->updateOne(array("email"=>$email), array('$set'=>array("status"=>'1')));
            return response()->json(['remember_token'=>$jwt , 'message'=> 'successfuly login']);
        }

    }
    public function checkLogged($email,$token)
    {
        $table = "users";
        $user = new ConnectionDb();
        $collection = $user->setConnection($table);
        $data = $collection->findOne(['email'=>$email,'remember_token'=>$token]);
        if($data!=NULL)
        {
            return true;
        }
        return false;
    }
    function updateName(Request $request)
    {
        $table = "users";
        $user = new ConnectionDb();
        $collection = $user->setConnection($table);
        $email = $request->email;
        $token = $request->token;
        $newname = $request->newname;
        $check = self::checkLogged($email,$token);
        if($check == true)
        {
            $collection->updateOne(array("remember_token"=>$token), array('$set'=>array("name"=>$newname)));
            echo "Name is changed...";
        }
        else
        {
            echo "Wrong Email or token...";
        }
    }
    function forgetPassword(Request $request)
    {
        $table = "users";
        $user = new ConnectionDb();
        $collection = $user->setConnection($table);
        $email = $request->email;
        $token = $request->token;
        $newpassword = Hash::make($request->newpassword);
        $check = self::checkLogged($email,$token);
        if($check == true)
        {
            $collection->updateOne(array("remember_token"=>$token), array('$set'=>array("password"=>$newpassword)));
            echo "Password is changed...";
        }
        else
        {
            echo "Wrong Email or token...";
        }
    }
    function updateGender(Request $request)
    {
        $table = "users";
        $user = new ConnectionDb();
        $collection = $user->setConnection($table);
        $email = $request->email;
        $token = $request->token;
        $gender = $request->gender;
        $check = self::checkLogged($email,$token);
        if($check == true)
        {
            $collection->updateOne(array("remember_token"=>$token), array('$set'=>array("gender"=>$gender)));
            echo "Gender is changed...";
        }
        else
        {
            echo "Wrong Email or token...";
        }
    }
    function loggingIn(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        return self::jwtToken($email,$password);
        
    }
}
