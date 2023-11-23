@extends('layouts.api_index')

@section('content')
    {{-- <h2>生徒出席一覧</h2> --}}
    {{-- <div class="text-right">
        <a class="btn btn-success btn-sm mr-auto" href="{{route('stu-attend.create')}}">新規登録</a>
    </div> --}}

    <!-- 検索エリア -->
    <form  class="my-2 my-lg-0 ml-2" action="{{route('stu-attendserch')}}" method="GET">
        @csrf
        @method('get')
        <table>
            <div style="display:inline-flex">
                <div class="col-sm-4">
                    <a class="btn btn-success btn-sm" href="{{route('stu-attend.create')}}">新規</a>
                </div>
                <div class="col-sm-8">
                <input type="date" class="form-control" id="frdate" name="frdate" value="{{$frdate}}">
                </div>
                <div class="col-sm-4">
                    <button type="submit" class="btn btn-secondary btn-sm">検索</button>
                </div>
                {{-- <div class="col-sm-4">
                    <a class="btn btn-secondary btn-sm ml-2" href="{{route('stu-attendexport')}}">CSV出力</a>
                </div> --}}
            </div>

            <tr></tr>
        </table>
    </form>
    <form  class="my-2 my-lg-0 ml-2" action="{{route('stu-attendexport')}}" method="GET">
        @csrf
        @method('get')
        <table>
            <div style="display:inline-flex">

                <div class="col-sm-6">
                <input type="date" class="form-control" id="frdate" name="frdate" value="{{$frdate}}">
                </div>
                <div class="col-sm-6">
                    <input type="date" class="form-control" id="todate" name="todate" value="{{$todate}}">
                </div>
                <div class="col-sm-4">
                    <button type="submit" class="btn btn-secondary btn-sm">CSV出力</button>
                </div>

            </div>

            <tr></tr>
        </table>
    </form>

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
                    <th scope="col" class ="fixed01">生徒名</th>
                    <th scope="col" class ="fixed01">@sortablelink('eventdate', '登校日')</th>
                    <th scope="col" class ="fixed01">@sortablelink('status', '出欠')</th>
                    <th scope="col" class ="fixed01">開始</th>
                    <th scope="col" class ="fixed01">終了</th>
                    <th scope="col" class ="fixed01">操作</th>
                </tr>
            </thead>

            <tbody>
                @if($studentattendances->count())
                    @foreach($studentattendances as $studentattend)
                    <tr>
                        {{-- ID --}}
                        <td>{{ $studentattend->id }}</td>

                        {{-- 生徒名 --}}
                        @php
                            $fullname = $studentattend->last_name.' '.$studentattend->first_name
                        @endphp
                        <td>{{ $fullname }}</td>

                        {{-- 登校日 'Y-m-d H:i:s'--}}
                        @php
                            $str = "-";
                            if (isset($studentattend->eventdate)) {
                                $str = ( new DateTime($studentattend->eventdate))->format('Y-m-d');
                            }
                        @endphp
                        <td>{{ $str }}</td>

                        {{-- //出欠 App/Providers/AppServiceProviderのboot--}}
                        @foreach ($loop_atd_status as $loop_atd_status2)
                            @if ($loop_atd_status2['no']==$studentattend->status)
                                <td>{{ $loop_atd_status2['name'] }}</td>
                            @endif
                        @endforeach

                        {{-- 開始 'Y-m-d H:i:s'--}}
                        @php
                            $str = "-";
                            if (isset($studentattend->entrytime)) {
                                $str = ( new DateTime($studentattend->entrytime))->format('H:i');
                            }
                        @endphp
                        <td>{{ $str }}</td>

                        {{-- 終了 --}}
                        @php
                            $str = "-";
                            if (isset($studentattend->exittime)) {
                                $str = ( new DateTime($studentattend->exittime))->format('H:i');
                            }
                        @endphp
                        <td>{{ $str }}</td>

                        <td>
                            <div class="btn-toolbar">
                                <div class="btn-group me-2 mb-0">
                                <a class="btn btn-primary btn-sm" href="{{ route('stu-attend.edit',$studentattend->id)}}">編集</a>
                                </div>
                                <div class="btn-group me-2 mb-0">
                                    <form action="{{ route('stu-attend.destroy', $studentattend->id)}}" method="POST">
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

                    </tr>
                @endif

            </tbody>
        </table>

    </div>

     {{-- ページネーション / pagination）の表示 --}}
     <ul class="pagination justify-content-center">
        {{ $studentattendances->appends(request()->query())->render() }}
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
