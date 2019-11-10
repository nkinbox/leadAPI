<template>
  <apexchart type=pie width=320 :options="chartOptions" :series="series" />
</template>

<script>
import VueApexCharts from 'vue-apexcharts'

export default {
    name: 'department-chart',
    components: {
        apexchart: VueApexCharts
    },
    computed: {
        series() {
            return this.departments.series
        },
        departments() {
            return this.$store.state.departments.reduce((chartData, department) => {
                if(department.id) {
                    chartData.labels.push(department.name)
                    chartData.series.push(department.total_agents)
                }
                return chartData
            }, {labels:[],series:[]})
        },
        chartOptions() {
            return {
                labels: this.departments.labels,
                responsive: [{
                  breakpoint: 480,
                  options: {
                    chart: {
                      width: 200
                    },
                    legend: {
                      position: 'right'
                    }
                  }
                }]
            }
        }
    }
}
</script>