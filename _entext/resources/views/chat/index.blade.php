@extends('layouts.api2_index')

@section('content')
    <h2>Chat</h2>
    <div class="text-right">

    </div>
	{{-- <script	src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous">
    </script> --}}

    <div class="row">
        <!-- 検索エリア -->
    </div>

    {{-- Line --}}
    <hr class="mb-4">

    <div class="table-responsive">
    </div>

    <script src="{{ asset('js/app.js')}}"></script>

    <body>
        <div id="chat">
            <button type="button" @click="send()">送信</button>
            {{-- <button @click="send()" :disabled="!textExists">送信</button> --}}
            <br>
            <textarea v-model="message"></textarea>
            <br>

            {{-- Line --}}
            <hr>

                <li v-for="(m, key) in messages" :key="key">
                    <span v-text="m.user.name"></span>
                    <span > :</span>&nbsp;
                    <span v-text="m.body"></span>
                </li>

            {{-- <div v-for="m in messages"> --}}
                <!-- 登録された日時 -->
                {{-- <span style="color: green" v-text="m.created_at">{{ $str }}</span> --}}
                {{-- <span > :</span>&nbsp; --}}
                <!-- メッセージ内容 -->
                {{-- <span v-text="m.body"></span> --}}

            {{-- </div> --}}

        </div>
        {{-- <script src="/js/app.js"></script> --}}
        {{-- <script src="{{ asset('js/app.js')}}"></script> --}}
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

        {{-- 2022/05/06 --}}
        {{-- js/app.jsでは、本番環境でvueが表示されないので、cdnと併用した。 --}}
        {{-- 本番：Uncaught TypeError: Vue is not a constructor --}}
        {{-- UT環境：[Vue warn]: Cannot find element: #app --}}
        {{-- <script src="{{ mix('js/app.js')}}" defer></script> --}}
        {{-- <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.min.js"></script> --}}
        {{-- <script src="https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.js"></script> --}}
        {{-- <script src="https://cdn.jsdelivr.net/npm/vue@2.6.0"></script> --}}
        <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.min.js"></script>

        <script>

            new Vue({
                el: '#chat',
                data: {
                    message: '',
                    messages: []
                },
                methods: {
                    getMessages() {

                        // const url = '/ajax/chat';
                        const url = "{{ route('ajaxchatin') }}";
                        axios.get(url)
                        .then((response) => {

                            this.messages = response.data;
                        console.log('getMessages');

                        });

                    },
                    send() {

                        // const url = '/ajax/chat';
                        const url = "{{ route('ajaxchatcr') }}";
                        // const params = { message: this.message, user: this.user };
                        const params = { message: this.message};
                        axios.post(url, params)
                        .then((response) => {

                            // 成功したらメッセージをクリア
                            this.message = '';

                        });
                        console.log('send');
                    }
                },
                mounted() {
                    console.log('mounted');
                    this.getMessages();
                    Echo.channel('chat')
                    .listen('MessageCreated', (e) => {
                        this.getMessages(); // メッセージを再読込
                    });

                }
            });
            // Vue.createApp(app).mount('#app')
        </script>

    </body>

    {{-- Line --}}
    <hr class="mb-4">

@endsection

@section('part_javascript')
{{-- ChangeSideBar("nav-item-system-user"); --}}
    <script type="text/javascript">

    </script>
@endsection
