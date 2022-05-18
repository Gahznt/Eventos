$(function () {
    const data = $('#chartData').data('data');

    const colors = {
        primary: '#313e8f',
        secondary: '#f7be4d',
        dark: '#34495e',
        success: '#49b7ac',
        info: '#477cc3',
        warning: '#f5a623',
        alert: '#d0305d'
    }

    const horizontalBarOptions = {
        legend: {display: false},
        scales: {
            xAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        },
        plugins: {
            datalabels: {
                color: 'white',
                font: {
                    weight: 'bold'
                }
            }
        }
    }

    const doughnutOptions = {
        legend: {
            position: 'right'
        },
        plugins: {
            labels: false,
            datalabels: {
                color: 'white',
                font: {
                    weight: 'bold'
                }
            }
        }
    }

    const barOptions = {
        plugins: {
            labels: {
                render: 'value',
                fontColor: colors.dark,
                fontStyle: 'bold',
            },
            datalabels: false
        }
    }

    // Artigos para Avaliação (Por designações)
    const chartArticle = $('#chartArticle');
    new Chart(chartArticle, {
        type: 'horizontalBar',
        data: {
            labels:  chartArticle.data('labels').toString().split(';'),
            datasets: [{
                label: chartArticle.data('label'),
                data:  chartArticle.data('values').toString().split(';'),
                backgroundColor: [
                    colors.primary,
                    colors.warning,
                    colors.info,
                    colors.dark
                ],
            }]
        },
        options: horizontalBarOptions
    });

    // Designações Efetuadas
    const chartMade = $('#chartMade');
    new Chart(chartMade, {
        type: 'doughnut',
        data: {
            labels:  chartMade.data('labels').toString().split(';'),
            datasets: [{
                data:  chartMade.data('values').toString().split(';'),
                backgroundColor: [
                    colors.success,
                    colors.alert
                ],
                borderWidth: 0,
            }]
        },
        options: doughnutOptions
    });

    // Artigos com 3 Designações
    const chartN3 = $('#chartN3');
    new Chart(chartN3, {
        type: 'doughnut',
        data: {
            labels:  chartN3.data('labels').toString().split(';'),
            datasets: [{
                data:  chartN3.data('values').toString().split(';'),
                backgroundColor: [
                    colors.success,
                    colors.alert
                ],
                borderWidth: 0,
            }]
        },
        options: doughnutOptions
    });

    // Artigos com 2 Designações
    const chartN2 = $('#chartN2');
    new Chart(chartN2, {
        type: 'doughnut',
        data: {
            labels:  chartN2.data('labels').toString().split(';'),
            datasets: [{
                data:  chartN2.data('values').toString().split(';'),
                backgroundColor: [
                    colors.success,
                    colors.alert
                ],
                borderWidth: 0,
            }]
        },
        options: doughnutOptions
    });

    // Artigos com 1 Designação
    const chartN1 = $('#chartN1');
    new Chart(chartN1, {
        type: 'doughnut',
        data: {
            labels:  chartN1.data('labels').toString().split(';'),
            datasets: [{
                data:  chartN1.data('values').toString().split(';'),
                backgroundColor: [
                    colors.success,
                    colors.alert
                ],
                borderWidth: 0,
            }]
        },
        options: doughnutOptions
    });

    // Conclusão por Divisões
    const chartConclusion = $('#chartConclusion');
    const labelsConclusion =  chartConclusion.data('labels').toString().split(';');

    new Chart(chartConclusion, {
        type: 'bar',
        data: {
            labels:  chartConclusion.data('vlabels').toString().split(';'),
            datasets: [{
                label: labelsConclusion[0],
                data:  chartConclusion.data('vvalues').toString().split(';'),
                backgroundColor: colors.success,
            }, {
                label: labelsConclusion[1],
                data:  chartConclusion.data('ivalues').toString().split(';'),
                backgroundColor: colors.alert,
            }]
        },
        options: barOptions
    });

    // Temas Por Divisão
    const labelsTheme =  $('#chartTheme').data('labels').toString().split(';');
    const chartTheme = new Chart($('#chartTheme'), {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: labelsTheme[0],
                data: [],
                backgroundColor: colors.success,
            }, {
                label: labelsTheme[1],
                data: [],
                backgroundColor: colors.alert,
            }]
        },
        options: barOptions
    });

    $('#filterTheme').on('click', function () {
        const val = $('#filter-theme').val();
        if (!val) {
            return;
        }

        const data = $('#chartData').data('data');

        if (!data['d_' + val]) {
            return;
        }
        const division = data['d_' + $('#filter-theme').val()];

        $('#divisionName').text(division.initials);
        chartTheme.data.labels = division.labels;
        chartTheme.data.datasets[0].data = division.valid_values;
        chartTheme.data.datasets[1].data = division.invalid_values;
        chartTheme.update();
    }).click();
});
