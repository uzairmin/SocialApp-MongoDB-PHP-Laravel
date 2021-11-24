<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Mail\TestMail;
use App\Http\Requests\SignupValidation;
use App\Http\Requests\EmailValidation;
use App\Services\ConnectionDB;

class SignupController extends Controller
{
   
    function signingUp(SignupValidation $request)
    {
        $table = "users";
        $token = rand(1000,1000000);
        $user = new ConnectionDb();
        $collection = $user->setConnection($table);
        $name = $request->name;
        $email = $request->email;
        $password = Hash::make($request->password);
        $gender = $request->gender;
        $active = 1;
        $token = $token;
        $document = array( 
            "name" => $name, 
            "email" => $email, 
            "password" => $password,
            "gender" => $gender,
            "active" => 1,
            "token" => $token
        );
        $collection->insertOne($document);
        $details = ['title'=>'Verify to continue',
                'body'=>'http://127.0.0.1:8000/api/confirmation/'.$email.'/'.$token
            ];
        Mail::to($email)->send(new TestMail($details));
        return 'Mail Sent...';
    }
    public function checkLogged($email,$token)
    {
        $table = "users";
        $user = new ConnectionDb();
        $collection = $user->setConnection($table);
        $data = $collection->findOne(['email'=>$email,'remember_token'=>$token]);
        if($data->email)
        {
            return true;
        }
        return false;
    }
    function deactivate(EmailValidation $request)
    {
        $table = "users";
        $email = $request->email;
        $token = $request->token;
        $check = self::checkLogged($email,$token);
        if($check == true)
        {
            $user = new ConnectionDb();
            $collection = $user->setConnection($table);
            $collection->updateOne(array("remember_token"=>$token), array('$set'=>array("active"=>null,"status"=>null,"remember_token"=>null)));
            echo "User deactivated";
        }
        else
        {
            echo "User is not authenticated";
        }
    }
}
