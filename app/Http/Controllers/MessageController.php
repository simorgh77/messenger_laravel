<?php

namespace App\Http\Controllers;

use App\Events\ReceiveMessage;
use App\Events\SendMessage;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class MessageController extends Controller
{
    public function allMessages(User $user)
    {

       $currentUser=Auth::user();
       return Message::where("sender_id",$user->id)
           ->where("receiver_id",$currentUser->id)
           ->orWhere("sender_id",$currentUser->id)
           ->where("receiver_id",$user->id)->get();
    }


    public function store(User $user,Request $request)
    {
        $validated=$request->validate([
            'message'=>'required|string',
        ]);
        $currentUser=Auth::user();
         $message=  Message::create([
                "message"=>$request['message'],
                "sender_id"=>$currentUser->id,
                "receiver_id"=>$user->id
            ]);

    $event=event(new SendMessage($message,$user->id));
//     $event=event(new ReceiveMessage($message['message'],$user->id,$currentUser->id));
    return $message;
    }

}
