<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Services\ConnectionDB;
use Illuminate\Http\Request;

class ConfirmationController extends Controller
{
    function confirming($email,$token)
    {
        $table = "users";
        $users = new ConnectionDb();
        $collection = $users->setConnection($table);
        $check = $collection->updateOne(array("email"=>$email, "token"=>$token), array('$set'=>array("email_verified_at"=>now())));
        if($check == true)
        {
            echo"Email Verified...";
        }
        else if($check == false)
        {
            echo"Email is not verified...";
        }
    }
}