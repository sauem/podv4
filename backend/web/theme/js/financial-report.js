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
            data: []
        }, {
            name: 'C11',
            type: 'column',
            data: []
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
    let optionsTwo = {
        colors: ['#022ae2', '#1e9ff6', '#ff00b7'],
        series: [{
            name: 'C11',
            type: 'column',
            data: []
        }, {
            name: 'C13',
            type: 'column',
            data: []
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
    let chartOne = new ApexCharts(document.querySelector("#chart-one"), optionsOne);
    let chartTwo = new ApexCharts(document.querySelector("#chart-two"), optionsTwo);
    chartOne.render();
    chartTwo.render();
    let htmlOverview = $('#financial-overview-template').html(),
        resultOverView = $('#financial-overview-result'),
        templateOverView = Handlebars.compile(htmlOverview);
    this.getData = async function (params = {}, type = 'GET') {
        return $.ajax({
            url: AJAX_PATH.financialReport,
            data: params,
            type: type,
            cache: false,
        });
    }
    this.showLoadingChart = () => {
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
            search: async function (params) {
                try {
                    instance.showLoadingChart();
                    const res = await instance.getData(params, 'POST');
                    this.initChart(res);
                } catch (e) {
                    console.log("error financial", e);
                }
            },
            initChart: function ({labels, data, counter, isEmpty}) {

                try {
                    if (isEmpty) {
                        chartOne.resetSeries();
                        chartTwo.resetSeries();
                        $('#chart-one').html('No data showing....!');
                        $('#chart-two').html('No data showing....!');
                        return false;
                    }

                    const {C8, C11, C13} = data;
                    optionsOne.labels = labels;
                    optionsOne.series[0].data = C8;
                    optionsOne.series[1].data = C11;
                    chartOne.updateOptions(optionsOne);
                    // Chart 2
                    optionsTwo.labels = labels;
                    optionsTwo.series[0].data = C11;
                    optionsTwo.series[1].data = C13;
                    chartTwo.updateOptions(optionsTwo);
                    resultOverView.html(templateOverView(counter));
                } catch (e) {
                    console.log('ERRORR');
                } finally {
                    instance.hideLoadingChart();
                }
            },
            overview: async function () {
                try {
                    let res = await instance.getData();
                    instance.showLoadingChart();
                    this.initChart(res);
                } catch (e) {
                    console.log("Init chart", e);
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
