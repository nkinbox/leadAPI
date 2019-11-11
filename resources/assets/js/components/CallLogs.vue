<template>
  <div>
    <div class="d-flex justify-content-center align-items-center p-1 alert-primary">
        <date-time :type="'start'"></date-time>
        <date-time :type="'end'"></date-time>
        <select-department class="p-2"></select-department>
        <select-agents class="p-2"></select-agents>
        <duration class="p-2"></duration>
        <div class="p-2">
            <select class="form-control" v-model="call_log_type">
                <option value="">Any</option>
                <option value="agent">Internal</option>
                <option value="external">External</option>
            </select>
        </div>
        <button class="btn btn-primary" @click="fetchCallRegister">Search</button>
    </div>
    <div class="row no-gutters">
        <div class="col-3">
            <call-summary></call-summary>
        </div>
        <div class="col-9">
            <call-log-table></call-log-table>
        </div>
    </div>
  </div>
</template>

<script>
import SelectDepartment from './SelectDepartment'
import SelectAgents from './SelectAgents'
import Duration from './Duration'
import DateTime from './DateTime'
import CallLogTable from './CallLogTable'
import CallSummary from './CallSummary'

export default {
    name: 'call-logs',
    components: {
        SelectDepartment,
        SelectAgents,
        Duration,
        DateTime,
        CallLogTable,
        CallSummary
    },
    created() {
        this.fetchCallRegister()
    },
    computed: {
        call_log_type: {
            get: function() {
                return this.$store.state.call_log_type
            },
            set: function(value) {
                this.$store.commit('setCallLogType', value)
            }
        }
    },
    methods: {
        fetchCallRegister() {
            this.$store.dispatch('fetchCallRegister')
        }
    }
}
</script>