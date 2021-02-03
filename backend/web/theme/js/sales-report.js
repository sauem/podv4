function initSaleChart() {
    let numberCounterTemplate = $("#sales-template").html(),
        numberCounterResult = $("#sales-result"),
        counterTemplate = Handlebars.compile(numberCounterTemplate),
        buttonSearch = $("#searchData"),
        formSearch = $("#sales-search");

    let optionsOne = {
        colors: ['#022ae2', '#1e9ff6', '#ff00b7'],
        series: [{
            name: 'C8',
            type: 'column',
            data: []
        }, {
            name: 'C3',
            type: 'column',
            data: []
        }, {
            name: 'C8/C3',
            type: 'line',
            data: []
        }],
        chart: {
            height: 350,
            type: 'line',
            stacked: false,
        },
        stroke: {
            width: [0, 0, 4]
        },
        dataLabels: {
            enabled: true,
            enabledOnSeries: [2],
            formatter: function (value, {seriesIndex, dataPointIndex, w}) {
                return value + '%';
            }
        },
        labels: [],
        noData: {
            text: 'Loading...'
        },
        legend: {
            position: 'top',
            fontSize: '15px',
            horizontalAlign: 'center',
            offsetY: 10,
        },
        xaxis: {
            labels: {
                rotateAlways: false
            }
        },
        yaxis: [
            {
                seriesName: 'C3',
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
                    formatter: function (val, index) {
                        return val.toFixed(0);
                    }
                },
                tooltip: {
                    // enabled: true
                }
            },
            {
                seriesName: 'C8',
                axisTicks: {show: false},
                axisBorder: {show: false},
                labels: {
                    show: false,
                    formatter: function (val, index) {
                        return val.toFixed(0);
                    }
                }
            },
            {
                seriesName: 'C8/C3',
                opposite: true,
                min: 0,
                max: 150,
                axisTicks: {
                    show: true,
                },
                axisBorder: {
                    show: true,
                    color: '#FEB019'
                },
                labels: {
                    style: {
                        colors: '#FEB019',
                    },
                    formatter: function (val, index) {
                        return val + '%';
                    }
                },
                title: {
                    text: "C8/C3 (%)",
                    style: {
                        color: '#FEB019',
                    }
                }
            },
        ]
    };
    let optionsTwo = {
        series: [{
            name: 'C8 (OK)',
            data: [],
        }, {
            name: 'C6 (Cancel)',
            data: [],
        }, {
            name: 'C7 (Callback)',
            data: [],
        }, {
            name: 'C4 (Number fail)',
            data: [],
        }, {
            name: 'C0 (New)',
            data: [],
        }],
        noData: {
            text: 'Loading...'
        },
        chart: {
            type: 'bar',
            height: 350,
            stacked: true,
            toolbar: {
                show: true
            },
            zoom: {
                enabled: true
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                legend: {
                    position: 'bottom',
                    offsetX: -10,
                    offsetY: 0
                }
            }
        }],
        plotOptions: {
            bar: {
                horizontal: false,
            },
        },
        yaxis: [
            {
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
                    }
                },
                tooltip: {
                    enabled: true
                }
            },
        ],
        labels: [],
        legend: {
            position: 'right',
            offsetY: 40
        },
        fill: {
            opacity: 1
        }
    };

    let chartOne = new ApexCharts(document.querySelector("#chart-one"), optionsOne);
    let chartTwo = new ApexCharts(document.querySelector("#chart-two"), optionsTwo);
    chartOne.render();
    chartTwo.render();

    this.getData = async function (params = {}, type = 'GET') {
        return $.ajax({
            url: AJAX_PATH.salesReport,
            data: params,
            type: type,
            cache: false,
        });
    }
    this.compile = function (labels, data, isEmpty) {

        let options = {
            optionsOne: {
                labels: labels,
                series: [{data: data.C8}, {data: data.C3}, {data: data.C8_C3}]
            },
            optionsTwo: {
                labels: labels,
                series: [{data: data.C8}, {data: data.C6}, {data: data.C7}, {data: data.C4}, {data: data.C0}]
            },
        }
        if (isEmpty) {
            this.setEmpty();
            return false;
        }
        chartOne.updateOptions(options.optionsOne);
        chartTwo.updateOptions(options.optionsTwo);
    }
    this.setLoadingChart = () => {
        $('.chart-area').append('<div class="card-disabled"><div class="card-portlets-loader"></div></div>');
    }
    this.removeLoadingChart = () => {
        let load = $('.chart-area').find('.card-disabled');
        setTimeout(function () {
            $(load).fadeOut('fast', function () {
                $(load).remove();
            });
        }, 500);
    }
    this.setEmpty = () => {
        chartOne.resetSeries();
        chartTwo.resetSeries();
        $('#chart-one').html('No data...');
        $('#chart-two').html('No data...');
    }
    this.search = async function (params) {
        this.setLoadingChart();
        try {
            let {labels, counter, data, isEmpty} = await this.getData(params, 'POST');

            numberCounterResult.html(counterTemplate(counter));
            this.compile(labels, data, isEmpty);
            this.removeLoadingChart();
        } catch (e) {
            console.log(e);
        }
    }
    this.renderView = async function (params = {}) {
        try {
            let {labels, counter, data, isEmpty} = await this.getData(params);
            numberCounterResult.html(counterTemplate(counter));
            this.compile(labels, data, isEmpty);
        } catch (e) {
            console.log(e);
        }
    }
}
