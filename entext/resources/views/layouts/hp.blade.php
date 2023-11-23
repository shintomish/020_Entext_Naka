<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="創業100周年を迎える老舗スーパー">
    <meta name="keywords" content="">

    <!-- Tytle -->
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- favicon.ico -->
    <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Custom styles for this template -->
    <link href="{{ asset('css/spma/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/spma/inview.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ asset('js/spma/openclose.js') }}" defer></script>
    <script src="{{ asset('js/spma/fixmenu.js') }}" defer></script>
    <script src="{{ asset('js/spma/fixmenu_pagetop.js') }}" defer></script>

    <!-- Place your kit's code here -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">

</head>

<body class="home">

    <header>
        <div class="inner">
            <h1 id="logo"><a href="{{ route('login') }}"><img src="{{ asset('images/logo_kanemo.png') }}" alt="スーパーカネモ"></a></h1>
            <ul id="header-nav">
            </ul>
        </div>
    </header>

    <!--PC用（901px以上端末）メニュー-->
    <nav id="menubar" class="nav-fix-pos">
        <ul class="inner">
            <li class="info1"><a href="{{ route('info') }}"><i class="fas fa-house-user"></i>
                {{-- <i class="fas fa-laptop-house"></i> --}}
                店内のご案内<span>Information</span></a></li>
            <li class="event1"><a href="{{ route('event') }}"><i class="fas fa-tasks"></i>
                イベント<span>Event</span></a></li>
            <li class="recipe1"><a href="{{ route('recipe') }}"><i class="fas fa-utensils"></i>
                旬のレシピ<span>Recipe</span></a></li>
            <li class="company1"><a href="{{ route('company') }}"><i class="fas fa-user-alt"></i>
                会社概要<span>Company</span></a></li>
            {{-- <li class="map"><a href="map.html">周辺マップ<span>Map</span></a></li> --}}
            <li class="contact1"><a href="{{ route('contact') }}"><i class="fas fa-envelope"></i>
                お問い合わせ<span>Contact</span></a></li>
        </ul>
    </nav>
    <!--小さな端末用（900px以下端末）メニュー-->
    <nav id="menubar-s">
        <ul>
            <li class="info1"><a href="{{ route('info') }}"><i class="fas fa-house-user"></i>
                {{-- <i class="fas fa-laptop-house"></i> --}}
                店内のご案内<span>Information</span></a></li>
            <li class="event1"><a href="{{ route('event') }}"><i class="fas fa-tasks"></i>
                イベント<span>Event</span></a></li>
            <li class="recipe1"><a href="{{ route('recipe') }}"><i class="fas fa-utensils"></i>
                旬のレシピ<span>Recipe</span></a></li>
            <li class="company1"><a href="{{ route('company') }}"><i class="fas fa-user-alt"></i>
                会社概要<span>Company</span></a></li>
            {{-- <li class="map"><a href="map.html">周辺マップ<span>Map</span></a></li> --}}
            <li class="contact1"><a href="{{ route('contact') }}"><i class="fas fa-envelope"></i>
                お問い合わせ<span>Contact</span></a></li>
        </ul>
    </nav>
    <main class="py-4">
        @yield('content')
    </main>

    <div class="contents bg-access">
        <div class="inner">

            <section>

            <h2 class="simple"><a href="{{ route('access_count') }}">アクセス</h2>

            <div class="href-left">

            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3234.3025664405945!2d139.71441071553568!3d35.84158378015734!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x601894e6755b666d%3A0xdf573201777d84df!2z44CSMzMzLTA4MzQg5Z-8546J55yM5bed5Y-j5biC5a6J6KGM6aCY5qC55bK477yR77yV77yX4oiS77yS!5e0!3m2!1sja!2sjp!4v1644386270751!5m2!1sja!2sjp" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>

            </div>
            <!--/#fooer-left-->

            <div class="href-right">

                <table class="ta1">
                    <caption>有限会社カネモ</caption>
                    <tr>
                    <th>所在地</th>
                    <td>〒333-0834<br>
                    埼玉県川口市大字安行領根岸157-2</td>
                    </tr>
                    <tr>
                    <th>営業時間</th>
                    <td>AM9:00〜PM6:00</td>
                    </tr>
                    <tr>
                    <th>TEL</th>
                    <td>048-281-1206</td>
                    </tr>
                    <tr>
                    <th>アクセス方法</th>
                    <td>
                        <ul class="disc">
                        <li>お車でお越しのお客様…南浦和前川通り「地蔵橋」近く。</li>
                        <li>バスでお越しのお客様…国際興業バス停「地蔵橋」下車徒歩１分。</li>
                        </ul>
                    </td>
                    </tr>
                    <tr>
                    <th>駐車場</th>
                    <td>20台可</td>
                    </tr>
                    <tr>
                    <th>関連サイト</th>
                    <td>
                        <div class="icon">
                        <a href="#"><img src="{{ asset('images/icon_facebook.png') }}" alt="Facebook"></a>
                        <a href="#"><img src="{{ asset('images/icon_instagram.png') }}" alt="Instagram"></a>
                        <a href="#"><img src="{{ asset('images/icon_twitter.png') }}" alt="Twitter"></a>
                        </div>
                    </td>
                    </tr>
                </table>
            </div>
            <!--/#fooer-right-->

        </section>

        </div>
        <!--/.inner-->
    </div>
    <!--/.contents-->

    <footer>
        <div id="footermenu" class="inner">
            <ul>
                <li class="title">サイトメニュー</li>
                <li><a href="{{ route('top') }}">ホーム</a></li>
                <li><a href="{{ route('info') }}">店内のご案内</a></li>
                <li><a href="{{ route('event') }}">イベント</a></li>
                <li><a href="{{ route('recipe') }}">旬のレシピ</a></li>
                {{-- <li><a href="map.html">周辺マップ</a></li> --}}
                <li><a href="{{ route('company') }}">会社概要</a></li>
                <li><a href="{{ route('contact') }}">お問い合わせ</a></li>

            </ul>
        </div>
        <!--/footermenu-->

        <div id="copyright">
            <small>Copyright&copy; <a href="{{ route('top') }}">有限会社カネモ</a> All Rights Reserved.</small>
            {{-- <span class="pr"><a href="https://template-party.com/" target="_blank">《Web Design:Template-Party》</a></span> --}}
        </div>

    </footer>

    <p class="nav-fix-pos-pagetop"><a href="#">↑</a></p>

    <!--メニュー開閉ボタン-->
    <div id="menubar_hdr" class="close"></div>
    <!--メニューの開閉処理条件設定　900px以下-->
    <script>
        window.addEventListener('DOMContentLoaded', function(){
            /** jQueryの処理 */
            if (OCwindowWidth() <= 900) {
                open_close("menubar_hdr", "menubar-s");
            }
        });
    </script>

    <!--パララックス用ファイル読み込み-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/protonet-jquery.inview/1.1.2/jquery.inview.min.js"></script>
    <script src="{{ asset('js/spma/jquery.inview_set.js') }}"></script>

</body>
