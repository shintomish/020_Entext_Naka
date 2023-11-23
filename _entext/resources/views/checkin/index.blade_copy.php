@extends('layouts.api3_index')

@section('content')
    <h2>CheckIn</h2>
    <div class="text-right">

    </div>
	{{-- <script	src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous" defer>
    </script> --}}

    {{-- api2_blade.phpへ --}}
    {{-- <script src="{{ asset('js/app.js')}}" defer></script> --}}

    <div class="row">
        <!-- 検索エリア -->
    </div>

    {{-- Line --}}
    <hr class="mb-4">

    <script type="text/javascript">

        var ws;

        // ========================================
        // 効果音を鳴らす（★今回のメインはこれ★）
        // ========================================
        function ring_in() {
            document.getElementById("InSound").play();
        }

        function ring_out() {
            document.getElementById("OutSound").play();
        }

        function ring_err() {
            // document.getElementById('ErrSound').muted = false;
            // document.getElementById("ErrSound").play();

            var audio = document.createElement("AUDIO");
            document.body.appendChild(audio);
            audio.src="{{ asset('mp3/Andy_Err.mp3')}}";
            // audio.play();
            // document.body.addEventListener("mousemove", function () {
            //     audio.play();
            // })
        }

        function connectSocketServer(callback) {
            var support = "MozWebSocket" in window ? 'MozWebSocket' : ("WebSocket" in window ? 'WebSocket' : null);

            if (support == null) {
                return;
            }

            console.log("* Connecting to server ..");
            // console.log(support);

            // const ws = new WebSocket('ws://localhost:6001');
            // ws.onmessage = (m) => { console.log(m.data) }; // ここまでは、全てのタブ共通で入力する
            // ws.onopen = () => ws.send("Hello");
            // con.send('Hello'); // ここは一つのタブでだけ入力する

            // create a new websocket and connect
            ws = new window[support]('wss://127.0.0.1:6001/');
            // ws = new window[support]("ws://" + location.host + "/");
            if( ws != null){
                console.log(ws);
                console.log("* ws.close() ..");
                // ws.close();
            }

            // const ws = new WebSocket("wss://" + location.host + "/")
            // Event 処理
            // - onopen    // 接続された時
            // - onerror   // エラーが発生した時
            // - onmessage // データを受け取った時
            // - onclose   // 切断された時

            // メソッド
            // - send()    // データを送信する
            // - close()   // 通信を切断する

            // 接続
            // ws.addEventListener('open',function(e){
            //     console.log('Socket 接続成功');
            // });
            // ws.addEventListener('close',function(e){
            //     console.log('Socket 接続解除');
            // });
            // // サーバーからデータを受け取る
            // ws.addEventListener('message',function(e){
            //     console.log(e.data);
            // });

            // when data is comming from the server, this metod is called
            // window.onload = function(){
                // document.querySelector('script[src="{{ asset("js/jquery-3.6.0.min.js") }}"]');
                ws.onmessage = function (evt) {
                    var json = evt.data;

                    console.log(json);

                    var data = JSON.parse(json);

                    var received_msg = evt.data;
                        alert("Message is received...");

                    if( data["command"] == "add_message_to_browser"){

                        var message = "[" + data["time"] + "] 受信:" + data["message"];

                        add_message(message);

                    }
                    else if( data["command"] == "detect"){

                        var spl = data["message"].split(",");
                        var serial = spl[0];
                        var id = spl[1];

                        $("#_card_message").text("検出");
                        $("#_card_serial").text(serial);
                        $("#_card_id").text(id);

                        $("#_card_message").css("color", "red");
                    }
                    else if( data["command"] == "lost"){

                        $("#_card_message").text("未検出");
                        $("#_card_serial").text("--");
                        $("#_card_id").text("--");

                        $("#_card_message").css("color", "black");
                    }
                };

                // when the connection is established, this method is called
                ws.onopen = function () {
                    console.log('* Connection open');
                    if( typeof callback != "undefined" ){
                        callback("open");
                    }
                };

                // when the connection is closed, this method is called
                ws.onclose = function (e) {
                    console.log(e.message);
                    if( typeof callback != "undefined" ){
                        console.log('* Connection closed not undefined');
                        callback("close");
                    }
                };
                ws.onerror = function (e) {
                    console.log('* Connection onerror ');
                    if( typeof callback != "undefined" ){
                        console.log('* Connection error not undefined');
                        callback("error");
                    }
                }
            // window.onload = function(){
            // }

        }

        function disconnectWebSocket() {
            if (ws) {
                ws.close();
            }
            // console.log("close");
        }

        function add_message(message){

            $("#_message").append(message + "<br>");

        }

        function connect(){

            $("#_signal_green").attr("src", "{{ asset('png/signal_green_off.png')}}");
            $("#_signal_red").attr("src", "{{ asset('png/signal_red_off.png')}}");

            connectSocketServer(function(state){

                if( state == "open" ){
                    $("#_signal_green").attr("src", "{{ asset('png/signal_green_on.png')}}");
                    $("#_signal_red").attr("src", "{{ asset('png/signal_red_off.png')}}");

                    ring_in();

                    $("#_button_send").removeAttr("disabled");
                }
                else{
                    $("#_signal_green").attr("src", "{{ asset('png/signal_green_off.png')}}");
                    $("#_signal_red").attr("src", "{{ asset('png/signal_red_on.png')}}");

                    // console.log('* connectSocketServer state');
                    // console.log(state);

                    ring_err();

                    $("#_button_send").attr("disabled", "disabled");
                }
            });

        }

        function send_message(){

            var message = $("#_message_send").val();
            var time_str = get_time_str();

            var send = {
                "command":"add_message_to_app",
                "message": message,
                "time": time_str
            };

            var send_str = JSON.stringify(send);

            ws.send(send_str);

            var self_msg = "[" + time_str + "] 送信:" + message;

            add_message(self_msg);
        }

        function get_time_str(){

            var now = new Date();
            var y = now.getFullYear();
            var m = padding_zero(now.getMonth() + 1, 2);
            var d = padding_zero(now.getDate(), 2);
            var h = padding_zero(now.getHours(), 2);
            var i = padding_zero(now.getMinutes(), 2);
            var s = padding_zero(now.getSeconds(), 2);

            var ret = y + "/" + m + "/" + d + " " + h + ":" + i + ":" + s;

            return ret;
        }

        function padding_zero(src, len){
            return ("0" + src).slice(-len);
        }

        $(document).ready(function(){

            connect();

        });

    </script>

    <div class="table-responsive">
    </div>

    <body>
        <div style="text-align: center;">
            <div style="width:350px; margin:20px;display:inline-block; border: solid 2px black; border-radius: 10px; font-size: 30px; padding: 20px;">
                <div style="margin:20px;">
                    <span>状態:</span>
                    <span id="_card_message">未検出</span>
                </div>
                <div style="margin:20px;">
                    <span>SN:</span>
                    <span id="_card_serial">--</span>
                </div>
                <div style="margin:20px;">
                    <span>ID:</span>
                    <span id="_card_id">--</span>
                </div>
                <audio id="InSound" preload="auto" >
                    {{-- <img src="{{ asset('mp3/Andy_In.mp3')}}"> --}}
                    <source id="_ring_In" src="{{ asset('mp3/Andy_In.mp3')}}" type="audio/mp3" onclick="ring_in();"/>
                </audio>
                <audio id="OutSound" preload="auto" >
                    <source id="_ring_Out" src="{{ asset('mp3/Andy_Out.mp3')}}" type="audio/mp3" onclick="ring_out();"/>
                </audio>
                <audio id="ErrSound" preload="auto" >
                    <source id="_ring_Err" src="{{ asset('mp3/Andy_Err.mp3')}}" type="audio/mp3" onclick="ring_err();"/>
                </audio>
                <div style="margin:10px; margin-bottom: 0;text-align: right;">
                    <span style="font-size:12px;">
                    カードリーダー接続：
                    </span>
                    {{-- <img src="{{ asset('png/signal_green_off.png')}}"> --}}
                    <img id="_signal_green" src="{{ asset('png/signal_green_off.png')}}" style="width:18px;"
                        onclick="connect();"/>
                    <img id="_signal_red" src="{{ asset('png/signal_red_on.png')}}" style="width:18px; "
                        onclick="connect();"/>
                </div>
                <p class="button">
                    <input type="button" onclick="ring_in(); " value="鳴らす" />
                </p>

            </div>
        </div>

    </body>

@endsection

@section('part_javascript')
{{-- ChangeSideBar("nav-item-system-user"); --}}
    <script type="text/javascript">
            // $('.btn_del').click(function()
            //     if( !confirm('本当に削除しますか？') ){
            //         /* キャンセルの時の処理 */
            //         return false;
            //     }
            //     else{
            //         /*　OKの時の処理 */
            //         return true;
            //     }
            // });
    </script>
@endsection
