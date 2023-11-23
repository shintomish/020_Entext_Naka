@extends('layouts.api_index')

@section('content')
    <h2>生徒一覧</h2>
    <div class="text-right">
        <a class="btn btn-success btn-sm mr-auto" href="{{route('stu.create')}}">新規登録</a>
    </div>

    <div class="row">
        <!-- 検索エリア -->
        <!-- 検索エリア -->
    </div>

    {{-- Line --}}
    <hr class="mb-4">
    <style>
        /* スクロールバーの実装 */
        .table_sticky {
            display: block;
            overflow-y: scroll;
            /* height: calc(100vh/2); */
            height: 600px;
            border:1px solid;
            border-collapse: collapse;
        }
        .table_sticky thead th {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            left: 0;
            color: #fff;
            background: rgb(180, 226, 11);
            &:before{
                content: "";
                position: absolute;
                top: -1px;
                left: -1px;
                width: 100%;
                /* height: 100%; 2023/06/12 sortablelink対応 */
                height: 10%;
                border: 1px solid #ccc;
            }
        }

        table{
            width: 1800px;
        }
        th,td{
            width: 400px;   /* 200->400 */
            height: 10px;
            vertical-align: middle;
            padding: 0 15px;
            border: 1px solid #ccc;
        }
        .fixed01,
        .fixed02{
            /* position: -webkit-sticky; */
            position: sticky;
            top: 0;
            left: 0;
            color: rgb(8, 8, 8);
            background: #333;
            &:before{
                content: "";
                position: absolute;
                top: -1px;
                left: -1px;
                width: 100%;
                height: 100%;
                border: 1px solid #ccc;
            }
        }
        .fixed01{
            z-index: 2;
        }
        .fixed02{
            z-index: 1;
        }
    </style>

    <div class="table-responsive">

        {{-- <table class="table table-striped table-borderd table-scroll"> --}}
        <table class="table table-striped table-borderd table_sticky">
            <thead>
                <tr>
                    <th scope="col" class ="fixed01">ID</th>
                    <th scope="col" class ="fixed01">@sortablelink('custom_no','顧客No')</th>
                    <th scope="col" class ="fixed01">生徒名</th>
                    <th scope="col" class ="fixed01">@sortablelink('sex','性別')</th>
                    <th scope="col" class ="fixed01">@sortablelink('care_type','コース')</th>
                    <th scope="col" class ="fixed01">@sortablelink('status','状態')</th>
                    <th scope="col" class ="fixed01">@sortablelink('week_type','曜日')</th>
                    <th scope="col" class ="fixed01">@sortablelink('employment_type','学年')</th>
                    <th scope="col" class ="fixed01">@sortablelink('school_name','学校名')</th>
                    <th scope="col" class ="fixed01">入会日</th>
                    <th scope="col" class ="fixed01">開始</th>
                    <th scope="col" class ="fixed01">終了</th>
                    <th scope="col" class ="fixed01">操作</th>
                </tr>
            </thead>

            <tbody>
                @if($students->count())
                    @foreach($students as $student)
                    <tr>
                        {{-- ID --}}
                        <td>{{ $student->id }}</td>

                        {{-- 顧客No --}}
                        <td>{{ $student->custom_no }}</td>

                        {{-- 生徒名 --}}
                        @php
                            $fullname = $student->last_name.' '.$student->first_name
                        @endphp
                        <td>{{ $fullname }}</td>

                        {{-- //性別 App/Providers/AppServiceProviderのboot--}}
                        @foreach ($loop_sex as $loop_sex2)
                            @if ($loop_sex2['no']==$student->sex)
                                <td>{{ $loop_sex2['name'] }}</td>
                            @endif
                        @endforeach

                        {{-- //コース App/Providers/AppServiceProviderのboot--}}
                        @foreach ($loop_care_type as $loop_care_type2)
                            @if ($loop_care_type2['no']==$student->care_type)
                                <td>{{ $loop_care_type2['name'] }}</td>
                            @endif
                        @endforeach

                        {{-- 入会状態 --}}
                        @foreach ($loop_status as $loop_status2)
                            @if ($loop_status2['no']==$student->status)
                                <td>{{ $loop_status2['name'] }}</td>
                            @endif
                        @endforeach

                        {{-- 曜日 --}}
                        @foreach ($loop_week_type as $loop_week_type2)
                            @if ($loop_week_type2['no']==$student->week_type)
                                <td>{{ $loop_week_type2['name'] }}</td>
                            @endif
                        @endforeach

                        {{-- 学年 --}}
                        @foreach ($loop_employment_type as $loop_employment_type2)
                            @if ($loop_employment_type2['no']==$student->employment_type)
                                <td>{{ $loop_employment_type2['name'] }}</td>
                            @endif
                        @endforeach

                        {{-- 学校名 --}}
                        <td>{{ $student->school_name }}</td>

                        {{-- 入会日 --}}
                        <td>{{ $student->joindate }}</td>

                        {{-- 開始 'Y-m-d H:i:s'--}}
                        @php
                            $str = "-";
                            if (isset($student->entrytime)) {
                                $str = ( new DateTime($student->entrytime))->format('H:i');
                            }
                        @endphp
                        <td>{{ $str }}</td>

                        {{-- 終了 --}}
                        @php
                            $str = "-";
                            if (isset($student->exittime)) {
                                $str = ( new DateTime($student->exittime))->format('H:i');
                            }
                        @endphp
                        <td>{{ $str }}</td>

                        <td>
                            <div class="btn-toolbar">
                                <div class="btn-group me-2 mb-0">
                                <a class="btn btn-primary btn-sm" href="{{ route('stu.edit',$student->id)}}">編集</a>
                                </div>
                                <div class="btn-group me-2 mb-0">
                                    <form action="{{ route('stu.destroy', $student->id)}}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input class="btn btn-danger btn-sm" type="submit" value="削除" id="btn_del"
                                            onclick='return confirm("削除しますか？");'>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td><p>0件です。</p></td>
                        <td><p> </p></td>
                        <td><p> </p></td>
                        <td><p> </p></td>
                        <td><p> </p></td>
                        <td><p> </p></td>
                        <td><p> </p></td>
                        <td><p> </p></td>
                        <td><p> </p></td>
                        <td><p> </p></td>
                        <td><p> </p></td>
                        <td><p> </p></td>
                        <td><p> </p></td>

                    </tr>
                @endif

            </tbody>
        </table>

    </div>

     {{-- ページネーション / pagination）の表示 --}}
     <ul class="pagination justify-content-center">
        {{ $students->appends(request()->query())->render() }}
     </ul>

@endsection

@section('part_javascript')
{{-- ChangeSideBar("nav-item-system-user"); --}}
    <script type="text/javascript">
            // $('.btn_del').click(function()
            //     if( !confirm('本当に削除しますか？') ){
            //         /* キャンセルの時の処理 */
            //         return false;
            //     }
            //     else{
            //         /*　OKの時の処理 */
            //         return true;
            //     }
            // });
    </script>
@endsection
