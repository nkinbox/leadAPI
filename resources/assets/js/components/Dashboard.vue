<template>
  <div>
    <div class="row no-gutters">
      <div class="col-3"><show-agents class="p-3"></show-agents></div>
      <div class="col-9">
        <apexchart type=bar height=350 :options="chartOptions" :series="series" />
      </div>
    </div>
  </div>
</template>

<script>
import VueApexCharts from 'vue-apexcharts'
import ShowAgents from './ShowAgents'
export default {
    name: 'dashboard',
    components: {
      ShowAgents,
      apexchart: VueApexCharts
    },
    data() {
      return {
        series: [{
          name: 'PRODUCT A',
          data: [44, 55, 41, 67, 22, 430]
        }, {
          name: 'PRODUCT B',
          data: [13, 23, 20, 8, 13, 27]
        }, {
          name: 'PRODUCT C',
          data: [11, 17, 15, 15, 21, 14]
        }, {
          name: 'PRODUCT D',
          data: [21, 7, 25, 13, 22, 8]
        }],
        chartOptions: {
          chart: {
            stacked: true,
            toolbar: {
              show: false
            },
            zoom: {
              enabled: false
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

          xaxis: {
            type: 'category',
            categories: [
              '12AM - 2AM',
              '2AM - 4AM',
              '4AM - 6AM',
              '6AM - 8AM',
              '8AM - 10AM',
              '10AM - 12PM'
            ],
          },
          legend: {
            position: 'left',
            offsetY: 40
          },
          fill: {
            opacity: 1
          }
        }
      }
    },

}
</script>
  