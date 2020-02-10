<template>
    <div class="alert alert-dismissible m-3 shadow" :class="{'alert-success':!hasError, 'alert-danger':hasError}" v-if="hasAlert">
        <div><span>{{message}}</span></div>
        <ul v-if="hasError">
            <template v-for="(error, key) in errors">
                <li v-for="(message, index) in error" :key="key+index">{{message}}</li>
            </template>
        </ul>
        <button type="button" class="close" @click="close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</template>

<script>
export default {
    name: 'toast',
    computed: {
        message() {
            return this.$store.state.toast.message
        },
        errors() {
            return this.$store.state.toast.errors
        },
        hasError() {
            return Object.keys(this.errors).length
        },
        hasAlert() {
            return (this.hasError || this.message)
        }
    },
    methods: {
        close() {
            this.$store.commit('setError', {})
        }
    },
    updated() {
        if(this.hasAlert) {
            window.scrollTo(0,0)
            setTimeout(this.close, 3000)
        }
    }
}
</script>

<style>

</style>