<template>
  <div>
      <input class="form-control" type="search" placeholder="Search" ref="searchbox" v-model="query" @keyup.enter="search" @blur="goBack()" @keyup.esc="$event.target.blur()">
  </div>
</template>

<script>
export default {
    name: 'search-input',
    computed: {
        query: {
            get: function() {
                return this.$store.state.search_query
            },
            set: function(newVal) {
                this.$store.commit('setSearchQuery', newVal)
            }
        }
    },
    methods: {
        search() {
            if(this.query) {
                this.$store.commit('setShowSearchResult', 1)
                this.$store.dispatch('fetchSearchCallRegister')
            }
        },
        goBack() {
            this.$store.commit('setSearchQuery', '')
            this.$store.commit('setShowSearchResult', 0)
        }
    },
    created() {
        this.$store.subscribe((mutation, state) => {
            if(mutation.type === 'setSearchQuery' && state.search_query) {
                this.$nextTick(function() {
                    this.$refs.searchbox.focus()
                })
            }
        })
    }
}
</script>