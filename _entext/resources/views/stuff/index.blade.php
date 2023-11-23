@extends('layouts.api_index')

@section('content')
    <h2>職員一覧</h2>
    <div class="text-right">
        <a class="btn btn-success btn-sm mr-auto" href="{{route('stuff.create')}}">新規登録</a>
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
                    <th scope="col" class ="fixed01">@sortablelink('custom_no', '管理No')</th>
                    <th scope="col" class ="fixed01">職員名</th>
                    <th scope="col" class ="fixed01">@sortablelink('sex', '性別')</th>
                    <th scope="col" class ="fixed01">@sortablelink('care_type', '職種')</th>
                    <th scope="col" class ="fixed01">@sortablelink('status', '状態')</th>
                    {{-- <th scope="col" class ="col-xs-3 col-md-1 bg-info text-right">@sortablelink('week_type', '曜日')</th> --}}
                    {{-- <th scope="col" class ="col-xs-3 col-md-1 bg-info text-right">@sortablelink('employment_type', '学年')</th> --}}
                    {{-- <th scope="col" class ="col-xs-3 col-md-1 bg-info text-right">@sortablelink('school_name', '学校名')</th> --}}
                    <th scope="col" class ="fixed01">入社日</th>
                    <th scope="col" class ="fixed01">開始</th>
                    <th scope="col" class ="fixed01">終了</th>
                    <th scope="col" class ="fixed01">操作</th>
                </tr>
            </thead>

            <tbody>
                @if($stuffs->count())
                    @foreach($stuffs as $stuff)
                    <tr>
                        {{-- ID --}}
                        <td>{{ $stuff->id }}</td>

                        {{-- 管理No --}}
                        <td>{{ $stuff->custom_no }}</td>

                        {{-- 職員名 --}}
                        @php
                            $fullname = $stuff->last_name.' '.$stuff->first_name
                        @endphp
                        <td>{{ $fullname }}</td>

                        {{-- //性別 App/Providers/AppServiceProviderのboot--}}
                        @foreach ($loop_sex as $loop_sex2)
                            @if ($loop_sex2['no']==$stuff->sex)
                                <td>{{ $loop_sex2['name'] }}</td>
                            @endif
                        @endforeach

                        {{-- //職種 App/Providers/AppServiceProviderのboot--}}
                        {{-- 1:一般,2:管理職,3:臨時,4:バイト --}}
                        @foreach ($loop_care_type_stuff as $loop_care_type_stuff2)
                            @if ($loop_care_type_stuff2['no']==$stuff->care_type)
                                <td>{{ $loop_care_type_stuff2['name'] }}</td>
                            @endif
                        @endforeach

                        {{-- 在職状態 --}}
                        @foreach ($loop_status_stuff as $loop_status_stuff2)
                            @if ($loop_status_stuff2['no']==$stuff->status)
                                <td>{{ $loop_status_stuff2['name'] }}</td>
                            @endif
                        @endforeach

                        {{-- 曜日 --}}
                        {{-- @foreach ($loop_week_type as $loop_week_type2)
                            @if ($loop_week_type2['no']==$stuff->week_type)
                                <td>{{ $loop_week_type2['name'] }}</td>
                            @endif
                        @endforeach --}}

                        {{-- 学年 --}}
                        {{-- @foreach ($loop_employment_type as $loop_employment_type2)
                            @if ($loop_employment_type2['no']==$stuff->employment_type)
                                <td>{{ $loop_employment_type2['name'] }}</td>
                            @endif
                        @endforeach --}}

                        {{-- 学校名 --}}
                        {{-- <td>{{ $stuff->school_name }}</td> --}}

                        {{-- 入社日 --}}
                        <td>{{ $stuff->joindate }}</td>

                        {{-- 開始 'Y-m-d H:i:s'--}}
                        @php
                            $str = "-";
                            if (isset($stuff->entrytime)) {
                                $str = ( new DateTime($stuff->entrytime))->format('H:i');
                            }
                        @endphp
                        <td>{{ $str }}</td>

                        {{-- 終了 --}}
                        @php
                            $str = "-";
                            if (isset($stuff->exittime)) {
                                $str = ( new DateTime($stuff->exittime))->format('H:i');
                            }
                        @endphp
                        <td>{{ $str }}</td>

                        <td>
                            <div class="btn-toolbar">
                                <div class="btn-group me-2 mb-0">
                                <a class="btn btn-primary btn-sm" href="{{ route('stuff.edit',$stuff->id)}}">編集</a>
                                </div>
                                <div class="btn-group me-2 mb-0">
                                    <form action="{{ route('stuff.destroy', $stuff->id)}}" method="POST">
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
                        {{-- <td><p> </p></td> --}}
                        {{-- <td><p> </p></td> --}}
                        {{-- <td><p> </p></td> --}}
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
        {{ $stuffs->appends(request()->query())->render() }}
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
