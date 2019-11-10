<template>
  <div>
    <div class="d-flex justify-content-center py-2">
        <select-department class="mr-1"></select-department>
        <select-agents class="mr-1"></select-agents>
        <div class="mr-1">
            <input class="form-control" type="date" v-model="date">
        </div>
        <div class="mr-1">
            <select class="form-control" v-model="type">
                <option v-for="(text, val) in types" :value="val" :key="val">{{text}}</option>
            </select>
        </div>
        <div class="mr-1">
            <button class="btn btn-primary text-nowrap" type="button" @click="refreshChart">
                <span v-show="loading" class="spinner-border spinner-border-sm"></span>
                <span>Refresh</span>
            </button>
        </div>
    </div>
    <apexchart type=bar height=400 :options="chartOptions" :series="series" />
  </div>
</template>

<script>
import VueApexCharts from 'vue-apexcharts'
import SelectDepartment from './SelectDepartment'
import SelectAgents from './SelectAgents'

export default {
    name: 'call-flow-chart',
    components: {
        apexchart: VueApexCharts,
        SelectDepartment,
        SelectAgents,
    },
    data() {
        return {
            types: {
                time: 'Hours Slot',
                days: 'Daily',
                months: 'Monthly'
            }
        }
    },
    computed: {
        loading() {
            return this.$store.state.loading.call_flow_chart
        },
        type: {
            get: function() {
                return this.$store.state.call_flow_chart_type
            },
            set: function(value) {
                this.$store.commit('setCallFlowChartType', value)
            }
        },
        date: {
            get: function() {
                return this.$store.getters.callFlowChartFilter.date
            },
            set: function(value) {
                this.$store.commit('setCallFlowFilterDate', value)
            }
        },
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
                    categories: this.$store.state.call_flow_chart.categories,
                },
                legend: {
                    position: 'bottom',
                },
                fill: {
                    opacity: 1
                }
            }
        },
        series() {
            return this.$store.state.call_flow_chart.series
        }
    },
    methods: {
      refreshChart() {
        this.$store.dispatch('fetchAnalytics')
      }
    }

}
</script>