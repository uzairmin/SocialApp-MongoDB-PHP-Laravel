<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\ConnectionDB;

class LogoutController extends Controller
{
    function checkLogged($email,$token)
    {
        $table = "users";
        $user = new ConnectionDb();
        $collection = $user->setConnection($table);
        $data = $collection->findOne(['email'=>$email,'remember_token'=>$token]);
        if($data["email"])
        {
            return true;
        }
        return false;
    }
    function loggingOut(Request $request)
    {
        $table = "users";
        $user = new ConnectionDb();
        $collection = $user->setConnection($table);
        $email = $request->email;
        $token = $request->token;
        $check = self::checkLogged($email,$token);
        if($check == true)
        {   
            $check1 = $collection->updateOne(array("remember_token"=>$token), array('$set'=>array("status"=>'0')));
            $check2 = $collection->updateOne(array("remember_token"=>$token), array('$set'=>array("remember_token"=>null)));
            echo"Logged Out";
        } 
    }
}
