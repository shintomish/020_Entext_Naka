<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;         //追記 並び替えをcolumn-sortable使って

class Student extends Model
{
    use HasFactory, Notifiable;
    use Sortable;                   //追記

    // 参照させたいSQLのテーブル名を指定
    protected $table = 'students';

    // 追記(ソートに使うカラムを指定
    public $sortable = [
        'last_name',
        'first_name',
        'sex',
        'care_type',
        'joindate',
        'custom_no',
        'employment_type',
        'week_type',
    	'id',
        'week_type',
        'school_name',
        'status',
    ];

    protected $fillable = [
        'created_at',
    ];

}
