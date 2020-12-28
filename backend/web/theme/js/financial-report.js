function initFinancialChart() {

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
            enabled: true,
            enabledOnSeries: [0, 1]
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

    const chartOne = new ApexCharts(document.querySelector("#chart-one"), optionsOne);
    const chartTwo = new ApexCharts(document.querySelector("#chart-two"), optionsOne);

    this.getData = async function (task = "overview") {
        return $.ajax({
            url: AJAX_PATH.financialReport,
            data: {task: task},
            cache: false,
        });
    }
    this.render = function () {

        return {
            overview: function () {
                chartOne.render();
                chartTwo.render();
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
