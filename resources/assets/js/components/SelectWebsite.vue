<template>
    <div>
        <select>
            <option v-for="(website, index) in websites" :key="index" :value="website.website">{{website.display_name}}</option>
        </select>
    </div>
</template>

<script>
export default {
    name: 'select-website',
    computed: {
        websites() {
            return this.$store.state.websites
        }
    }
}
</script>