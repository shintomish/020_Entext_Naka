<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;         //追記 並び替えをcolumn-sortable使って

class StudentAttendance extends Model
{
    use HasFactory, Notifiable;
    use Sortable;                   //追記

    // 参照させたいSQLのテーブル名を指定
    protected $table = 'studentattendances';

    // 追記(ソートに使うカラムを指定
    public $sortable = [
    	'id',
        'eventdate',
        'status',
    ];

    protected $fillable = [
        'created_at',
    ];

}
