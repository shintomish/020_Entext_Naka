<html>
<body>
    <div id="app">
        <div>
            集計：
            <span v-text="year"></span>年
            <span v-text="month"></span>月
            <button type="button" @click="moveMonth(-1)">前へ</button>
            <button type="button" @click="moveMonth(1)">次へ</button>
            <a class="btn btn-primary btn-sm btn-block" href="{{route('top')}}">戻る</a>
        </div>
        <canvas id="chart"></canvas>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.2/dist/Chart.min.js"></script>
    <script>

        new Vue({
            el: '#app',
            data: {
                year: '',
                month: '',
                chart: null
            },
            methods: {
                drawChart() {

                    const url = '/ajax/access_count?year='+ this.year +'&month='+ this.month;

                    axios.get(url)
                        .then(response => {

                            const data = response.data;
                            const ctx = document.getElementById('chart').getContext('2d');
                            let labels = [];

                            for(let i = 0 ; i < 24 ; i++) {

                                labels.push(i +'時');

                            }

                            // 以下の部分でChart.jsのインスタンスをクリアしていることに注目してください。
                            // こうしないと「年・月」を変更した場合に前のデータが残ってしまうことになるからです。
                            // （描画は上手く行きますが、ポップアップがおかしくなります）
                            if(this.chart) {

                                this.chart.destroy();

                            }

                            this.chart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'アクセス回数',
                                        data: data,
                                        // borderColor: "rgba(0,0,255,1)"
                                        backgroundColor: "rgba(0,255,0,0.5)"    //緑
                                    }]
                                },
                                options: {
                                    scales: {
                                        yAxes: [{
                                            ticks: {
                                                stepSize: 1
                                            }
                                        }]
                                    }
                                }
                            });

                        });

                },
                moveMonth(number) {

                    this.month += number;

                    if(this.month === 0) {

                        this.year--;
                        this.month = 12;

                    } else if(this.month === 13) {

                        this.year++;
                        this.month = 1;

                    }

                    this.drawChart();

                }
            },
            mounted() {

                const date = new Date();
                this.year = date.getFullYear();
                this.month = date.getMonth() + 1;
                this.drawChart();

            }
        });

    </script>
</body>
</html>
