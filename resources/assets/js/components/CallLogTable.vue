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
                    <th scope="col">Department</th>
                    <th scope="col">Icon</th>
                    <th scope="col">Conversed</th>
                    <th scope="col">Call Type</th>
                    <th scope="col">Duration</th>
                    <th scope="col">Time</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody class="text-white">
                <tr v-for="(log, index) in records" :key="log.id" :class="{'bg-danger':(log.duration?false:true), 'bg-success':(log.duration?true:false)}">
                    <th scope="row" v-text="index+1"></th>
                    <td class="small" v-html="log.agent_name+'<br>'+log.agent_phone_number"></td>
                    <td><label class="badge badge-dark text-light">{{log.department_name}}</label></td>
                    <td ><img class="mr-1" v-if="(log.call_type != 'incoming' && log.call_type != 'outgoing')" :src="(log.call_type == 'busy')?'vuejs/outgoing.png':'vuejs/incoming.png'"><img :src="'vuejs/'+log.call_type+'.png'"></td>
                    <td class="small pointer" @click="search(log.phone_number)" v-html="(log.saved_name?log.saved_name:'NA')+'<br>'+log.dial_code+' '+log.phone_number"></td>
                    <td>
                        <div v-text="log.call_type" class="badge text-uppercase" :class="{'badge-info':(log.call_type == 'outgoing' || log.call_type == 'busy'), 'badge-warning':(log.call_type == 'incoming' || log.call_type == 'missed' || log.call_type == 'rejected')}"></div>
                    </td>
                    <td>{{log.duration | readableSeconds}}</td>
                    <td class="text-nowrap">{{log.device_time | formatDate}}</td>
                    <td><button class="btn btn-sm btn-primary" @click="pushLead(log.phone_number)">&#10142;</button></td>
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
    props: {
        prefix: {
            type: String,
            default: ''
        }
    },
    computed: {
        loading() {
            return this.$store.state.loading[this.prefix+'call_register']
        },
        records() {
            return (this.prefix)?this.$store.getters.searchFilteredLogs:this.$store.getters.filteredLogs
        }
    },
    methods: {
        search(number) {
            this.$store.dispatch('setSearchQuery', number)
        },
        pushLead(number) {
            this.$router.push({name:'push_lead', props:{phone_number:number}})
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