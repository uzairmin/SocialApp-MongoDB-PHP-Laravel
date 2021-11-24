<?php

namespace App\Http\Controllers;
use App\Models\Friend;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\ConnectionDB;

class FriendController extends Controller
{
    function checkLogged($email,$token)
    {
        $table = "users";
        $users = new ConnectionDb();
        $collection = $users->setConnection($table);
        $data = $collection->findOne(['email'=>$email,'remember_token'=>$token]);
        if($data->email)
        {
            return true;
        }
        return false;
    }
    function addFriend(Request $request)
    {
        $u1Id;
        $u2Id;
        $table = "users";
        $users = new ConnectionDb();
        $collection = $users->setConnection($table);
        $email1 = $request->email1;
        $token = $request->token;
        $email2 = $request->email2;
        $check = self::checkLogged($email1,$token);
        if($check == true)
        {
            $data1 = $collection->findOne(['email'=>$email1]);
            $u1Id = $data1->_id;
            $data2 = $collection->findOne(['email'=>$email2]);
            $u2Id = $data2->_id;
            $table = "friends";
            $friends = new ConnectionDb();
            $collection = $friends->setConnection($table);
            $data = $collection->findOne(['user1_id'=>$u1Id, 'user2_id'=>$u2Id]);
            if($data!=NULL)
            {
                return "You both are already friends"; 
            }
            else
            {
                $document = array( 
                    "user1_id" => $u1Id, 
                    "user2_id" => $u2Id, 
                );
                $collection->insertOne($document);
            }
        }
    }
}
