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
    computed: {
      chartOptions() {
        return {
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
            categories: this.$store.state.chartData.categories,
          },
          legend: {
            position: 'left',
            offsetY: 40
          },
          fill: {
            opacity: 1
          }
        }
      },
      series() {
        return this.$store.state.chartData.series
      }
    },
    mounted() {
      this.$store.dispatch('fetchAnalytics')
    }
}
</script>
  