@extends('layouts.hp')

@section('content')
<div class="contents">

    <ul class="pan">
        <li><a href="{{ route('top') }}">ホーム</a></li>
        <li>旬のレシピ</li>
    </ul>

    <div class="inner">

        <section class="mb20 ofh">

        <h2>旬のレシピ</h2>
        <h3>大根を使って</h3>
        <div class="photo up">
            <a href="#">
            <figure><img src="images_sample/recipe/recipe04.jpg" alt=""></figure>
            <p>簡単大根もち</p>
            </a>
            </div>

            <div class="photo up">
            <a href="#">
            {{-- <figure><img src="images_sample/sample9.jpg" alt="Cake Shop"></figure> --}}
            <figure><img src="images_sample/recipe/recipe02.jpg" alt=""></figure>
            <p>大根のナムル</p>
            </a>
            </div>

            <div class="photo up">
            <a href="#">
            <figure><img src="images_sample/recipe/recipe03.jpg" alt=""></figure>
            <p>焼き大根</p>
            </a>
            </div>

        </section>

        <section>

            <div class="box1 up">
            <figure class="fr w30p"><a href="#"><img src="images_sample/recipe/recipe04.jpg" alt="Flower Shop"></a></figure>
            <div class="fl w65p">
            <h4>簡単大根もち</h4>
            <p>香味野菜や調味料を上手に使って、大根自体のおいしさを引き出すのが中国風の特徴。</p>
            {{-- <p class="btn1"><a href="#">もっと詳しく見る</a></p> --}}
            </div>
            </div>

            <div class="box1 up">
            <figure class="fl w30p"><a href="#"><img src="images_sample/recipe/recipe02.jpg" alt=""></a></figure>
            <div class="fr w65p">
            <h4>大根のナムル</h4>
            <p>とうがらしをピリリときかせた甘酸っぱいナムルです。簡単に食卓に一品増やしましょう！</p>
            {{-- <p class="btn1"><a href="#">もっと詳しく見る</a></p> --}}
            </div>
            </div>

            <div class="box1 up">
                <figure class="fr w30p"><a href="#"><img src="images_sample/recipe/recipe03.jpg" alt="Flower Shop"></a></figure>
                <div class="fl w65p">
                <h4>焼き大根</h4>
                <p>香ばしいごま油で大根を焼く、粋な江戸料理です。おいしい大根をたっぷり味わって。</p>
                {{-- <p class="btn1"><a href="#">もっと詳しく見る</a></p> --}}
                </div>
            </div>

        </section>



    </div>
    <!--/.inner-->
</div>
<!--/.contents-->

@endsection
