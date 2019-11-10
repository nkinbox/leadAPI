<template>
  <div>
    <div class="row no-gutters">
      <div class="col-9">
        <call-flow-chart class="m-2"></call-flow-chart>
      </div>
      <div class="col-3">
        <department-chart></department-chart>
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
  