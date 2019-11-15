<template>
  <div>
      <select-agents :show_sim="0"></select-agents>
      <div>{{phone_number}}</div>
      <select-website></select-website>
      <select v-model="leadType">
          <option value="hotel">Hotel Lead</option>
          <option value="tour">Tour Lead</option>
      </select>
      <button @click="pushHotelLead">Push</button>
  </div>
</template>

<script>
import SelectAgents from './SelectAgents'
import SelectWebsite from './SelectWebsite'

export default {
    name: 'push-lead',
    props: ['phone_number'],
    components: {
        SelectAgents,
        SelectWebsite
    },
    computed: {
        leadType: {
            set: function(newVal) {
                this.$store.commit('selectLeadType', newVal)
            },
            get: function() {
                return this.$store.state.lead_type
            }
        }
    },
    methods: {
        pushHotelLead() {
            this.$store.dispatch('pushToCRM')
        }
    }
}
</script>

<style>

</style>