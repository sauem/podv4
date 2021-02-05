const nFormatter = function (num) {
    if (num >= 1000000000) {
        return (num / 1000000000).toFixed(1).replace(/\.0$/, '') + 'G';
    }
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
    }
    if (num >= 1000) {
        return (num / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
    }
    return num;
}

function initFinancialChart() {

    let optionsOne = {
        colors: ['#022ae2', '#1e9ff6', '#ff00b7'],
        series: [{
            name: 'C8',
            type: 'column',
            data: [202, 505, 414, 160, 227, 302, 201, 352, 752, 320, 257, 160]
        }, {
            name: 'C11',
            type: 'column',
            data: [440, 505, 414, 671, 227, 413, 201, 352, 752, 320, 257, 160]
        }],
        chart: {
            height: 350,
            type: 'line',
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    position: 'center', // top, center, bottom
                },
            }
        },
        stroke: {
            width: [0, 0]
        },
        dataLabels: {
            background: false,
            style: {
                fontSize: '10px',
            },
            enabled: true,
            enabledOnSeries: [0, 1],
            formatter: function (val, {seriesIndex, dataPointIndex, w}) {
                return SYMBOL_MARKET + nFormatter(val);
            }
        },
        labels: ['01 Jan 2001', '02 Jan 2001', '03 Jan 2001', '04 Jan 2001', '05 Jan 2001', '06 Jan 2001', '07 Jan 2001', '08 Jan 2001', '09 Jan 2001', '10 Jan 2001', '11 Jan 2001', '12 Jan 2001'],
        legend: {
            position: 'top',
            fontSize: '15px',
            horizontalAlign: 'center',
            offsetY: 10,
        },
        yaxis: [
            {
                opposite: true,
                axisTicks: {
                    show: true,
                },
                axisBorder: {
                    show: true,
                    color: '#008FFB'
                },
                labels: {
                    style: {
                        colors: '#008FFB',
                    },
                    formatter: function (value, index) {
                        return SYMBOL_MARKET + nFormatter(value);
                    }
                },
            },
        ]
    };
    let htmlOverview = $('#financial-overview-template').html(),
        resultOverView = $('#financial-overview-result'),
        templateOverView = Handlebars.compile(htmlOverview);
    this.getData = async function (task = "overview", params = {}) {
        return $.ajax({
            url: AJAX_PATH.financialReport,
            data: {task, params},
            cache: false,
        });
    }
    this.showLoadingChart  = () => {
        $('.chart-area').append('<div class="card-disabled"><div class="card-portlets-loader"></div></div>');
    }
    this.hideLoadingChart = () => {
        let load = $('.chart-area').find('.card-disabled');
        setTimeout(function () {
            $(load).fadeOut('fast', function () {
                $(load).remove();
            });
        }, 500);
    }
    this.render = function () {
        const instance = this;
        return {
            search: function (params) {

            },
            overview: async function () {
                try {
                    let {labels, data, counter} = await instance.getData();

                    optionsOne.labels = labels;
                    optionsOne.series[0].data = data.C8;
                    optionsOne.series[1].data = data.C11;

                    const chartOne = new ApexCharts(document.querySelector("#chart-one"), optionsOne);
                    chartOne.render();

                    // Chart 2
                    optionsOne.labels = labels;
                    optionsOne.series[0].data = data.C11;
                    optionsOne.series[0].name = 'C11';
                    optionsOne.series[1].data = data.C13;
                    optionsOne.series[1].name = 'C13';

                    const chartTwo = new ApexCharts(document.querySelector("#chart-two"), optionsOne);
                    chartTwo.render();
                    // init result
                    resultOverView.html(templateOverView(counter));
                    this.hideLoadingChart();
                } catch (e) {
                }
            },
            topup: async function (search = {}) {
                initPicker();

                const topupReq = async function (search = {}) {
                    return $.ajax({
                        type: 'GET',
                        url: AJAX_PATH.financialTopup,
                        cache: false,
                        data: search
                    });
                }
                try {
                    const res = await topupReq(search);
                    resultIndex.html(template(res.index));
                    resultData.html(templateData(res.data));

                } catch (e) {
                    console.log(e);
                }
            },
            crossed: function (table) {
                console.log("Crossed");
            },
            uncross: function (table) {
                console.log("uncross");
            }
        }
    }
}
