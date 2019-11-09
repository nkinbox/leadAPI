<template>
  <div>
    <div class="row no-gutters">
      <div class="col-3"><show-agents class="p-3"></show-agents></div>
      <div class="col-9"></div>
    </div>
  </div>
</template>

<script>
import ShowAgents from './ShowAgents'
export default {
    name: 'dashboard',
    components: {
      ShowAgents
    }
}
</script>