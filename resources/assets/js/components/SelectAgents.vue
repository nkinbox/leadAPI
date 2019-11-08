<template>
  <div>
    <select v-model="selectedAgent" class="form-control">
        <option value="" v-if="!selectedAgent">All Agents</option>
        <option v-for="agent in agents" :key="agent.user_name" :value="agent.user_name">{{agent.user_name+' '+agent.name}}</option>
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
        agents() {
            return this.$store.getters.agentsByDepartment
        }
    }
}
</script>