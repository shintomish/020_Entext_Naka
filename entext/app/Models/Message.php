<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;
    use SerializeDate;
    
    protected $guarded = ['id'];

    // 参照させたいSQLのテーブル名を指定
    protected $table = 'messages';

    // messagesテーブルのcreated_atの値をY-m-d H:i:sの形で参照したい場合
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
