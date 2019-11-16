<template>
  <div class="dialog" @click="goBack">
      <div class="card" @click="$event.stopPropagation()">
        <div v-if="loading" class="text-center p-4">
            <div class="spinner-border text-secondary" role="status" style="width: 4rem; height: 4rem;">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div v-else>
            <div class="card-header"><b>Selected Number</b> <span>{{phone_number}}</span></div>
            <div class="card-body">
                <select-agents class="mb-2" :show_sim="0"></select-agents>
                <select-website v-show="leadType == 'hotel'"></select-website>
                <select v-model="leadType" class="form-control mt-2">
                    <option value="hotel">Hotel Lead</option>
                    <option value="tour">Tour Lead</option>
                </select>
            </div>
            <div class="card-footer">
                <button @click="pushHotelLead" class="btn btn-primary text-capitalize">Push as {{leadType}} lead</button>
            </div>
        </div>
      </div>
  </div>
</template>

<script>
import SelectWebsite from './SelectWebsite'
import SelectAgents from './SelectAgents'

export default {
    name: 'push-lead',
    props: ['phone_number'],
    components: {
        SelectWebsite,
        SelectAgents
    },
    computed: {
        leadType: {
            set: function(newVal) {
                this.$store.commit('selectLeadType', newVal)
            },
            get: function() {
                return this.$store.state.lead_type
            }
        },
        loading() {
            return this.$store.state.loading['website']
        },
    },
    methods: {
        pushHotelLead() {
            this.$store.dispatch('pushToCRM')
        },
        goBack() {
            this.$router.replace({path:'/logs'})
        }
    },
    created() {
        this.$store.commit('selectPhone', this.phone_number)
        this.$store.dispatch('fetchWebsites')
    }
}
</script>
<style scoped>
.dialog {
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    top: 0;
    left: 0;
    z-index: 99999;
}
.card {
    width: 400px;
    margin-top: calc(50vh - 100px);
    margin-left: calc(50vw - 200px);
    opacity: 1;
    background: #fff;
}
</style>