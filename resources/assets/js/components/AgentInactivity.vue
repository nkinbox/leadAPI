<template>
    <div>
        <h5 class="text-center my-2">App Activity <button class="btn btn-primary float-right mr-2 btn-sm badge-pill" @click="refreshAgents">&#8634;</button></h5>

        <div v-if="loading" class="text-center py-4">
            <div class="spinner-border text-secondary" role="status" style="width: 4rem; height: 4rem;">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <ul v-else class="list-group m-3 overflow-y">
            <li class="list-group-item pointer d-flex" v-for="agent in agents" :key="agent.agent_id" :class="{'bg-danger text-white':!agent.is_active}">
                <div class="flex-fill d-flex"><div class="px-1 border-right user_name">{{agent.user_name}}</div><div class="px-2 pl-4 text-truncate name">{{agent.name}}</div></div>
                <div class="text-center"><kbd>{{agent.last_update_at | formatDate}}</kbd></div>
            </li>
        </ul>
    </div>
</template>

<script>
import moment from 'moment'
export default {
    name: 'agent-inactivity',
    computed: {
        loading() {
            return this.$store.state.loading.agent
        },
        agents() {
            return this.$store.state.agents.sort((a, b) => {
                if(a.last_update_at == null || b.last_update_at == null)
                return -1
                else {
                    return moment(a.last_update_at, 'YYYY-MM-DD HH-mm-ss').diff(moment(b.last_update_at, 'YYYY-MM-DD HH-mm-ss'))
                }
            })
        }
    },
    filters: {
        formatDate(rawDate) {
            if(rawDate == null)
            return 'UNINSTALLED'
            return moment(rawDate, 'YYYY-MM-DD HH-mm-ss').format('hh:mm A, DD MMM')
        }
    },
    methods: {
        refreshAgents() {
            this.$store.dispatch('fetchAgents')
        }
    }
}
</script>
<style scoped>
.user_name {
    width: 60px;
    display: inline-block;
}
.name {
    max-width: calc(100% - 100px);
}
.overflow-y {
    overflow-y: scroll;
    height: 250px;
}
</style>