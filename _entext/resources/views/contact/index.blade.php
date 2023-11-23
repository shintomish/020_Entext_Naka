@extends('layouts.hp')

@section('content')
<div class="contents">

    <ul class="pan">
        <li><a href="{{ route('top') }}">ホーム</a></li>
        <li>お問い合わせ</li>
    </ul>

    <div class="inner">

        <section>

        <h2>お問い合わせ</h2>

        <table class="ta1">
        <tr>
        <th>お名前※</th>
        <td><input type="text" name="お名前" size="30" class="ws"></td>
        </tr>
        <tr>
        <th>メールアドレス※</th>
        <td><input type="text" name="メールアドレス" size="30" class="ws"></td>
        </tr>
        <tr>
        <th>お問い合わせ詳細※</th>
        <td><textarea name="お問い合わせ詳細" cols="30" rows="10" class="wl"></textarea></td>
        </tr>
        </table>

        <p class="c">
        <input type="submit" value="内容を確認する" class="btn">
        </p>

        </section>

    </div>
    <!--/.inner-->
</div>
<!--/.contents-->

@endsection
