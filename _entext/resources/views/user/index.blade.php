{{-- @extends('layouts.app') --}}
@extends('layouts.customer')

@section('content')
    <h2>利用者一覧</h2>
    <div class="text-right">
        <a class="btn btn-success btn-sm mr-auto" href="{{route('user.create')}}">新規登録</a>
    </div>

    <div class="table-responsive">

        <table class="table table-striped table-borderd">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">@sortablelink('name', 'ユーザー名')</th>
                    <th scope="col">@sortablelink('email', 'メールアドレス')</th>
                    <th scope="col">所属組織</th>
                    <th scope="col">@sortablelink('login_flg', '利用区分')</th>
                    <th scope="col">@sortablelink('admin_flg', '管理区分')</th>
                    <th scope="col">操作</th>
                </tr>
            </thead>

            <tbody>
                @if($users->count())
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>

                        <td>
                            @foreach ($organizations as $organization2)
                                @if ($organization2->id==$user->organization_id)
                                    {{$organization2->name}}
                                @endif
                            @endforeach
                        </td>

                            {{-- @php
                                echo var_dump($loop_login_flg);
                            @endphp --}}
                            {{-- //利用区分 --}}
                            @foreach ($loop_login_flg as $loop_login_flg2)
                                @if ($loop_login_flg2['no']==$user->login_flg)
                                    <td>{{ $loop_login_flg2['name'] }}</td>
                                @endif
                            @endforeach

                            {{-- //管理区分 --}}
                            @foreach ($loop_admin_flg as $loop_admin_flg2)
                                @if ($loop_admin_flg2['no']==$user->admin_flg)
                                    <td>{{ $loop_admin_flg2['name'] }}</td>
                                @endif
                            @endforeach

                        <td>
                            <div class="btn-toolbar">
                                <div class="btn-group me-2 mb-0">
                                    <a class="btn btn-primary btn-sm" href="{{ route('user.edit',$user->id)}}">編集</a>
                                </div>
                                <div class="btn-group me-2 mb-0">
                                    <form action="{{ route('user.destroy', $user->id)}}" method="POST">
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
       {{-- {{ $users->render() }} --}}
       {{ $users->appends(request()->query())->render() }}
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
            //         /* OKの時の処理 */
            //         return true;
            //     }
            // });
    </script>
@endsection
