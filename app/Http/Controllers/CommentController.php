<?php

namespace App\Http\Controllers;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\ConnectionDB;
use App\Http\Requests\EmailValidation;

class CommentController extends Controller
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
    function checkFriends($userId,$postId)
    {
        $table = "posts";
        $conn = new ConnectionDb();
        $collection = $conn->setConnection($table);
        $data1 = $collection->findOne(['_id'=>$postId]);
        $user = $data1->user_id;
        $table = "friends";
        $conn = new ConnectionDb();
        $collection = $conn->setConnection($table);
        $data2 = $collection->findOne(['user1_id'=>$userId,'user2_id'=>$user]);
        if($data2)
        {
            return true;
        }
        $data3 = $collection->findOne(['user2_id'=>$userId,'user1_id'=>$user]);
        if($data3!=NULL)
        {
            return true;
        }
        return false;
    }
    function checkAccess($postId)
    {
        $table = "posts";
        $conn = new ConnectionDb();
        $collection = $conn->setConnection($table);
        $user = $collection->findOne(['_id'=>$postId]);
        $access = $user->access;    
        if($access == "private")
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    function commenting(EmailValidation $request)
    {
        $ch;
        $table = "users";
        $conn = new ConnectionDb();
        $collection = $conn->setConnection($table);
        $userId = 0;
        $email = $request->email;
        $token = $request->token;
        $check = self::checkLogged($email,$token);
        if($check == true)
        {
            $user = $collection->findOne(['email'=>$email]);
            $userId = $user->_id;    
        }
        $pid = $request->post_id;
        $postId = new \MongoDB\BSon\ObjectId($pid);
        $flag = self::checkAccess($postId);
        if($flag = true)
        {
            $ch = self::checkFriends($userId,$postId);
            if($ch == true)
            {
                $table = "comments";
                $conn = new ConnectionDb();
                $collection = $conn->setConnection($table);
                $comment = $request->comment;
                $file = $request->file('file')->store('comment');
                $post_id = $postId;
                $user_id = $userId;
                $document = array( 
                    "comment" => $comment,
                    "file" => $file, 
                    "post_id" => $postId, 
                    "user_id" => $userId
                );
                $collection->insertOne($document);
                echo "Comment Added";
            }
        }
        else
        {
            $table = "comments";
            $conn = new ConnectionDb();
            $collection = $conn->setConnection($table);
            $comment = new Comment();
            $comment = $request->comment;
            $comment->file = $request->file('file')->store('comment');
            // $post_id = $postId;
            // $user_id = $userId;
            $document = array( 
                "comment" => $comment,
                "file" => $file, 
                "post_id" => $postId, 
                "user_id" => $userId
            );
            $collection->insertOne($document);
            echo "Comment Added";
        }
    }
    function updateFile(EmailValidation $request)
    {
        $email = $request->email;
        $tokens = $request->token;
        $file = $request->file('file')->store('comment');
        $pid = $request->post_id;
        $postId = new \MongoDB\BSon\ObjectId($pid);
        $cid = $request->comment_id;
        $commentId = new \MongoDB\BSon\ObjectId($cid);
        $check = self::checkLogged($email,$tokens);
        if($check == true)
        {
            $table = "users";
            $conn = new ConnectionDb();
            $collection = $conn->setConnection($table);
            $data = $collection->findOne(['email'=>$email]); 
            $id = $data->_id;
            $table = "comments";
            $conn = new ConnectionDb();
            $collection = $conn->setConnection($table);
            $collection->updateOne(array("user_id"=>$id,"_id"=>$commentId), array('$set'=>array("file"=>$file)));
            echo "File Updated...";
       }   
    }
    function updateComment(EmailValidation $request)
    {
        $id;
        $email = $request->email;
        $tokens = $request->token;
        $comment = $request->comment;
        $pid = $request->post_id;
        $postId = new \MongoDB\BSon\ObjectId($pid);
        $cid = $request->comment_id;
        $commentId = new \MongoDB\BSon\ObjectId($cid);
        $check = self::checkLogged($email,$tokens);
        if($check == true)
        {
            $table = "users";
            $conn = new ConnectionDb();
            $collection = $conn->setConnection($table);
            $data = $collection->findOne(['email'=>$email]);
            $id = $data->_id;
            $table = "comments";
            $conn = new ConnectionDb();
            $collection = $conn->setConnection($table);
            $collection->updateOne(array("user_id"=>$id,"_id"=>$commentId), array('$set'=>array("comment"=>$comment)));
            echo "Comment updated...";
       }   
    }
    function deleteComment(EmailValidation $request)
    {
        $id;
        $table = "users";
        $conn = new ConnectionDb();
        $collection = $conn->setConnection($table);
        $email = $request->email;
        $tokens = $request->token;
        $cid = $request->comment_id;
        $commentId = new \MongoDB\BSon\ObjectId($cid);
        $check = self::checkLogged($email,$tokens);
        if($check == true)
        {
            $data = $collection->findOne(['email'=>$email]);
            $id = $data->_id;
            $table = "comments";
            $conn = new ConnectionDb();
            $collection = $conn->setConnection($table);
            $data = $collection->findOne(['_id'=>$commentId]);
            if($data!=NULL)
            {
                $d = $collection->deleteOne(['_id' => $commentId]);
                echo "Comment Deleted";
            }
            else
            {
                echo "Comment already deleted";
            }
        }   
    }
}
