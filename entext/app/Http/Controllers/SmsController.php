<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client; // 追加
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Notifications\Notification;
use Validator;
use DateTime;

class SmsController extends Controller
{
    public function index1()
    {
        $sid    = env( 'TWILIO_SID' );
        $token  = env( 'TWILIO_TOKEN' );
        $twilioNumber = env('TWILIO_PHONE_NUMBER');

        // $sid    = 'AC9e3d59dda6c35b6f6d891df363afe798';
        // $token  = 'e9a288026bcb4d4156419217954e8777';
        // $twilioNumber = '18645287824';

        Log::info('SmsController index START');

        $message = "\n";
        $message = $message . "\nAizen\n";
        $message = $message . "◆パソコン教室◆\n";
        $message = $message . "\n藤崎 翔平";
        $message = $message . " さんが、\n";
        $message = $message . "21:47:00";
        $message = $message . "\n";
        $message = $message . "に、退室しました。";

        $number  = '+81' . '08032689820'; //送信したい電話番号。+81とかから。
        // $number  = '+81' . '08059709715'; //送信したい電話番号。+81とかから。

        Log::debug('SmsController message = ' . print_r($message,true));

        $client = new Client( $sid, $token );

            $client->messages->create(
                $number,
                [
                    "body" => $message,
                    "from" => env( 'TWILIO_FROM' )
                ]
            );
            Log::info('Message sent to ' . $number);

        Log::info('SmsController index END');
        // $client->messages->create(
        //     $number,
        //     [
        //         'from' => env( 'TWILIO_FROM' ),
        //         'body' => $message,
        //     ]
        // );
    }

    public function index2() {

        Log::info('SmsController index ST');

        $id     = env( 'NEXMO_KEY');
        $token  = env( 'NEXMO_SECRET' );
        $from   = env( 'NEXMO_FROM' );
        // $to     = '81' . '8032689820'; //送信したい電話番号。+81から0省く。
        $to  = '+81' . '8059709715'; //fujisaki電話番号。+81から0省く。
        // $to  = '+81' . '8063474683'; //sueoka電話番号。+81から0省く。

        $basic  = new \Vonage\Client\Credentials\Basic($id, $token);
        $client = new \Vonage\Client($basic);

        $nowDate = new DateTime('now');
        $date_now = $nowDate->format('Y-m-d');
        $time_now = $nowDate->format('H:i:s');

        $message = "";
        $message = $message . "Aizen\n";
        $message = $message . "◆パソコン教室◆\n";
        $message = $message . "\n新冨 泰明";
        $message = $message . " さんが、\n";
        $message = $message . $time_now;
        $message = $message . "\n";
        $message = $message . "に、退室しました。\n";

        Log::info('SmsController index new Client');
        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS($from, $to, $message)
        );

        // \Notification::route('nexmo', $to)
        //         ->notify(new SmsNotification());

        $message = $response->current();
        if ($message->getStatus() == 0) {
            Log::info("The message was sent successfully");
        } else {
            Log::debug("The message failed with status: = " . print_r($message->getStatus(),true));
        }

        Log::info('SmsController index ED');
    }

    public function index() {
        Log::info('SmsController index ST');

        // パッケージ呼び出し
        $sns = App::make('aws')->createClient('sns');

        // 引数の値を取得
        $tel = "08032689820";

        $nowDate = new DateTime('now');
        $date_now = $nowDate->format('Y-m-d');
        $time_now = $nowDate->format('H:i:s');

        // メッセージ作成
        $message = "";
        $message = $message . "Aizen\n";
        $message = $message . "◆パソコン教室◆\n";
        $message = $message . "\n新冨 泰明";
        $message = $message . " さんが、\n";
        $message = $message . $time_now;
        $message = $message . "\n";
        $message = $message . "に、入室しました。\n";

        // AWSよりSMS送信
        // Artisan::call('send:sms', [
        //     'tel' => $tel
        // ]);

        // 送信
        $sns->publish([
            'Message' => $message,
            'PhoneNumber' => '+81' . mb_substr($tel, 1), // 電話番号は国際電話番号表記に変更
            'MessageAttributes' => [
                'AWS.SNS.SMS.SenderID' => [
                    'DataType' => 'String',
                    'StringValue' => 'Entext' // <- 表示名
                ]
            ]
        ]);

        Log::info('SmsController index ED');
    }
}
