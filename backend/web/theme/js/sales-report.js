function initSaleChart() {
    this.numberCounterTemplate = $("#sales-template").html();
    this.numberCounterResult = $("#sales-result");

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
        },
        stroke: {
            width: [0, 0, 4]
        },

        dataLabels: {
            enabled: true,
            enabledOnSeries: [2]
        },
        labels: ['01 Jan 2001', '02 Jan 2001', '03 Jan 2001', '04 Jan 2001', '05 Jan 2001', '06 Jan 2001', '07 Jan 2001', '08 Jan 2001', '09 Jan 2001', '10 Jan 2001', '11 Jan 2001', '12 Jan 2001'],
        xaxis: {
            type: 'datetime'
        },
        legend: {
            position: 'top',
            fontSize: '15px',
            horizontalAlign: 'center',
            offsetY: 10,
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
            {
                opposite: true,
                title: {
                    text: 'C8/C13(%)'
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
        xaxis: {
            type: 'datetime',
            categories: ['01/01/2011 GMT', '01/02/2011 GMT', '01/03/2011 GMT', '01/04/2011 GMT',
                '01/05/2011 GMT', '01/06/2011 GMT'
            ],
        },
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


    this.getData = async function f() {
        return $.ajax({
            url: AJAX_PATH.salesReport,
            data: {},
            cache: false,
        });
    }


    this.renderView = function () {
        chartOne.render();
        chartTwo.render();
    }
}