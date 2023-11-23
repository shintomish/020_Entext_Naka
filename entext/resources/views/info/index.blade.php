@extends('layouts.hp')

@section('content')

<div class="contents">

    <ul class="pan">
    <li><a href="{{ route('top') }}">ホーム</a></li>
    <li>店内のご案内</li>
    </ul>

    <div class="inner">

        <section class="mb20 ofh">

        <h2>店内のご案内</h2>

        {{-- <h3>photoパターン</h3> --}}

        <div class="up">

        <div class="photo">
        <figure><img src="images_sample/shopp/shopp01_flip.png" alt="shopp01"></figure>
        <p>店内</p>
        </div>

        <div class="photo">
        <figure><img src="images_sample/shopp/shopp02_flip.png" alt="shopp02"></figure>
        <p>レジ</p>
        </div>

        <div class="photo">
            <figure><img src="images_sample/shopp/shopp03_flip.jpg" alt="shopp03"></figure>
        <p>店内</p>
        </div>

        </div>
        <!--/.up-->

        </section>

        <section>

        {{-- <h3>box1パターン</h3> --}}

        <div class="box1 transform1">
            <figure class="fr w30p">
                <img src="{{ asset('images_sample/shopp/shopp01.png') }}" alt="shopp01">
            </figure>
            <div class="fl w65p">
                <h4>店内</h4>
                <p>新鮮な野菜や美味しい果物を、毎朝市場より仕入て店頭に並べています。</p>
            </div>
        </div>

        <div class="box1 transform1">
            <figure class="fl w30p">
                <img src="{{ asset('images_sample/shopp/shopp02.png') }}" alt="shopp01">
            </figure>
            <div class="fr w65p">
                <h4>レジ</h4>
                <p>キャッシュレスに不慣れな方にも親切に対応させていただきます。</p>
            </div>
        </div>

        <div class="box1 transform1">
            <figure class="fr w30p">
                <figure><img src="images_sample/shopp/shopp03_flip.jpg" alt="shopp03"></figure>
            </figure>
            <div class="fr w65p">
                <h4>店内</h4>
                <p>いつでも清潔に消毒を実施しています。</p>
            </div>
        </div>

        </section>

    </div>
    <!--/.inner-->
</div>
<!--/.contents-->


@endsection
