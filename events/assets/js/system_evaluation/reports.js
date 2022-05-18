$(function () {
  $('#pendingReasonModal').on('show.bs.modal', function (e) {
    const data = $(e.relatedTarget).data('data');
    $('#pendingReasonModal .fill').each(function () {
      $(this).text(data[$(this).data('field')]);
    });
  });

  $('#authorModal').on('show.bs.modal', function (e) {
    const data = $(e.relatedTarget).data('data');
    $('#authorModal .value').each(function () {
      $(this).text(data[$(this).data('field')]);
    });
  });

  $('#tableModal').on('show.bs.modal', function (e) {
    const data = $(e.relatedTarget).data('data');
    $('#tableModal .value').each(function () {
      $(this).html(data[$(this).data('field')]);
    });
  });

  // chart
  const colors = [ '#313e8f', '#f7be4d', '#34495e', '#49b7ac', '#477cc3', '#f5a623', '#d0305d' ];

  const chartTotal = $('#chartTotal');
  if(chartTotal.length > 0) {
    new Chart(chartTotal, {
      type: 'line',
      data: {
        labels: Object.keys(chartTotal.data('data')),
        datasets: [{
          label: chartTotal.data('label'),
          fill: false,
          borderColor: '#477cc3',
          data: Object.values(chartTotal.data('data'))
        }]
      },
      options: {
        plugins: {
          labels: false,
          datalabels: false
        },
        tooltips: {
          mode: 'index',
          intersect: false,
          callbacks: {
            labelColor: function(i, c) {
              return {
                backgroundColor: c.config.data.datasets[i.datasetIndex].borderColor
              }
            }
          }
        },
        legend: {
          display: false
        }
      }
    });
  }

  const chartLast = $('#chartLast');
  if(chartLast.length > 0) {
    const data = chartLast.data('data');
    new Chart(chartLast, {
      type: 'line',
      data: {
        labels: Object.keys(data[0].data),
        datasets: data.map((d, i) => {
          return {
            label: d.label,
            data: Object.values(d.data),
            fill: false,
            borderColor: colors[i]
          }
        })
      },
      options: {
        plugins: {
          labels: false,
          datalabels: false
        },
        tooltips: {
          mode: 'index',
          intersect: false,
          callbacks: {
            title: function(i) {
              return `${chartLast.data('day')} ${i[0].label}`;
            },
            labelColor: function(i, c) {
              return {
                backgroundColor: c.config.data.datasets[i.datasetIndex].borderColor
              }
            }
          }
        }
      }
    });
  }
});