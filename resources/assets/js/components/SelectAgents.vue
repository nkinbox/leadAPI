<template>
  <div>
    <select v-model="selectedAgent" class="form-control">
        <option value="" v-if="!selectedAgent">All Agents</option>
        <option v-for="agent in agents" :key="agent.user_name" :value="agent.user_name">{{agent.user_name+' '+agent.name}}</option>
    </select>
    <select v-if="selectedAgent" v-model="selectedSim">
        <option value="">Any SIM</option>
        <option v-for="sim_allocation in sim_allocations" :key="sim_allocation.id" value="sim_allocation.id">{{sim_allocation.phone_number + ' - ' + sim_allocation.sim_name?sim_allocation.sim_name:(sim_allocation.is_personal?'Personal':'')}}</option>
    </select>
  </div>
</template>

<script>
export default {
    name: 'select-agents',
    computed: {
        loading() {
            return this.$store.state.loading.agent
        },
        selectedAgent: {
            get: function() {
                return this.$store.state.selected_agent.user_name
            },
            set: function(user_name) {
                let agent = this.agents.find(agent => agent.user_name == user_name)
                if(agent)
                this.$store.dispatch('setAgent', agent)
            },
        },
        selectedSim: {
            get: function() {
                return this.$store.state.selected_sim_id
            },
            set: function(sim_id) {
                this.$store.dispatch('selectSimID', sim_id)
            },
        },
        agents() {
            return this.$store.getters.agentsByDepartment
        },
        sim_allocations() {
            return this.$store.state.selected_agent.sim_allocations
        }
    }
}
</script>