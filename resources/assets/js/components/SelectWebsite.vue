<template>
    <div>
        <div class="d-flex">
            <select v-model="selectWebsite" class="form-control flex-fill">
                <option v-for="website in websites" :key="website.id" :value="website.id">{{website.display_name}}</option>
            </select>
            <button class="btn btn-primary" @click="refresh">&#8634;</button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'select-website',
    computed: {
        selectWebsite: {
            set: function(newVal) {
                this.$store.commit('selectWebsite', newVal)
            },
            get: function() {
                return this.$store.state.selected_website_id
            }
        },
        websites() {
            return this.$store.state.websites
        }
    },
    methods: {
        refresh() {
            this.$store.dispatch('fetchWebsites')
        }
    }
}
</script>