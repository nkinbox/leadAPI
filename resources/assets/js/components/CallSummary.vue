<template>
  <div>
      <ul class="list-group text-center">
        <li class="list-group-item pointer rounded-0" v-for="(stat, call_type) in summary" :key="call_type">
            <div>
                <div class="h6 text-uppercase">{{call_type}}</div>
            </div>
            <div class="d-flex border-top pt-1">
                <div v-for="(filter, filter_type) in stat" :key="filter_type" @click="filterLogs(filter.name)" class="flex-fill py-1" :class="{'bg-primary text-white':selected==filter.name, 'border-left':(filter_type!='total' && filter_type!='missed')}">
                    <div class="small text-uppercase">{{filter_type}}</div>
                    <div v-if="filter.name">{{filter.value}}</div>
                    <div v-else>{{filter.value | readableSeconds}}</div>
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
            if(call_type)
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