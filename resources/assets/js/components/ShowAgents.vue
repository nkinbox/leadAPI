<template>
  <div>
    <ul class="list-group">
        <li class="list-group-item pointer d-flex" v-for="agent in agents" :key="agent.user_name" :class="{'bg-success text-white':(agent.user_name == selectedAgentUserName)}" @click="selectAgent(agent)">
            <div v-text="agent.user_name" class="pr-2"></div>
            <div v-text="agent.name"></div>
            <div class="flex-grow-1 text-right">
                <label v-text="agent.department_name" class="badge badge-secondary"></label>
            </div>
        </li>
    </ul>
  </div>
</template>

<script>
export default {
    name: 'show-agents',
    computed: {
        agents() {
            return this.$store.getters.agentsByDepartment
        },
        selectedAgentUserName() {
            return this.$store.state.selected_agent.user_name
        }
    },
    methods: {
        selectAgent(agent) {
            this.$store.dispatch('setAgent', agent)
        }
    }
}
</script>