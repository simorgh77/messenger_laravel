<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class MessageController extends Controller
{
    public function receivingMessages(User $user)
    {

       $currentUser=Auth::user();
       return Message::where("sender_id",$user->id)
           ->where("receiver_id",$currentUser->id)->get();
    }

    public function sendingMassages(User $user)
    {
        $currentUser=Auth::user();
        return Message::where("sender_id",$currentUser->id)
            ->where("receiver_id",$user->id)->get();
    }

    public function store(User $user,Request $request)
    {

        $validated=$request->validate([
            'message'=>'required"string',
        ]);
        $currentUser=Auth::user();
            Message::create([
                "message"=>$request['message'],
                "sender_id"=>$currentUser->id,
                "receiver_id"=>$user->id
            ]);

        return Redirect::route("/dashboard");
    }

}
