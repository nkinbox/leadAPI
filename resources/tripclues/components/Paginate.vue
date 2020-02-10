<template>
    <div>
        <div v-if="meta.total" class="mb-1">Showing <b>{{meta.from}}</b> - <b>{{meta.to}}</b> of <b>{{meta.total}}</b></div>
        <div v-else class="alert alert-danger m-3">No Records Found.</div>
        <div>
            <ul class="pagination pagination-sm">
                <li class="page-item" :class="{disabled:!links.first}"><a class="page-link" href="#" @click.prevent="firstPage">&lt;&lt; First</a></li>
                <li class="page-item" :class="{disabled:!links.prev}"><a class="page-link" href="#" @click.prevent="prevPage">&lt; Prev</a></li>
                <li class="page-item" :class="{disabled:!links.next}"><a class="page-link" href="#" @click.prevent="nextPage">Next &gt;</a></li>
                <li class="page-item" :class="{disabled:!links.last}"><a class="page-link" href="#" @click.prevent="lastPage">Last &gt;&gt;</a></li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    name: 'paginate',
    props: ['links', 'meta', 'action'],
    methods: {
        firstPage() {
            if(this.links.first)
            this.$store.dispatch(this.action, this.links.first)
        },
        lastPage() {
            if(this.links.last)
            this.$store.dispatch(this.action, this.links.last)
        },
        nextPage() {
            if(this.links.next)
            this.$store.dispatch(this.action, this.links.next)
        },
        prevPage() {
            if(this.links.prev)
            this.$store.dispatch(this.action, this.links.prev)
        }
    }
}
</script>

<style>

</style>