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
            data: null
        }, {
            name: 'C3',
            type: 'column',
            data: null
        }, {
            name: 'C8/C3',
            type: 'line',
            data: null
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
                    formatter: function (val) {
                        return val.toFixed(0);
                    }
                },
                tooltip: {
                    enabled: true
                }
            },
            {
                seriesName: 'C8',
                axisTicks: {show: false},
                axisBorder: {show: false},
                labels: {show: false}
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
    this.compile = function (labels, data) {

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
        chartOne.updateOptions(options.optionsOne);
        chartTwo.updateOptions(options.optionsTwo);
    }
    this.search = async function (params) {
        try {
            let {labels, counter, data} = await this.getData(params, 'POST');

            numberCounterResult.html(counterTemplate(counter));
            this.compile(labels, data);
        } catch (e) {

        }
    }
    this.renderView = async function (params = {}) {
        try {
            let {labels, counter, data} = await this.getData(params);
            numberCounterResult.html(counterTemplate(counter));
            this.compile(labels, data);

        } catch
            (e) {
            console.log(e);
        }
    }
}