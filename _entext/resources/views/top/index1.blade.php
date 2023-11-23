@extends('layouts.hp')

@section('content')

<div class="contents">
    <div class="inner">
        <section>

            <h2 class="simple">カネモ・今月の催し物</h2>
            {{-- <div class="box2 transform1"> --}}
            <div class="box2 transform2">
                @php
                    $src1    = 'images_sample/top/top01_flip.jpg';
                    $tytle1  = "刺身の試食";
                    $detail1 = "02月15日(火)に刺身の試食会を行います。みなさまぜひご参加下さい。";
                    $sdate1  = "02/15";
                @endphp
                <figure>
                    <img src="{{ asset( $src1 ) }}" alt="top01">
                </figure>
                <h4>{{ $tytle1 }}</h4>
                <p>{{ $detail1 }}</p>
                <span class="date">{{ $sdate1 }}</span>
            </div>

            <div class="box2 transform2">
                @php
                    $src2    = 'images_sample/top/top02_flip.jpg';
                    $tytle2  = "野菜のつかみ取り";
                    $detail2 = "02月21日(月)に新鮮野菜のつかみ取りを行います。おひとり様200gまで。";
                    $sdate2  = "02/21";
                @endphp
                <figure>
                    <img src="{{ asset( $src2 ) }}" alt="top01">
                </figure>
                <h4>{{ $tytle2 }}</h4>
                <p>{{ $detail2 }}</p>
                <span class="date">{{ $sdate2 }}</span>
            </div>

            <div class="box2 transform2">
                @php
                    $src3    = 'images_sample/top/top03_flip.jpg';
                    $tytle3  = "りんごの試食";
                    $detail3 = "02月25日(金)に産地直送青森県産りんご シナノゴールドの試食会を行います。お早めに。";
                    $sdate3  = "02/25";
                @endphp
                <figure>
                    <img src="{{ asset( $src3 ) }}" alt="top01">
                </figure>
                <h4>{{ $tytle3 }}</h4>
                <p>{{ $detail3 }}</p>
                <span class="date">{{ $sdate3 }}</span>
            </div>

        </section>

    </div>
    <!--/.inner-->
</div>
<!--/.contents-->

<div class="contents">
    <div class="inner">
        <section>
            <h2 class="simple">今月のピックアップ野菜</h2>

            {{-- <div class="box1 up"> --}}
            <div class="box1 right">
                @php
                    $src1    = 'images_sample/event/event01.jpg';
                    $tytle1  = "大根";
                    $detail1 = "大根の最もおいしい時期です。". "\r\n";
                    $detail1 = $detail1 . "秋冬物は甘みと水分が多いので鍋物や煮物などに向いています。";
                @endphp

                <figure class="fr w30p">
                    <img src="{{ asset( $src1 ) }}" alt="event01">
                </figure>
                <div class="fr w65p">
                    <h4>{{ $tytle1 }}</h4>
                    <p>{{ $detail1 }}</p>
                </div>
            </div>

            <div class="box1 left">
                @php
                    $src2    = 'images_sample/event/event02.jpg';
                    $tytle2  = "人参";
                    $detail2 = "形がきれい、逆三角形でほっそりと長いものを揃えています。". "\r\n";
                    $detail2 = $detail2 . "食べたときに旨味や甘味がしっかりと感じられ、さわやかな香りがします。";
                @endphp

                <figure class="fl w30p">
                    <img src="{{ asset( $src2 ) }}" alt="event01">
                </figure>
                <div class="fl w65p">
                    <h4>{{ $tytle2 }}</h4>
                    <p>{{ $detail2 }}</p>
                </div>
            </div>

            <div class="box1 right">
                @php
                    $src3    = 'images_sample/event/event06_flip.jpg';
                    $tytle3  = "パプリカ";
                    $detail3 = "色野菜、食べてますか？". "\r\n";
                    $detail3 = $detail3 . "１日に必要な野菜３５０ｇを上手に摂りましょう。";
                @endphp

                <figure class="fr w30p">
                    <img src="{{ asset( $src3 ) }}" alt="event01">
                </figure>
                <div class="fr w65p">
                    <h4>{{ $tytle3 }}</h4>
                    <p>{{ $detail3 }}</p>
                </div>

            </div>

        </section>

    </div>
    <!--/.inner-->
</div>
<!--/.contents-->

<div class="contents">
    <div class="inner">
        <section id="new">
            <h2>更新情報・お知らせ</h2>
            <dl>
                <dt>2022/02/12</dt>
                    <dd>ホームページを公開しました。<span class="newicon">NEW</span></dd>
                <dt>2020/01/12</dt>
                    <dd>新型コロナ感染予防対策の取込みについて</dd>
            </dl>
            <p class="r">&raquo;&nbsp;<a href="#">過去ログ</a></p>
        </section>
    </div>
    <!--/.inner-->
</div>
<!--/.contents-->

@endsection
