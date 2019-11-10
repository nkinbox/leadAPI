<template>
  <div>
    <div class="row no-gutters">
      <div class="col-8 border-right">
        <h5 class="text-center my-2">Call Flow Chart</h5>
        <call-flow-chart class="m-2"></call-flow-chart>
      </div>
      <div class="col-4">        
        <h5 class="text-center my-2">Departments</h5>
        <department-chart></department-chart>
        <div class="border-top my-2">
        <h5 class="text-center my-2">Employee Engagement</h5>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import CallFlowChart from './CallFlowChart'
import DepartmentChart from './DepartmentChart'

export default {
    name: 'dashboard',
    components: {
      CallFlowChart,
      DepartmentChart
    },
    mounted() {
      this.$store.dispatch('fetchAnalytics')
    }
}
</script>
  