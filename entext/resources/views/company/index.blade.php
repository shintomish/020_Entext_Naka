@extends('layouts.hp')

@section('content')

<div class="contents">
    <ul class="pan">
        <li><a href="{{ route('top') }}">ホーム</a></li>
        <li>会社概要</li>
    </ul>

    <div class="inner">
        <div class="main">
            <section>
                <h2>会社概要</h2>

                <table class="ta1">
                    {{-- <caption>有限会社カネモ</caption> --}}
                    <tr>
                        <th>会社名</th>
                        <td>有限会社  カネモ</td>
                    </tr>
                    <tr>
                        <th>設立</th>
                        <td>明治36年</td>
                    </tr>
                    <tr>
                        <th>沿革</th>
                        <td>店名由来：カネモの「カネ」は、商店でよくつける昔の市場の名前、屋号の名前からきており、「モ」は創業者名字の「森」からきている
                            <ul class="disc">
                                <li>明治36年：雑貨屋として開業</li>
                                <li>昭和61年：スーパーへ業態転換を図る</li>
                                <li>平成04年：5月1日に資本金500万円にて有限会社カネモを設立</li>
                                <li>令和03年：株式会社ウチダとM&Aが成立。屋号はそのままに、子会社となる</li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <th>代表者</th>
                        <td>代表取締役  平尾 隆司</td>
                    </tr>
                    <tr>
                        <th>資本金</th>
                        <td>500万円</td>
                    </tr>
                    <tr>
                        <th>所在地</th>
                        <td>〒333-0834<br>
                        埼玉県川口市大字安行領根岸157-2</td>
                    </tr>
                    <tr>
                        <th>TEL/FAX</th>
                        <td>TEL:048-281-1206  FAX:048-281-1075</td>
                    </tr>
                    <tr>
                        <th>事業内容</th>
                        <td>スーパーマーケット事業・食材納品事業・個人宅配事業</td>
                    </tr>
                    <tr>
                        <th>取引銀行</th>
                        <td>青木信用金庫 上根支店</td>
                    </tr>
                    <tr>
                        <th>取引先</th>
                        <td>前川小学校・前川東小学校・根岸小学校・上根小学校<br>
                        株式会社せいび埼玉・ニュータウンビルサービス株式会社・他</td>
                    </tr>
                    <tr>
                        <th>仕入先</th>
                        <td>株式会社花得・他</td>
                    </tr>
                </table>
            </section>

        </div>
        <!--/.main-->

        <div class="sub">

            <section>

                <h2>お知らせ</h2>

                <div class="list">
                    {{-- <figure><img src="images_sample/sample8.jpg" alt="写真の説明"></figure> --}}
                    <dt>2022/02/12</dt>
                    <h4>ホームページ<span class="newicon">NEW</span></h4>
                    <p>ホームページを公開しました。</p>
                </div>

                <div class="list">
                    {{-- <figure><img src="images_sample/sample8.jpg" alt="写真の説明"></figure> --}}
                    <dt>2020/01/12</dt>
                    <h4><a href="#">新型コロナ</a></h4>
                    <p><a href="#">新型コロナ感染予防対策の取込み</a></p>
                </div>

            </section>

            <section>
                <h2>アクセス</h2>
                <p class="list">埼玉県川口市大字安行領根岸157-2<br>
                TEL：048-281-1206<br>
                受付：9:00～19:00</p>
            </section>

        </div>
        <!--/.sub-->

    </div>
    <!--/.inner-->
</div>
<!--/.contents-->

@endsection
