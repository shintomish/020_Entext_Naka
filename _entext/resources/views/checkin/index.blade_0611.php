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

    <div class="table-responsive">
    </div>

    <body>
        <div style="text-align: center;">
            <div class="container" id="app">
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
                </div>
                <div style="margin:10px; margin-bottom: 0;text-align: right;">
                    <span  v-if="connected && app.statisticsEnabled" style="font-size:12px;">接続：</span>
                    <span  v-if="! connected" style="font-size:12px;">切断：</span>
                    <img id="_signal_green" src="{{ asset('png/signal_green_off.png')}}" style="width:18px;"/>
                    <img id="_signal_red"   src="{{ asset('png/signal_red_on.png')}}" style="width:18px; "/>
                </div>

                <div class="card col-xs-12 mt-4">
                    <div class="card-header">
                        <form id="connect" class="form-inline" role="form">
                            <div class="row">
                                <div class="col-2">
                                    <label class="my-1 mr-2" for="app">App:</label>
                                </div>
                                <div class="col-4">
                                    <select class="form-control form-control-sm mr-2" name="app" id="app" v-model="app">
                                        <option v-for="app in apps" :value="app">{{ config('app.name', 'Laravel') }}</option>
                                    </select>
                                </div>

                                <div class="col-2">
                                    <label class="my-1 mr-2" for="app">Port:</label>
                                </div>
                                <div class="col-4">
                                    <input class="form-control form-control-sm mr-2" v-model="port" placeholder="Port">
                                </div>
                            </div>

                            <button v-if="! connected" type="submit" @click.prevent="connect" class="mr-2 mt-2 btn btn-sm btn-primary">
                                接続
                            </button>
                            <button v-if="connected" type="submit" @click.prevent="disconnect" class="mt-2 btn btn-sm btn-danger">
                                切断
                            </button>

                        </form>
                        <div id="status"></div>
                    </div>
                    <div class="card-body">
                        <div v-if="connected && app.statisticsEnabled">
                            <h4>Realtime Statistics</h4>
                            <div id="statisticsChart" style="width: 100%; height: 250px;"></div>

                            <h4>Event Creator</h4>
                            <form>
                                <div class="row">
                                    <div class="col">
                                        <input type="text" class="form-control" v-model="form.channel" placeholder="Channel">
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control" v-model="form.event" placeholder="Event">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col">
                                        <div class="form-group">
                                            <textarea placeholder="Data" v-model="form.data" class="form-control" id="data"
                                                      rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row text-right">
                                    <div class="col">
                                        <button type="submit" @click.prevent="sendEvent" class="mt-2 btn btn-sm btn-primary">Send event
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <h4>Events</h4>
                            <table id="events" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Socket</th>
                                        <th>Details</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(log, index) in logs.slice().reverse()" :key="index">
            {{-- OK <td><span class="badge bg-secondary">443210</span></td> --}}
                                        {{-- <td><span
                                            class="badge " :class="getBadgeClass(log)"
                                            v-text="log.type"></span></td>
                                        <td><span v-text="log.socketId"></span></td>
                                        <td><span v-text="log.details"></span></td>
                                        <td><span v-text="log.time"></span></td> --}}
                            <td><span class="badge" :class="getBadgeClass(log)">@{{ log.type }}</span></td>
                                        <td>@{{ log.socketId }}</td>
                                        <td>@{{ log.details }}</td>
                                        <td>@{{ log.time }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            $.ajaxSetup({
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            });

            new Vue({
                el: '#app',
                data: {
                    connected: false,
                    chart: null,
                    pusher: null,
                    app: null,
                    port: 6001,
                    apps: [{"id":"1404791","key":"0ff8809ccd70d39e96f8","secret":"50af2cd4cc7cc48e21e0","name":"Entext","host":null,"path":null,"capacity":null,"clientMessagesEnabled":false,"statisticsEnabled":true}],
                    form: {
                        channel: null,
                        event: null,
                        data: null
                    },
                    logs: [],
                },

                mounted() {
                    this.app = this.apps[0] || null;
                },

                methods: {
                    connect() {
                        // const authpath = "{{ route('checkin') }}" + "";
                        // alert(location.host); localhost:8573
                        const authpath = 'http://' + location.host + '/laravel-websockets/auth';
                        this.pusher = new Pusher(this.app.key, {
                            wsHost: this.app.host === null ? window.location.hostname : this.app.host,
                            wsPort: this.port === null ? 6001 : this.port,
                            wssPort: this.port === null ? 6001 : this.port,
                            wsPath: this.app.path === null ? '' : this.app.path,
                            disableStats: true,
                            authEndpoint: authpath,
                            auth: {
                                headers: {
                                    'X-CSRF-Token': "{{ csrf_token() }}",
                                    'X-App-ID': this.app.id,
                                },
                            },
                            enabledTransports: ['ws', 'wss'],
                            forceTLS: false,
                        });

                        this.pusher.connection.unbind('state_change');

                        this.pusher.connection.bind('state_change', states => {
                            this.connecting = false;
                            $('div#status').text("Channels current state is " + states.current);
                        });

                        this.pusher.connection.bind('connected', () => {
                            this.connected = true;
                            this.connecting = true;
                            this.loadChart();
                            $("#_signal_green").attr("src", "{{ asset('png/signal_green_on.png')}}");
                            $("#_signal_red").attr("src", "{{ asset('png/signal_red_off.png')}}");
                            // send_message();
                            // this.sendEvent();
                        });

                        this.pusher.connection.bind('disconnected', () => {
                            this.connected = false;
                            this.connecting = false;
                            this.logs = [];
                            // console.log("* disconnected to server ..");
                            $("#_signal_green").attr("src", "{{ asset('png/signal_green_off.png')}}");
                            $("#_signal_red").attr("src", "{{ asset('png/signal_red_on.png')}}");
                        });

                        this.pusher.connection.bind('error', event => {
                            if (event.error.data.code === 4100) {
                                $('div#status').text("Maximum connection limit exceeded!");
                                this.connected = false;
                                this.logs = [];
                                throw new Error("Over capacity");
                            }
                            this.connecting = false;
                        });

                        this.subscribeToAllChannels();

                        this.subscribeToStatistics();

                        this.pusher.connection.bind('pusher:subscription_succeeded', function(members) {
                            alert('successfully subscribed!');
                        });

                    },

                    disconnect() {
                        this.pusher.disconnect();
                    },

                    loadChart () {
                        const url = 'http://' + location.host + '/laravel-websockets/api/';
                        $.getJSON( url + this.app.id + '/statistics', (data) => {

                            let chartData = [
                                {
                                    x: data.peak_connections.x,
                                    y: data.peak_connections.y,
                                    type: 'lines',
                                    name: '# Peak Connections'
                                },
                                {
                                    x: data.websocket_message_count.x,
                                    y: data.websocket_message_count.y,
                                    type: 'bar',
                                    name: '# Websocket Messages'
                                },
                                {
                                    x: data.api_message_count.x,
                                    y: data.api_message_count.y,
                                    type: 'bar',
                                    name: '# API Messages'
                                }
                            ];

                            let layout = {
                                margin: {
                                    l: 50,
                                    r: 0,
                                    b: 50,
                                    t: 50,
                                    pad: 4
                                }
                            };
                            console.log("* statisticsChart ..");

                            this.chart = Plotly.newPlot('statisticsChart', chartData, layout);
                        });
                    },

                    subscribeToAllChannels () {
                        [
                            'disconnection',
                            'connection',
                            'vacated',
                            'occupied',
                            'subscribed',
                            'client-message',
                            'api-message',
                        ].forEach(channelName => this.subscribeToChannel(channelName))
                    },

                    subscribeToChannel(channel) {
                        Pusher.logToConsole = true;
                    // this.pusher.subscribe('{{ \BeyondCode\LaravelWebSockets\Dashboard\DashboardLogger::LOG_CHANNEL_PREFIX }}' + channel)
                    this.pusher.subscribe('private-websockets-dashboard-' + channel)
                        .bind('log-message', (data) => {
                            this.logs.push(data);
                        });
                    },

                    subscribeToStatistics() {
                    this.pusher.subscribe('private-websockets-dashboard-statistics')
                        .bind('statistics-updated', (data) => {
                            var update = {
                                x: [[data.time], [data.time], [data.time]],
                                y: [[data.peak_connection_count], [data.websocket_message_count], [data.api_message_count]]
                            };
                            this.logs.push(data);

                            Plotly.extendTraces('statisticsChart', update, [0, 1, 2]);
                        });
                    },

                    sendEvent() {
                        const baseURL = 'http://' + location.host + '/laravel-websockets/event';
                        axios
                            .post(baseURL, {
                                title: "Hello World!",
                                body: "This is a new post.",

                                _token: '{{ csrf_token() }}',
                                appId: this.app.id,
                                key: this.app.key,
                                secret: this.app.secret,
                                channel: this.form.channel,
                                event: this.form.event,
                                // data: (this.form.data),
                                // data: JSON.stringify(this.form.data),
                                data: JSON.stringify([ this.form.data ]),

                            })
                            .then((response) => {
                                alert('Send OK');
                                // alert(JSON.stringify(response));
                                send_message(response);
                            })
                            .catch(err => {
                                // alert('Error sending event.');
                                alert(err);
                            })
                            .then(() => {
                                this.connecting = false;
                            });
                    },
                    getBadgeClass (log) {
                        if (log.type === 'occupied'   ||
                            log.type === 'connection'
                        ) {
                            return 'bg-primary';
                        }
                        if (log.type === 'vacated') {
                            return 'bg-warning';
                        }
                        if (log.type === 'disconnection') {
                            return 'bg-error';
                        }
                        if (log.type === 'api_message') {
                            return 'bg-info';
                        }
                        return 'bg-secondary';
                    },
                    startRefreshInterval () {
                        this.refreshTicker = setInterval(function () {
                            this.loadChart();
                        }.bind(this), this.refreshInterval * 1000);
                    },
                    stopRefreshInterval () {
                        clearInterval(this.refreshTicker);
                        this.refreshTicker = null;
                    },
                },
            });

            function send_message(response){

                // var message = $("#_message_send").val();
                var message = JSON.stringify([ response["status"] ]);
                var time_str = get_time_str();

                var send = {
                    "command":"add_message_to_app",
                    "message": message,
                    "time": time_str
                };

                var send_str = JSON.stringify(send);

                // console.log(response["data"]);
                // console.log(response);

                // ws.send(send_str);

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

            function add_message(message){
                $("#_message").append(message + "<br>");
                // console.log("message");
                console.log(message);
            }

            $(document).ready(function(){

                // connect();

            });

        </script>
    </body>

    {{-- Line --}}
    <hr class="mb-4">

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
