<template>
    <div>
        <select v-model="selectWebsite">
            <option v-for="website in websites" :key="website.id" :value="website.id">{{website.display_name}}</option>
        </select>
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
    }
}
</script>