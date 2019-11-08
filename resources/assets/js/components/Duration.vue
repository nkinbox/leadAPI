<template>
  <div>
      <select class="form-control" @change="changeDuartion($event.target.value)">
          <option v-for="(option, index) in options" :key="index" :value="index" :selected="index == duration">{{option.text}}</option>
      </select>
  </div>
</template>

<script>
export default {
    name: 'duration',
    data() {
        return {
            options: [
                {
                    text: 'Any Duration',
                    start: 0,
                    end: 0
                },
                {
                    text: 'Less than 1 Minutes',
                    start: 0,
                    end: 59
                },
                {
                    text: 'Between 1 and < 3 Minutes',
                    start: 60,
                    end: 179
                },
                {
                    text: 'Between 3 and < 5 Minutes',
                    start: 180,
                    end: 299
                },
                {
                    text: 'Between 5 and < 10 Minutes',
                    start: 300,
                    end: 599
                },
                {
                    text: 'Between 10 and < 20 Minutes',
                    start: 600,
                    end: 1199
                },
                {
                    text: 'More than 20 Minutes',
                    start: 1200,
                    end: 172800
                }
            ]
        }
    },
    computed: {
        duration() {
            let duration = this.$store.state.duration
            return this.options.findIndex((option) => (option.start == duration.start && option.end == duration.end))
        }
    },
    methods: {
        changeDuartion(index) {
            let option = this.options.find((opt, i) => i == index)
            this.$store.commit('setDuration', option)
        }
    }
}
</script>

<style>

</style>