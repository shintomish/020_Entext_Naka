@extends('layouts.main')

@section('content')
<div class="content-wrapper">
    <!-- ---------------------------------------------------------------------- -->
    <!-- Content Header (Page header)                                           -->
    <!-- ---------------------------------------------------------------------- -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 id="page-title" class="m-0 text-dark">保育士の管理</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- ---------------------------------------------------------------------- -->
    <!-- Main content                                                           -->
    <!-- ---------------------------------------------------------------------- -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- card content 保育士の出席簿 -->
                    <div id="card_kid_list" class="card">
                        <div class="card-header">
                            <h3 class="card-title">保育士の勤務表 編集</h3>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <form id="form_teacher_attendance" method="GET" action="{{ route('teacher-attendance.input') }}">
                                <input type="hidden" id="submit_type" name="submit_type" value="search">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <!-- select -->
                                        <div class="form-group">
                                            <label>保育士の状態</label>
                                            <select class="custom-select " id="search_status_id" name="search_status_id">
                                                <option value="0" {{ $search_status_id == 0 ? 'selected' : '' }}>ALL</option>
                                                <option value="1" {{ $search_status_id == 1 ? 'selected' : '' }}>在籍中</option>
                                                <option value="2" {{ $search_status_id == 2 ? 'selected' : '' }}>休職中</option>
                                                <option value="3" {{ $search_status_id == 3 ? 'selected' : '' }}>退職済</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <!-- select -->
                                        <div class="form-group">
                                            <label>保育士</label>
                                            <select class="custom-select" id="search_teacher_id" name="search_teacher_id">
                                                <option value="0" ></option>
                                                @foreach($teachers as $teacher)
                                                    <option value="{{$teacher->id}}" {{ $search_teacher_id == $teacher->id ? 'selected' : '' }}>
                                                        {{$teacher->last_name}} {{$teacher->first_name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-1">
                                        <div class="form-group">
                                            <label>年</label>
                                            <select class="custom-select" id="search_year" name="search_year">
                                                @foreach($years as $year)
                                                    <option value="{{$year->year}}" {{ $year->year == $search_year ? 'selected' : '' }}>{{$year->year}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-1">
                                        <div class="form-group">
                                            <label>月</label>
                                            <select class="custom-select" id="search_month" name="search_month">
                                                @for ($i = 1; $i <= 12; $i++)
                                                    <option value="{{$i}}" {{ $search_month == $i ? 'selected' : '' }}>{{$i}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                    </div>

                                    <div class="col-md-1">
                                        <button type="button" id="btn_search" class="btn btn-block btn-primary">検索</button>
                                    </div>
                                    
                                    <div class="col-md-1">
                                    </div>

                                    <div class="col-md-1">
                                        <button type="button" id="btn_save" class="btn btn-block btn-success">保存</button>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" id="btn_create" class="btn btn-block btn-warning">作成</button>
                                    </div>

                                </div>

                                <div id="" class="dataTables_wrapper dt-bootstrap4">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6"></div>
                                        <div class="col-sm-12 col-md-6"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                        <table id=""
                                            class="table-valign-middle table table-bordered table-hover dataTable dtr-inline" role="grid"
                                            aria-describedby="example2_info">
                                            <thead>
                                                <tr role="row">
                                                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                                                    aria-label="Platform(s): activate to sort column ascending">日付</th>
                                                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                                                    aria-label="Platform(s): activate to sort column ascending">担当</th>
                                                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                                                    aria-label="Engine version: activate to sort column ascending">出勤時刻</th>
                                                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                                                    aria-label="Engine version: activate to sort column ascending">退勤時刻</th>
                                                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                                                    aria-label="Engine version: activate to sort column ascending">休憩時間</th>
                                                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                                                    aria-label="Engine version: activate to sort column ascending">実働時間</th>
                                                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                                                    aria-label="Engine version: activate to sort column ascending">状態</th>
                                                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                                                    aria-label="Engine version: activate to sort column ascending">コメント</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach($teacher_attendances as $teacher_attendance)
                                                <tr role="row">
                                                    <td>
                                                        <input type="hidden" name="id[]" value="{{ $teacher_attendance->id }}">
                                                        <input type="date" readonly class="form-control" name="eventdate[]" id="eventdate_{{ $teacher_attendance->eventdate }}" value="{{ $teacher_attendance->eventdate }}">
                                                    </td>
                                                    <td>
                                                        <select class="custom-select" name="Responsible[]">
                                                            <option value="0"></option>
                                                            <option value="1" {{ $teacher_attendance->care_type == 1 ? 'selected' : '' }}>普通科</option>
                                                            <option value="2" {{ $teacher_attendance->care_type == 2 ? 'selected' : '' }}>保育科</option>
                                                        </select>
                                                    </td>
                                                    
                                                    <td>
                                                        <input type="time" class="form-control" name="entrytime[]" id="entrytime_{{ $teacher_attendance->eventdate }}" value="{{ $teacher_attendance->entrytime }}">
                                                    </td>
                                                    
                                                    <td>
                                                        <input type="time" class="form-control" name="exittime[]" id="exittime_{{ $teacher_attendance->eventdate }}" value="{{ $teacher_attendance->exittime }}">
                                                    </td>
                                                    
                                                    <td>
                                                        <input type="time" class="form-control" name="breaktime[]" id="breaktime_{{ $teacher_attendance->eventdate }}" value="{{ $teacher_attendance->breaktime }}">
                                                    </td>

                                                    <td>
                                                        <input type="time" readonly class="form-control" name="worktime[]" id="worktime_{{ $teacher_attendance->eventdate }}" value="{{ $teacher_attendance->worktime }}">
                                                    </td>

                                                    <td>
                                                        <select class="custom-select"  name="status[]" id="status_{{ $teacher_attendance->eventdate }}">
                                                            <option value="0" {{ $teacher_attendance->status == 0 ? 'selected' : '' }}></option>
                                                            <option value="1" {{ $teacher_attendance->status == 1 ? 'selected' : '' }}>通常</option>
                                                            <option value="2" {{ $teacher_attendance->status == 2 ? 'selected' : '' }}>遅刻</option>
                                                            <option value="3" {{ $teacher_attendance->status == 3 ? 'selected' : '' }}>早退</option>
                                                            <option value="4" {{ $teacher_attendance->status == 4 ? 'selected' : '' }}>休暇</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="comment[]" id="comment_{{ $teacher_attendance->eventdate }}" value="{{ $teacher_attendance->comment }}">
                                                    </td>
                                                </tr>
                                                @endforeach
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                        
                                <div class="row justify-content-center">
                                    {{ $teacher_attendances->appends([
                                        'search_teacher_id' => $search_teacher_id
                                        ,'search_status_id' => $search_status_id
                                        ,'search_year' => $search_year
                                        ,'search_month' => $search_month ])->links() }}
                                </div>
                            </form>

                        </div>
                        <!-- /.card-body -->

                    </div>
                    <!-- /.card -->

                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
@endsection

@section('part_javascript')
ChangeSideBar("nav-item-teacher-attendance_input");

$('.btn_del').click(function(){
    if( !confirm('本当に削除しますか？') ){
        /* キャンセルの時の処理 */
        return false;
    }
    else{
        /*　OKの時の処理 */
        return true;
    }
});

$('input[name="entrytime[]"]').change( function(e){
    var $value = $(this).attr("id");
    $eventdate = $value.replace( /entrytime/g , "" ) ;
    retval = calc_work_time($eventdate);
    $('#worktime' + $eventdate ).val(retval);
});
$('input[name="exittime[]"]').change( function(e){
    var $value = $(this).attr("id");
    $eventdate = $value.replace( /exittime/g , "" ) ;
    retval = calc_work_time($eventdate);
    $('#worktime' + $eventdate ).val(retval);
});
$('input[name="breaktime[]"]').change( function(e){
    var $value = $(this).attr("id");
    $eventdate = $value.replace( /breaktime/g , "" ) ;
    retval = calc_work_time($eventdate);
    $('#worktime' + $eventdate ).val(retval);
});

// date: 日付オブジェクト
// format: 書式フォーマット
function formatDate (date, format) {
    format = format.replace(/yyyy/g, date.getFullYear());
    format = format.replace(/MM/g, ('0' + (date.getMonth() + 1)).slice(-2));
    format = format.replace(/dd/g, ('0' + date.getDate()).slice(-2));
    format = format.replace(/HH/g, ('0' + date.getHours()).slice(-2));
    format = format.replace(/mm/g, ('0' + date.getMinutes()).slice(-2));
    format = format.replace(/ss/g, ('0' + date.getSeconds()).slice(-2));
    format = format.replace(/SSS/g, ('00' + date.getMilliseconds()).slice(-3));
    return format;
  }

function calc_work_time( eventdate ){
    var $id_entrytime   = "#entrytime" + eventdate;
    var $id_exittime    = "#exittime"  + eventdate;
    var $id_breaktime   = "#breaktime" + eventdate;
    var $id_worktime    = "#worktime"  + eventdate;

    var val_entrytime = $( $id_entrytime ).val();
    var val_exittime  = $( $id_exittime ).val();
    var val_breaktime = $( $id_breaktime ).val();
    var val_worktime  = $( $id_worktime ).val();

    basetime = new Date('2020/01/01 00:00:00');

    entrytime = new Date('2020/01/01 ' + val_entrytime);
    exittime = new Date('2020/01/01 ' + val_exittime);
    breaktime = new Date('2020/01/01 ' + val_breaktime);
    worktime_ms = exittime.getTime() - entrytime.getTime() - (breaktime.getTime() - basetime.getTime());

    var hours = worktime_ms / ( 1000 * 60 * 60);    // 時間の計算
    worktime_ms = worktime_ms % ( 1000 * 60 * 60 ); // 算出した時間を取り除く
    var mins = worktime_ms / ( 1000 * 60 );         // 分の計算
    worktime_ms = worktime_ms % ( 1000 * 60 );      // 算出した分を取り除く
    var secs = worktime_ms / 1000;                  // 秒の計算

    worktime = ('0' + Math.floor(hours)).slice(-2) + ":" + ('0' + Math.floor(mins)).slice(-2) + ":" + ('0' + Math.floor(secs)).slice(-2);

    var msg = "";
    msg += 'exittime.getTime()  = ' + exittime.getTime() + "\r\n";
    msg += 'entrytime.getTime() = ' + entrytime.getTime() + "\r\n";
    msg += 'breaktime.getTime() = ' + breaktime.getTime() + "\r\n";
    msg += 'entrytime = ' + entrytime.getHours() +":" + entrytime.getMinutes()+":" + entrytime.getSeconds() + "\r\n";
    msg += 'exittime  = ' + exittime.getHours() +":" + exittime.getMinutes()+":" + exittime.getSeconds() + "\r\n";
    msg += 'breaktime = ' + breaktime.getHours() +":" + breaktime.getMinutes()+":" + breaktime.getSeconds() + "\r\n";
    msg += 'worktime_ms  = ' + worktime_ms + "\r\n";
    msg += 'worktime  = ' + worktime;

    return worktime;
}

//---------------------------------------------------------------
//--保育士の状態プルダウン変更イベントハンドラ
//---------------------------------------------------------------
$('#search_status_id').change( function(e){

    var reqData = new FormData();
    reqData.append( "search_status_id", $(this).val() );

    dispLoading("処理中...");

    // Ajax通信呼出(データファイルのアップロード)
    AjaxAPI.callAjax( 
        "{{ route('TeacherAttendance.getTeachers') }}",
        reqData,
        function (res) {
            // クラスプルダウンの初期化
            $('#search_teacher_id option').remove();

            // クラスプルダウン項目の追加
            $.each( res[0].teachers, function(id, obj){
                var teacher_name = obj.last_name + ' ' + obj.first_name;
                $('#search_teacher_id').append( $('<option>').text(teacher_name).attr('value',obj.id) );
            });
            dispLoading("成功しました");
        }
    );

    return false;
});

//---------------------------------------------------------------
//--年/月プルダウン変更イベントハンドラ
//---------------------------------------------------------------
$('#search_year').change( function(e){
    change_day_puldown_list();
});
$('#search_month').change( function(e){
    change_day_puldown_list();
});
function change_day_puldown_list( year, month ){
    // 日プルダウンの要素を年月に連動する。
    // 選択中の「日」が範囲外の場合は「0」を選択する

    var select_year     = Number($("#search_year").val());
    var select_month    = Number($("#search_month").val());
    var select_day      = Number($("#search_day").val());

    // プルダウンの初期化
    $('#search_day option').remove(); 

    // 指定年月から末日を計算
    var last_date   = new Date(Number(select_year), Number(select_month), 1);
    last_date.setDate(last_date.getDate() - 1)
    var last_day    = last_date.getDate();

    // 日プルダウン要素を再構築
    $('#search_day').append( $('<option>').text('ALL').attr('value',0) );
    for(var i = 1; i <= last_day; i++ ){
        var isSelected =  (i == select_day) ? true : false;
        $option = $('<option>').text(i)
                               .attr('value',i)
                               .prop('selected', isSelected);
        $('#search_day').append( $option );
    }
}
change_day_puldown_list();

var FORM_CHANGED = false;

//---------------------------------------------------------------
//--フォーム要素の変更イベントハンドラ
//---------------------------------------------------------------
$('#form_teacher_attendance').change(function(e){
    var target = $( e.target );

    // search_status_id
    // search_teacher_id
    // search_year
    // search_month
    if(    target.attr('id') === 'search_status_id'
        || target.attr('id') === 'search_teacher_id'
        || target.attr('id') === 'search_year'
        || target.attr('id') === 'search_month' ){
        // 何もしない
    }
    else{
        FORM_CHANGED = true;
    }
});

//---------------------------------------------------------------
//--検索ボタン押下イベントハンドラ
//---------------------------------------------------------------
$('#btn_search').click(function(){
    if( FORM_CHANGED ){
        BootstrapMessage.confirm({
            message: "フォームの内容が変更されています。破棄しますか？",
            title: "確認メッセージ",
            caption_ok: "はい",
            caption_cancel: "いいえ",
            funcOk: function(){
                dispLoading("loading..."); 
                $('#submit_type').val('search');
                $('#form_teacher_attendance').submit();
            },
        });
    }
    else{
        dispLoading("loading..."); 
        $('#submit_type').val('search');
        $('#form_teacher_attendance').submit();
    }
});

//---------------------------------------------------------------
//--保存ボタン押下イベントハンドラ
//---------------------------------------------------------------
$('#btn_save').click(function(){
    dispLoading("loading..."); 
    $('#submit_type').val('save');
    $('#form_teacher_attendance').submit();
});

//---------------------------------------------------------------
//--作成ボタン押下イベントハンドラ
//---------------------------------------------------------------
$('#btn_create').click(function(){
    if( FORM_CHANGED ){
        BootstrapMessage.confirm({
            message: "フォームの内容が変更されています。破棄しますか？",
            title: "確認メッセージ",
            caption_ok: "はい",
            caption_cancel: "いいえ",
            funcOk: function(){
                dispLoading("loading..."); 
                $('#submit_type').val('search');
                $('#form_teacher_attendance').submit();
            },
        });
    }
    else{
        dispLoading("loading..."); 
        $('#submit_type').val('create');
        $('#form_teacher_attendance').submit();
    }
});

@endsection
