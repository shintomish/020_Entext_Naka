@extends('layouts.hp')

@section('content')

<div class="contents">

    <ul class="pan">
        <li><a href="{{ route('top') }}">ホーム</a></li>
        <li>イベント</li>
    </ul>

    <div class="inner">

        <section>

        <h2>イベントのお知らせ</h2>

        <table class="ta1">
            <caption>2月のイベントのお知らせ</caption>
            <tr>
                <th>02月11日(金)</th>
                <td>
                    <img src="{{ asset('images_sample/event/event01.jpg') }}" alt="" class="fr w30p">
                    <strong class="color1 big1">毎月恒例の大根の特売を開催します。</strong>
                    <ul class="disc">
                        <li>大根の最もおいしい時期です。</li>
                        <li>秋冬物は甘みと水分が多いので鍋物や煮物などに向いています。</li>
                        <li>特売価格でご奉仕します。</li>
                    </ul>
                    {{-- <p class="btn1"><a href="#">詳しくはこちら</a></p> --}}
                </td>
            </tr>

            <tr>
                <th>02月14日(月)</th>
                <td>
                    <img src="{{ asset('images_sample/event/event02.jpg') }}" alt="" class="fl w30p">
                    <strong class="color1 big1">毎週月曜のにんじん感謝デーを開催します。</strong>
                    <ul class="disc">
                        {{-- <li></li> --}}
                        <li>形がきれい、逆三角形でほっそりと長いものを揃えています。</li>
                        <li>食べたときに旨味や甘味がしっかりと感じられ、さわやかな香りがします。</li>
                    </ul>
                    {{-- <p class="btn1"><a href="#">詳しくはこちら</a></p> --}}
                </td>
            </tr>

            <tr>
                <th>02月25日(金)</th>
                <td>
                    <img src="{{ asset('images_sample/event/event06_flip.jpg') }}" alt="" class="fr w30p">
                    <strong class="color1 big1">色野菜の特売を開催します。</strong>
                    <ul class="disc">
                        <li>色野菜、食べてますか？</li>
                        <li>１日に必要な野菜３５０ｇを上手に摂りましょう。</li>
                    </ul>
                    {{-- <p class="btn1"><a href="#">詳しくはこちら</a></p> --}}
                </td>
            </tr>

            {{-- <tr>
                <th>XX月XX日</th>
                <td>サンプルテキスト。サンプルテキスト。サンプルテキスト。</td>
            </tr> --}}

        </table>

        </section>
    </div>
    <!--/.inner-->
</div>
<!--/.contents-->

@endsection
