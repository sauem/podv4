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
            data: [202, 505, 414, 160, 227, 302, 201, 352, 752, 320, 257, 160]
        }, {
            name: 'C3',
            type: 'column',
            data: [440, 505, 414, 671, 227, 413, 201, 352, 752, 320, 257, 160]
        }, {
            name: 'C8/C3',
            type: 'line',
            data: [23, 42, 35, 27, 43, 22, 17, 31, 22, 22, 12, 16]
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
        labels: ['01 Jan 2001', '02 Jan 2001', '03 Jan 2001', '04 Jan 2001', '05 Jan 2001', '06 Jan 2001', '07 Jan 2001', '08 Jan 2001', '09 Jan 2001', '10 Jan 2001', '11 Jan 2001', '12 Jan 2001'],
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
            data: [44, 55, 41, 67, 22, 43]
        }, {
            name: 'C6 (Cancel)',
            data: [13, 23, 20, 8, 13, 27]
        }, {
            name: 'C7 (Callback)',
            data: [11, 17, 15, 15, 21, 14]
        }, {
            name: 'C4 (Number fail)',
            data: [21, 7, 25, 13, 22, 8]
        }, {
            name: 'C0 (New)',
            data: [21, 7, 25, 13, 22, 8]
        }],
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


    this.getData = async function () {
        return $.ajax({
            url: AJAX_PATH.salesReport,
            data: {},
            cache: false,
        });
    }


    this.renderView = async function () {
        try {
            let {labels, counter, data} = await this.getData();
            optionsOne.labels = labels;
            optionsOne.series[0].data = data.C8;
            optionsOne.series[1].data = data.C3;
            optionsOne.series[2].data = data.C8_C3;

            let chartOne = new ApexCharts(document.querySelector("#chart-one"), optionsOne);
            // chart 2
            optionsTwo.labels = labels;
            optionsTwo.series[0].data = data.C8;
            optionsTwo.series[1].data = data.C6;
            optionsTwo.series[2].data = data.C7;
            optionsTwo.series[3].data = data.C4;
            optionsTwo.series[4].data = data.C0;
            let chartTwo = new ApexCharts(document.querySelector("#chart-two"), optionsTwo);

            numberCounterResult.html(counterTemplate(counter));
            chartOne.render();
            chartTwo.render();
        } catch
            (e) {
            console.log(e);
        }
    }
}