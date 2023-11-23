{{-- @extends('layouts.app') --}}
@extends('layouts.customer')

@section('content')
    <h2>職員出席</h2>
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
            <form class="needs-validation" novalidate action="{{route('stuff-attend.update',$stuffattend->id)}}" method="POST">
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
                    <label for="last_name">職員名</label>
                </div>
                <div class="col-4">
                    <select class="custom-select d-block w-100" id="last_name" name="last_name">
                        @foreach($stuffs as $stuffs2)
                            @if($stuffs2->id==$stuffattend->stuff_id)
                                @php
                                    $fullname = $stuffs2->last_name. ' '.$stuffs2->first_name
                                @endphp
                      <option select value={{$stuffs2->id}}>{{ $fullname }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-2 bg-info text-right">
                    <label for="eventdate">出勤日</label>
                </div>
                <div class="col-4">
                    <input type="date" class="form-control" name="eventdate" value="{{ old('eventdate',$stuffattend->eventdate) }}">
                </div>
            </div>

            <div class="row">
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
            </div>

            <div class="row">
                <div class="col-2 bg-info text-right">
                    <label for="status">出欠</label>
                </div>
                <div class="col-4">
                    <select class="custom-select d-block w-100" id="status" name="status">
                        @foreach ($loop_atd_status_stuff as $loop_atd_status_stuff2)
                            @if ($loop_atd_status_stuff2['no']==$stuffattend->status)
<option selected="selected" value={{$loop_atd_status_stuff2['no']}}>{{ $loop_atd_status_stuff2['name'] }}</option>
                            @else
                                @if ($loop_atd_status_stuff2['no']==0)
        <option disabled value={{$loop_atd_status_stuff2['no']}}>{{ $loop_atd_status_stuff2['name'] }}</option>
                                @else
        <option value={{$loop_atd_status_stuff2['no']}}>{{ $loop_atd_status_stuff2['name'] }}</option>

                                @endif

                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-2">
                    {{-- <label for="fee_02">02月</label> --}}
                </div>
                <div class="col-4">
                    {{-- <input type="text" class="form-control" name="fee_02" value="{{ old('fee_02',$stuffattend->fee_02) }}"> --}}
                </div>

            </div>

            <div class="row">
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
            </div>

            <div class="row">
                <div class="col-2 bg-info text-right">
                    <label for="entrytime">開始</label>
                </div>
                <div class="col-4">
                    <input type="time" class="form-control" name="entrytime" value="{{ old('entrytime',$stuffattend->entrytime) }}">
                </div>

                <div class="col-2 bg-info text-right">
                    <label for="entrytime">終了</label>
                </div>
                <div class="col-4">
                    <input type="time" class="form-control" name="exittime" value="{{ old('exittime',$stuffattend->exittime) }}">
                </div>

            </div>

            <hr class="mb-4">  {{-- // line --}}
            <div class="row">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button class="btn btn-primary btn-lg btn-block" type="submit">保存</button>
                    <a class="btn btn-primary btn-lg btn-block" href="{{route('stuff-attend.index')}}">戻る</a>
                </div>
            </div>
            <hr class="mb-4">  {{-- // line --}}

        </form>
        </div>
    </div>

@endsection

@section('part_javascript')

@endsection
