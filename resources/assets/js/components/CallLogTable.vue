<template>
  <div>
      <div v-if="loading" class="text-center p-4">
        <div class="spinner-border text-secondary" role="status" style="width: 4rem; height: 4rem;">
            <span class="sr-only">Loading...</span>
        </div>
      </div>
      <div v-else-if="records.length">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Employee</th>
                    <th scope="col">Conversed</th>
                    <th scope="col">Call Type</th>
                    <th scope="col">Duration</th>
                    <th scope="col">Time</th>
                </tr>
            </thead>
            <tbody class="text-white">
                <tr v-for="(log, index) in records" :key="index" :class="{'bg-danger':(log.duration?false:true), 'bg-success':(log.duration?true:false)}">
                    <th scope="row" v-text="index+1"></th>
                    <td class="small" v-html="log.agent_name+'<br>'+log.agent_phone_number"></td>
                    <td class="small" v-html="(log.saved_name?log.saved_name:'NA')+'<br>'+log.dial_code+' '+log.phone_number"></td>
                    <td>
                        <div v-text="log.call_type" class="badge font-weight-normal" :class="{'badge-info':(log.call_type == 'outgoing' || log.call_type == 'busy'), 'badge-warning':(log.call_type == 'incoming' || log.call_type == 'missed' || log.call_type == 'rejected')}"></div>
                    </td>
                    <td>{{log.duration | readableSeconds}}</td>
                    <td>{{log.timestamp | formatDate}}</td>
                </tr>
            </tbody>
        </table>
      </div>
      <div v-else class="p-2">
        <div class="alert alert-danger text-center h5 p-3">No Record Found</div>
      </div>
  </div>
</template>

<script>
import moment from 'moment'
export default {
    name: 'call-log-table',
    computed: {
        loading() {
            return this.$store.state.loading.call_register
        },
        records() {
            return this.$store.getters.filteredLogs
        }
    },
    filters: {
        readableSeconds(totalSeconds) {
            let hours = Math.floor(totalSeconds / 3600)
            totalSeconds %= 3600
            let minutes = Math.floor(totalSeconds / 60)
            let seconds = totalSeconds % 60;
            return ((hours)?hours+'h ':'')+((minutes)?minutes+'m ':'')+((hours)?'':seconds+'s')
        },
        formatDate(rawDate) {
            return moment(rawDate, 'YYYY-MM-DD HH-mm-ss').format('hh:mm A, DD MMM')
        }
    }
}
</script>