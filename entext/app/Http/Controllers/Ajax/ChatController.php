<?php

namespace App\Http\Controllers\Ajax;

// use App\Models\User;
use App\Models\Message;
use App\Events\MessageCreated;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() { // 新着順にメッセージ一覧を取得

        Log::info('Ajax index START');

        // return Message::orderBy('id', 'desc')->get();
        return Message::with('user')->orderBy('id', 'desc')->get();

        Log::info('Ajax index END');

    }

    public function create(Request $request) { // メッセージを登録

        Log::info('Ajax create START');
        // $message = Message::create([
        //     'body' => $request->message
        // ]);

        // event(new MessageCreated($message));
        // broadcast(new MessageCreated($message))->toOthers();
        // broadcast(new MessageCreated($message));

        $user = Auth::user();

        $message = $user->messages()->create([
            'body' => $request->input('message'),
            'customer_id' => $user->id
        ]);

        Log::info('Ajax create END');
        // broadcast(new MessageCreated($user, $message))->toOthers();
        broadcast(new MessageCreated($user, $message));

        // return ['status' => 'Message Sent!'];

    }
}
