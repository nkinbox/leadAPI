<template>
  <div class="p-2 h6 mb-0">
      <div class="d-flex justify-content-center">
        <select class="rounded-left text-secondary" v-model="date">
            <option v-for="i in 31" :key="i">{{i}}</option>
        </select>
        <select class="border-left-0 border-right-0 text-secondary" v-model="month">
            <option>Jan</option>
            <option>Feb</option>
            <option>Mar</option>
            <option>Apr</option>
            <option>May</option>
            <option>Jun</option>
            <option>Jul</option>
            <option>Aug</option>
            <option>Sept</option>
            <option>Oct</option>
            <option>Nov</option>
            <option>Dec</option>
        </select>
        <select class="rounded-right text-secondary" v-model="year">
            <option>2019</option>
            <option>2020</option>
            <option>2021</option>
            <option>2022</option>
        </select>
      </div>
      <div class="d-flex justify-content-center p-1">
        <select class="rounded-left border-right-0 text-secondary" v-model="hour">
            <option v-for="(i, index) in 24" :key="index">{{index}}</option>
        </select>
        <select class="rounded-right text-secondary" v-model="minute">
            <option v-for="(i, index) in 60" :key="index" :value="index">{{('0'+index).slice(-2)}}</option>
        </select>
      </div>
  </div>
</template>

<script>
export default {
    name: 'date-time',
    props: ['type'],
    computed: {
        year: {
            get: function() {
                return this.$store.state.date[this.type].format('YYYY')
            },
            set: function(newVal) {
                this.$store.commit('setDate', {
                    type: this.type,
                    fn: 'year',
                    value: newVal
                })
            }
        },
        month: {
            get: function() {
                return this.$store.state.date[this.type].format('MMM')
            },
            set: function(newVal) {
                this.$store.commit('setDate', {
                    type: this.type,
                    fn: 'month',
                    value: newVal
                })
            }
        },
        date: {
            get: function() {
                return this.$store.state.date[this.type].format('D')
            },
            set: function(newVal) {
                this.$store.commit('setDate', {
                    type: this.type,
                    fn: 'date',
                    value: newVal
                })
            }
        },
        hour: {
            get: function() {
                return this.$store.state.date[this.type].format('H')
            },
            set: function(newVal) {
                this.$store.commit('setDate', {
                    type: this.type,
                    fn: 'hour',
                    value: newVal
                })
            }
        },
        minute: {
            get: function() {
                return this.$store.state.date[this.type].format('m')
            },
            set: function(newVal) {
                this.$store.commit('setDate', {
                    type: this.type,
                    fn: 'minute',
                    value: newVal
                })
            }
        }
    }
}
</script>