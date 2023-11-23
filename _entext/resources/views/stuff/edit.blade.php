{{-- @extends('layouts.app') --}}
@extends('layouts.customer')

@section('content')
    <h2>職員編集</h2>
    <div class="text-right">
        {{-- <a class="btn btn-success btn-sm mr-auto" href="{{route('customer.create')}}">新規登録</a> --}}
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        </div>
    @endif
    <div class="row">
        <div class="col-md-8 order-md-1">
            <form class="needs-validation" novalidate action="{{route('stuff.update',$stuff->id)}}" method="POST">
                @csrf
                @method('PUT')

            <h4 class="mb-3">情報</h4>

            <div class="row">
                <div class="col-2">

                </div>
                <div class="col-4">

                </div>

                <div class="col-2 bg-secondary text-right">
                    {{-- <label for="year">年</label> --}}
                </div>
                <div class="col-4">

                </div>
            </div>

            <div class="row">
                <div class="col-2 bg-info text-right">
                    <label for="custom_no">管理No</label>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="custom_no" value="{{ old('custom_no',$stuff->custom_no) }}">
                </div>

                <div class="col-2 bg-info text-right">
                    <label for="ic_number">ICカードNo</label>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="ic_number" value="{{ old('ic_number',$stuff->ic_number) }}">
                </div>
            </div>

            <div class="row">
                <div class="col-2 bg-info text-right">
                    <label for="last_name">氏名</label>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="last_name" value="{{ old('last_name',$stuff->last_name) }}">
                </div>

                <div class="col-2 bg-info text-right">
                    <label for="first_name">名前</label>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="first_name" value="{{ old('first_name',$stuff->first_name) }}">
                </div>
            </div>

            <div class="row">
                <div class="col-2 bg-info text-right">
                    <label for="last_kana">氏名(カナ)</label>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="last_kana" value="{{ old('last_kana',$stuff->last_kana) }}">
                </div>

                <div class="col-2 bg-info text-right">
                    <label for="first_kana">名前(カナ)</label>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="first_kana" value="{{ old('first_kana',$stuff->first_kana) }}">
                </div>
            </div>

            <div class="row">
                {{-- //性別 App/Providers/AppServiceProviderのboot--}}
                <div class="col-2 bg-info text-right">
                    <label for="sex">性別</label>
                </div>
                <div class="col-4">
                    <select class="custom-select d-block w-100" id="sex" name="sex">
                        @foreach ($loop_sex as $loop_sex2)
                            @if ($loop_sex2['no']==$stuff->sex)
                                <option selected="selected" value={{$loop_sex2['no']}}>{{ $loop_sex2['name'] }}</option>
                            @else
                                @if ($loop_sex2['no']==0)
                                    <option disabled value={{$loop_sex2['no']}}>{{ $loop_sex2['name'] }}</option>
                                @else
                                    <option value={{$loop_sex2['no']}}>{{ $loop_sex2['name'] }}</option>
                                @endif
                            @endif
                        @endforeach
                    </select>
                </div>

                {{-- //職種 App/Providers/AppServiceProviderのboot--}}
                <div class="col-2 bg-info text-right">
                    <label for="care_type">コース</label>
                </div>
                <div class="col-4">
                    <select class="custom-select d-block w-100" id="care_type" name="care_type">
                        @foreach ($loop_care_type_stuff as $loop_care_type_stuff2)
                            @if ($loop_care_type_stuff2['no']==$stuff->care_type)
                                <option selected="selected" value={{$loop_care_type_stuff2['no']}}>{{ $loop_care_type_stuff2['name'] }}</option>
                            @else
                                @if ($loop_care_type_stuff2['no']==0)
                                    <option disabled value={{$loop_care_type_stuff2['no']}}>{{ $loop_care_type_stuff2['name'] }}</option>
                                @else
                                    <option value={{$loop_care_type_stuff2['no']}}>{{ $loop_care_type_stuff2['name'] }}</option>
                                @endif
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-2 bg-info text-right">
                    <label for="birthdate">誕生日</label>
                </div>
                <div class="col-4">
                    <input type="date" class="form-control" name="birthdate" value="{{ old('birthdate',$stuff->birthdate) }}">
                </div>

                <div class="col-2 bg-info text-right">
                    <label for="age">年齢</label>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="age" value="{{ old('age',$stuff->age) }}">
                </div>
            </div>

            <div class="row">
                {{-- 入会状態 --}}
                <div class="col-2 bg-info text-right">
                    <label for="status">入社状態</label>
                </div>
                <div class="col-4">
                    <select class="custom-select d-block w-100" id="status" name="status">
                        @foreach ($loop_status_stuff as $loop_status_stuff2)
                            @if ($loop_status_stuff2['no']==$stuff->status)
                                <option selected="selected" value={{$loop_status_stuff2['no']}}>{{ $loop_status_stuff2['name'] }}</option>
                            @else
                                @if ($loop_status_stuff2['no']==0)
                                    <option disabled value={{$loop_status_stuff2['no']}}>{{ $loop_status_stuff2['name'] }}</option>
                                @else
                                    <option value={{$loop_status_stuff2['no']}}>{{ $loop_status_stuff2['name'] }}</option>
                                @endif
                            @endif
                        @endforeach
                    </select>
                </div>

                {{-- 曜日 --}}
                <div class="col-2 bg-info text-right">
                    {{-- <label for="week_type">曜日</label> --}}
                </div>
                <div class="col-4">
                    {{-- <select class="custom-select d-block w-100" id="week_type" name="week_type">
                        @foreach ($loop_week_type as $loop_week_type2)
                            @if ($loop_week_type2['no']==$stuff->week_type)
                                <option selected="selected" value={{$loop_week_type2['no']}}>{{ $loop_week_type2['name'] }}</option>
                            @else
                                @if ($loop_week_type2['no']==0)
                                    <option disabled value={{$loop_week_type2['no']}}>{{ $loop_week_type2['name'] }}</option>
                                @else
                                    <option value={{$loop_week_type2['no']}}>{{ $loop_week_type2['name'] }}</option>
                                @endif
                            @endif
                        @endforeach
                    </select> --}}
                </div>
            </div>

            {{-- 学年 --}}
            <div class="row">
                <div class="col-2 bg-info text-right">
                    {{-- <label for="employment_type">学年</label> --}}
                </div>
                <div class="col-4">
                    {{-- <select class="custom-select d-block w-100" id="employment_type" name="employment_type">
                        @foreach ($loop_employment_type as $loop_employment_type2)
                            @if ($loop_employment_type2['no']==$stuff->employment_type)
<option selected="selected" value={{$loop_employment_type2['no']}}>{{ $loop_employment_type2['name'] }}</option>
                            @else
                                @if ($loop_employment_type2['no']==0)
        <option disabled value={{$loop_employment_type2['no']}}>{{ $loop_employment_type2['name'] }}</option>
                                @else
        <option value={{$loop_employment_type2['no']}}>{{ $loop_employment_type2['name'] }}</option>

                                @endif
                            @endif
                        @endforeach
                    </select> --}}
                </div>

                <div class="col-2 bg-info text-right">
                    {{-- <label for="school_name">学校名</label> --}}
                </div>
                <div class="col-4">
                    {{-- <input type="text" class="form-control" name="school_name" value="{{ old('school_name',$stuff->school_name) }}"> --}}
                </div>

            </div>

            {{-- <div class="row">
                <div class="col-2">
                    <label for=""></label>
                </div>
                <div class="col-4">
                </div>

                <div class="col-2">
                    <label for=""></label>
                </div>
                <div class="col-4">
                </div>
            </div> --}}

            <div class="row">
                {{-- 入会日 --}}
                <div class="col-2 bg-info text-right">
                    <label for="joindate">入社日</label>
                </div>
                <div class="col-4">
                    <input type="date" class="form-control" name="joindate" value="{{ old('joindate',$stuff->joindate) }}">
                </div>

                <div class="col-2 bg-info text-right">
                    <label for="withdrawaldate">退職日</label>
                </div>
                <div class="col-4">
                <input type="date" class="form-control" name="withdrawaldate" value="{{ old('withdrawaldate',$stuff->withdrawaldate) }}">
                </div>
            </div>

            <div class="row">
                {{-- 休会日 --}}
                <div class="col-2 bg-info text-right">
                    <label for="recessdate">休職日</label>
                </div>
                <div class="col-4">
                    <input type="date" class="form-control" name="recessdate" value="{{ old('recessdate',$stuff->recessdate) }}">
                </div>

                <div class="col-2 bg-info text-right">
                    {{-- <label for="parent_name">保護者名</label> --}}
                </div>
                <div class="col-4">
                    {{-- <input type="text" class="form-control" name="parent_name" value="{{ old('parent_name',$stuff->parent_name) }}"> --}}
                </div>
            </div>

            <div class="row">
                <div class="col-2 bg-info text-right">
                    <label for="zip_code">郵便番号</label>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="zip_code" value="{{ old('zip_code',$stuff->zip_code) }}">
                </div>

                <div class="col-2 bg-info text-right">
                    <label for="address">住所</label>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="address" value="{{ old('address',$stuff->address) }}">
                </div>
            </div>

            <div class="row">
                <div class="col-2 bg-info text-right">
                    <label for="phone_1">電話１</label>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="phone_1" value="{{ old('phone_1',$stuff->phone_1) }}">
                </div>

                <div class="col-2 bg-info text-right">
                    <label for="phone_2">電話２</label>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="phone_2" value="{{ old('phone_2',$stuff->phone_2) }}">
                </div>
            </div>

            <div class="row">
                <div class="col-2 bg-info text-right">
                    <label for="email">eメール</label>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="email" value="{{ old('email',$stuff->email) }}">
                </div>

                <div class="col-2 bg-info text-right">
                    <label for="reserve">予備</label>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" name="reserve" value="{{ old('reserve',$stuff->reserve) }}">
                </div>
            </div>

            <div class="row">
                {{-- 開始 'Y-m-d H:i:s'--}}
                <div class="col-2 bg-info text-right">
                    <label for="entrytime">開始</label>
                </div>
                <div class="col-4">
                    <input type="time" class="form-control" name="entrytime" value="{{ old('entrytime',$stuff->entrytime) }}">
                </div>

                {{-- 終了 --}}
                <div class="col-2 bg-info text-right">
                    <label for="entrytime">終了</label>
                </div>
                <div class="col-4">
                    <input type="time" class="form-control" name="exittime" value="{{ old('exittime',$stuff->exittime) }}">
                </div>

            </div>

            <hr class="mb-4">  {{-- // line --}}
            <div class="row">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button class="btn btn-primary btn-lg btn-block" type="submit">保存</button>
                    <a class="btn btn-primary btn-lg btn-block" href="{{route('stuff.index')}}">戻る</a>
                </div>
            </div>
            <hr class="mb-4">  {{-- // line --}}

        </form>
        </div>
    </div>

@endsection

@section('part_javascript')

@endsection
