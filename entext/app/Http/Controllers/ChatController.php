<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {

        // return Message::orderBy('id', 'desc')->get();
        // $messages = Message::orderBy('id', 'desc')->get();
        $messages = Message::with('user')->orderBy('id', 'desc')->get();

        $common_no = '00_7';
        $compacts = compact( 'messages','common_no' );
        return view('chat.index', $compacts );
    }
}
