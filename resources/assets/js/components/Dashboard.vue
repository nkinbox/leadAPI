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
          <ul class="list-group">
              <li class="list-group-item pointer d-flex align-items-center">
                  <div class="text-center flex-fill">Employee 2</div>
                  <div class="border">&nbsp;</div>
                  <div class="text-center flex-fill">Employee 1</div>
              </li>
          </ul>
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
  