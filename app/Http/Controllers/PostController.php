<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\ConnectionDB;
use App\Http\Requests\PostValidation;
use App\Http\Requests\EmailValidation;
use App\Http\Requests\UpdateAccessValidation;

class PostController extends Controller
{
    function checkLogged($email,$token)
    {
        $table = "users";
        $users = new ConnectionDb();
        $collection = $users->setConnection($table);
        $data = $collection->findOne(['email'=>$email,'remember_token'=>$token]);
        if($data["email"])
        {
            return true;
        }
        return false;
    }
    function posting(PostValidation $request)
    {
        $id;
        $table = "users";
        $conn = new ConnectionDb();
        $collection = $conn->setConnection($table);
        $email = $request->email;
        $tokens = $request->token;
        $check = self::checkLogged($email,$tokens);
        if($check == true)
        {
            $data = $collection->findOne(['remember_token'=> $tokens]);
            $id = $data->_id;
            $file = $request->file('file')->store('post');
            $user_id = $id;
            $access =$request->access;
            $table = "posts";
            $conn = new ConnectionDb();
            $collection = $conn->setConnection($table);
            $document = array( 
                "file" => $file, 
                "user_id" => $user_id, 
                "access" => $access
            );
            $collection->insertOne($document);
            echo "Posted";
       }   
    }
    function updateFile(EmailValidation $request)
    {
        $id;
        $table = "users";
        $conn = new ConnectionDb();
        $collection = $conn->setConnection($table);
        $email = $request->email;
        $tokens = $request->token;
        $pid = $request->post_id;
        $postId = new \MongoDB\BSon\ObjectId($pid);
        $check = self::checkLogged($email,$tokens);
        if($check == true)
        {
            $data = $collection->findOne(['email'=>$email]);
            $id = $data->_id;
            $file = $request->file('file')->store('post');
            $table = "posts";
            $conn = new ConnectionDb();
            $collection = $conn->setConnection($table);
            $collection->updateOne(array("_id"=>$postId), array('$set'=>array("file"=>$file)));
            echo "File Updated...";
       }   
    }
    function updateAccess(UpdateAccessValidation $request)
    {
        $id;
        $table = "users";
        $conn = new ConnectionDb();
        $collection = $conn->setConnection($table);
        $email = $request->email;
        $tokens = $request->token;
        $pid = $request->post_id;
        $postId = new \MongoDB\BSon\ObjectId($pid);
        $access = $request->access;
        $check = self::checkLogged($email,$tokens);
        if($check == true)
        {
            $data = $collection->findOne(['email'=>$email]);
            $id = $data->_id;
            $table = "posts";
            $conn = new ConnectionDb();
            $collection = $conn->setConnection($table);
            $collection->updateOne(array("_id"=>$postId), array('$set'=>array("access"=>$access)));
            echo "Access Updated...";
       }   
    }
    function deletePost(EmailValidation $request)
    {
        $id;
        $table = "users";
        $conn = new ConnectionDb();
        $collection = $conn->setConnection($table);
        $email = $request->email;
        $tokens = $request->token;
        $pid = $request->post_id;
        $postId = new \MongoDB\BSon\ObjectId($pid);
        $check = self::checkLogged($email,$tokens);
        if($check == true)
        {
            $data = $collection->findOne(['email'=>$email]);
            $id = $data["_id"];
            $table = "posts";
            $conn = new ConnectionDb();
            $collection = $conn->setConnection($table);
            $data1 = $collection->findOne(['user_id'=>$id, '_id'=>$postId]);
            if($data1!=NULL)
            {
                $table = "comments";
                $conn = new ConnectionDb();
                $collection = $conn->setConnection($table);
                $collection->deleteOne(['post_id' => $postId]);
                $table = "posts";
                $conn = new ConnectionDb();
                $collection = $conn->setConnection($table);
                $collection->deleteOne(['user_id'=>$id, '_id' => $postId]);
                echo "Post deleted...";
            }
            else
            {
                echo "No post available";
            }   
        }
        else
        {
            echo "Wrong email or token";
        }
    }
}
