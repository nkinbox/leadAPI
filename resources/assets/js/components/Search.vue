<template>
  <div>
    <search-input></search-input>
    <div class="position-relative mt-1">
        <div id="searchResult" v-if="searchActive" class="position-absolute row no-gutters" @mousedown="$event.preventDefault()">
            <div class="col-3">
                <call-summary :prefix="'search_'" class="overflow-y"></call-summary>
            </div>
            <div class="col-9">
                <call-log-table :prefix="'search_'" class="overflow-y"></call-log-table>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
import CallLogTable from './CallLogTable'
import CallSummary from './CallSummary'
import SearchInput from './SearchInput'

export default {
    name: 'search',
    components: {
        CallLogTable,
        CallSummary,
        SearchInput
    },
    computed: {
        searchActive() {
            return this.$store.state.show_search_result
        }
    }
}
</script>

<style scoped>
#searchResult {
    width: 70vw;
    height: 90vh;
    right: 0;
    top: 0;
    z-index: 1;
    background: #fff;
    box-shadow: -3px 3px 5px 5px #aaa;
}
.overflow-y {
    overflow-y: scroll;
    height: 90vh;
}
</style>