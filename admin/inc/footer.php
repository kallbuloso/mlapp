<footer class="footer py-4  ">
  <div class="container-fluid">
    <div class="row align-items-center justify-content-lg-between">
      <div class="col-lg-6 mb-lg-0 mb-4">
        <div class="copyright text-center text-sm text-muted text-lg-start">
          © <script>
            document.write(new Date().getFullYear())
          </script>,
          <a href="<?= APP_URL; ?>" class="font-weight-bold" target="_blank"><?= APP_NAME; ?></a>
        </div>
      </div>

    </div>
  </div>
</footer>
</div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js"></script>

<!--   Core JS Files   -->

<script src="assets/js/core/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>


<script src="assets/js/core/bootstrap.min.js"></script>
<script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="assets/js/plugins/smooth-scrollbar.min.js"></script>
<script src="assets/js/plugins/chartjs.min.js"></script>
<script src="assets/js/plugins/toaster.js"></script>
<script src="assets/js/functions.js"></script>

<?php if($page == "dashboard"){ ?>
<script>
var ctx = document.getElementById("chart-bars").getContext("2d");

new Chart(ctx, {
type: "bar",
data: {
  labels: <?= json_encode($init_products->lastPagesViewsGrapich->daysNameGrapich); ?>,
  datasets: [{
    label: "Visitas",
    tension: 0.4,
    borderWidth: 0,
    borderRadius: 4,
    borderSkipped: false,
    backgroundColor: "rgba(255, 255, 255, .8)",
    data: <?= json_encode($init_products->lastPagesViewsGrapich->daysQtdGrapich); ?>,
    maxBarThickness: 6
  }, ],
},
options: {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false,
    }
  },
  interaction: {
    intersect: false,
    mode: 'index',
  },
  scales: {
    y: {
      grid: {
        drawBorder: false,
        display: true,
        drawOnChartArea: true,
        drawTicks: false,
        borderDash: [5, 5],
        color: 'rgba(255, 255, 255, .2)'
      },
      ticks: {
        suggestedMin: 0,
        suggestedMax: 500,
        beginAtZero: true,
        padding: 10,
        font: {
          size: 14,
          weight: 300,
          family: "Roboto",
          style: 'normal',
          lineHeight: 2
        },
        color: "#fff"
      },
    },
    x: {
      grid: {
        drawBorder: false,
        display: true,
        drawOnChartArea: true,
        drawTicks: false,
        borderDash: [5, 5],
        color: 'rgba(255, 255, 255, .2)'
      },
      ticks: {
        display: true,
        color: '#f8f9fa',
        padding: 10,
        font: {
          size: 14,
          weight: 300,
          family: "Roboto",
          style: 'normal',
          lineHeight: 2
        },
      }
    },
  },
},
});


var ctx2 = document.getElementById("chart-line").getContext("2d");

new Chart(ctx2, {
type: "line",
data: {
  labels: <?= json_encode($init_transactions->lastMonths12Name); ?>,
  datasets: [{
    label: "Últimos 12 meses",
    tension: 0,
    borderWidth: 0,
    pointRadius: 5,
    pointBackgroundColor: "rgba(255, 255, 255, .8)",
    pointBorderColor: "transparent",
    borderColor: "rgba(255, 255, 255, .8)",
    borderColor: "rgba(255, 255, 255, .8)",
    borderWidth: 4,
    backgroundColor: "transparent",
    fill: true,
    data: <?= json_encode($init_transactions->formatGraphic12MonthsTransactions); ?>,
    maxBarThickness: 6

  }],
},
options: {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false,
    }
  },
  interaction: {
    intersect: false,
    mode: 'index',
  },
  scales: {
    y: {
      grid: {
        drawBorder: false,
        display: true,
        drawOnChartArea: true,
        drawTicks: false,
        borderDash: [5, 5],
        color: 'rgba(255, 255, 255, .2)'
      },
      ticks: {
        display: true,
        color: '#f8f9fa',
        padding: 10,
        font: {
          size: 14,
          weight: 300,
          family: "Roboto",
          style: 'normal',
          lineHeight: 2
        },
      }
    },
    x: {
      grid: {
        drawBorder: false,
        display: false,
        drawOnChartArea: false,
        drawTicks: false,
        borderDash: [5, 5]
      },
      ticks: {
        display: true,
        color: '#f8f9fa',
        padding: 10,
        font: {
          size: 14,
          weight: 300,
          family: "Roboto",
          style: 'normal',
          lineHeight: 2
        },
      }
    },
  },
},
});

var ctx3 = document.getElementById("chart-line-tasks").getContext("2d");

new Chart(ctx3, {
type: "line",
data: {
  labels: <?= json_encode($init_clients->lastMonths12Name); ?>,
  datasets: [{
    label: "Clientes cadastrados",
    tension: 0,
    borderWidth: 0,
    pointRadius: 5,
    pointBackgroundColor: "rgba(255, 255, 255, .8)",
    pointBorderColor: "transparent",
    borderColor: "rgba(255, 255, 255, .8)",
    borderWidth: 4,
    backgroundColor: "transparent",
    fill: true,
    data: <?= json_encode($init_clients->formatGraphic12MonthsClients); ?>,
    maxBarThickness: 6

  }],
},
options: {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false,
    }
  },
  interaction: {
    intersect: false,
    mode: 'index',
  },
  scales: {
    y: {
      grid: {
        drawBorder: false,
        display: true,
        drawOnChartArea: true,
        drawTicks: false,
        borderDash: [5, 5],
        color: 'rgba(255, 255, 255, .2)'
      },
      ticks: {
        display: true,
        padding: 10,
        color: '#f8f9fa',
        font: {
          size: 14,
          weight: 300,
          family: "Roboto",
          style: 'normal',
          lineHeight: 2
        },
      }
    },
    x: {
      grid: {
        drawBorder: false,
        display: false,
        drawOnChartArea: false,
        drawTicks: false,
        borderDash: [5, 5]
      },
      ticks: {
        display: true,
        color: '#f8f9fa',
        padding: 10,
        font: {
          size: 14,
          weight: 300,
          family: "Roboto",
          style: 'normal',
          lineHeight: 2
        },
      }
    },
  },
},
});
</script>
<?php } ?> 

<script>
  var win = navigator.platform.indexOf('Win') > -1;
  if (win && document.querySelector('#sidenav-scrollbar')) {
  var options = {
    damping: '0.5'
  }
  Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
  }
</script>
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="assets/js/material-dashboard.min.js?v=3.0.4"></script>
</body>

</html>
