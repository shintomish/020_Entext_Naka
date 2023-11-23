<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use DateTime;

class AwsSendSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'command:name';
    protected $signature = 'send:sms';

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Command description';
    protected $description = 'SMS送信';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('AwsSendSms handle ST');

        // パッケージ呼び出し
        $sns = App::make('aws')->createClient('sns');

        // 引数の値を取得
        $tel = "08032689820";
        // 引数の値を取得
        // $tel = $this->argument("tel");

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

        // 送信
        $sns->publish([
            'Message' => $message,
            'PhoneNumber' => '+81' . mb_substr($tel, 1), // 電話番号は国際電話番号表記に変更
            'MessageAttributes' => [
                'AWS.SNS.SMS.SenderID' => [
                    'DataType' => 'String',
                    'StringValue' => 'us-east-1' // <- 表示名
                ]
            ]
        ]);
        // Log::debug("AwsSendSms handle sns =" . print_r($sns->publish,true));
        Log::info('AwsSendSms handle ED');
        return 0;
    }
}
