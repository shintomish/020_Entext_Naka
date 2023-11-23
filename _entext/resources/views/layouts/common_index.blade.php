<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="generator" content="">

        <!-- favicon.ico -->
        <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Tytle -->
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
        <script src="{{ asset('js/jquery-3.6.0.min.js') }}" defer></script>

        <!-- Bootstrap core CSS -->
        {{-- <link href="{{ asset('css/back/bootstrap.min.css') }}" rel="stylesheet"> --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

        <!-- Fonts -->
        {{-- <link rel="dns-prefetch" href="//fonts.gstatic.com"> --}}
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="{{ asset('css/back/dashboard.css') }}" rel="stylesheet">

        <!-- flash_message -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        {{-- プラグイン(pace.min.js center-atom.css) loading-bar.css center-circle.css--}}
        <script type="text/javascript" src="{{ asset('js/back/pace.min.js') }}"></script>
        <link href="{{ asset('css/back/center-circle_d.css') }}" rel="stylesheet">

        <!-- Place your kit's code here -->
        {{-- <script src="https://kit.fontawesome.com/376cff10ff.js" crossorigin="anonymous"></script> --}}
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">

        <style>
            .bd-placeholder-img {
                font-size: 1.125rem;
                text-anchor: middle;
                -webkit-user-select: none;
                -moz-user-select: none;
                user-select: none;
            }
            @media (min-width: 768px) {
                .bd-placeholder-img-lg {
                font-size: 3.5rem;
                }
            }
        </style>

    </head>

    <body>
        <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="{{ route('top') }}">{{ config('app.name', 'Laravel') }}</a>
            <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="form-control bg-dark w-100" type="text" placeholder="" aria-label="Search"></div>
            <ul class="navbar-nav px-3">
                {{-- 2021/11/19 変更 --}}
                {{-- <li class="nav-item text-nowrap">
                    <a class="nav-link" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">{{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li> --}}
                <li class="nav-item text-nowrap">
                    <a class="nav-link" href="{{ route('logout') }}" onclick="return logout(event);">
                        <span class="text-danger">
                            {{-- <i class="fa fa-fw fa-sign-out"></i>ログアウト --}}
                            <i class="fa fa-sign-out-alt"></i> ログアウト
                         </span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <script type="text/javascript">
                        function logout(event){
                                event.preventDefault();
                                var check = confirm("ログアウトしますか？");
                                if(check){
                                   document.getElementById('logout-form').submit();
                                }
                         }
                    </script>
                </li>
            </ul>
        </header>

        <div class="container-fluid">
            <div class="row">
                <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link active">
                                    <i class="fas fa-address-card"></i>
                                    <?php $user = Auth::user(); ?>{{ $user->name }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link">
                                    <a class="nav-link" href="{{route('top')}}">
                                    <i class="fas fa-laptop-house"></i>
                                    ホーム
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('organization.index')}}">
                                    <i class="fas fa-user-alt"></i>
                                    組織
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('user.index')}}">
                                    <i class="fas fa-user-friends"></i>
                                    利用者管理
                                </a>
                            </li>
                            <li class="nav-item">

                            </li>
                            <li class="nav-item">
                            </li>
                            <li class="nav-item">
                                </a>
                            </li>

                        </ul>

                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                            <span>業務管理</span>
                            <a class="link-secondary" href="#" aria-label="Add a new report">
                              <span data-feather="plus-circle"></span>
                            </a>
                        </h6>

                        <ul class="nav flex-column mb-2">

                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-tasks"></i>
                                    イベント作成
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-clipboard"></i>
                                    旬の野菜作成
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-wallet"></i>
                                    レシピ作成
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-wrench"></i>
                                    更新情報
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

                        <!-- 検索エリア -->
                        @switch ($common_no)
                            @case ('00_1')
                                <!-- タイトル -->
                                {{-- <h3>利用者管理</h3>
                                <form  class="form-inline my-2 my-lg-0 ml-2" action="{{route('userserch')}}" method="GET"> --}}
                                @break;
                            @case ('00_2')
                                <!-- タイトル -->
                                {{-- <h3>顧客管理</h3>
                                <form  class="form-inline my-2 my-lg-0 ml-2" action="{{route('customerserch')}}" method="GET"> --}}
                                @break;
                            @case ('00_3')
                                <!-- タイトル -->
                                <!-- TOP -->
                                @break;
                            @case ('00_4')
                                <!-- タイトル -->
                                {{-- <h3>複数法人</h3>
                                <form  class="form-inline my-2 my-lg-0 ml-2" action="{{route('ctluserserch')}}" method="GET"> --}}
                                @break;
                            @case ('01')
                                <!-- layouts.upload 検索が日付でdatapickを使用-->
                                {{-- <h3>アップロードユーザー一覧</h3> --}}
                                @break;
                            @case ('02')
                                <!-- layouts.costomer-->
                                {{-- <h3>NEWS作成</h3> --}}
                                @break;
                            <!-- 03以降 顧客名検索 -->
                            @case ('03')
                                @break;
                            @case ('04')
                                @break;
                            @case ('05')
                                @break;
                            @case ('06')
                                @break;
                            @case ('07')
                                @break;
                            @default:
                                @break;
                        @endswitch

                        {{-- <form  class="form-inline my-2 my-lg-0 ml-2" action="{{route('wokprocbookserch')}}" method="GET"> --}}
                            @csrf
                            @method('get')
                            <div class='btn-toolbar' role="toolbar">
                                <div class="input-group">
                                </div>
                            </div>
                        </form>
                        <!-- 検索エリア -->
                    </div>

                    <!-- フラッシュメッセージ -->
                    @include('components.toastr')

                    <div class="container">
                        @yield('content')
                    </div>


                </main>
            </div>
        </div>

        <script src="{{ asset('js/back/bootstrap.bundle.min.js') }}" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha384-zNy6FEbO50N+Cg5wap8IKA4M/ZnLJgzc6w2NqACZaK0u0FXfOWRRJOnQtpZun8ha" crossorigin="anonymous"></script>
        <script src="{{ asset('js/back/dashboard.js') }}"></script>

    </body>

</html>
