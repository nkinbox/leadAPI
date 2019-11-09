<template>
  <div>
      <ul class="list-group text-center">
        <li class="list-group-item pointer rounded-0" v-for="(stat, call_type) in summary" :key="call_type" @click="filterLogs(call_type)" :class="{'active':selected==call_type}">
            <div>
                <div class="h6 text-uppercase">{{call_type}}</div>
            </div>
            <div class="d-flex border-top pt-1">
                <div class="flex-fill">
                    <div class="small">Total</div>
                    <div>{{stat.total}}</div>
                </div>
                <div class="flex-fill border-left">
                    <div class="small">Unique</div>
                    <div>{{stat.unique}}</div>
                </div>
                <div v-if="stat.duration" class="flex-fill border-left">
                    <div class="small">Duration</div>
                    <div>{{stat.duration | readableSeconds}}</div>
                </div>
            </div>
        </li>
    </ul>
  </div>
</template>

<script>
export default {
    name: 'call-summary',
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
        summary() {
            return this.$store.state[this.prefix+'call_register'].summary
        },
        selected() {
            return this.$store.state[this.prefix+'filter_logs']
        }
    },
    methods: {
        filterLogs(call_type) {
            this.$store.commit((this.prefix?'setSearchFilterLog':'setFilterLog'), call_type)
        }
    },
    filters: {
        readableSeconds(totalSeconds) {
            let hours = Math.floor(totalSeconds / 3600)
            totalSeconds %= 3600
            let minutes = Math.floor(totalSeconds / 60)
            let seconds = totalSeconds % 60;
            return ((hours)?hours+'h ':'')+((minutes)?minutes+'m ':'')+((hours)?'':seconds+'s')
        }
    }
}
</script>