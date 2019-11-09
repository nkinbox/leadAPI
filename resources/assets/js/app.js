import Vue from 'vue'
import VueRouter from 'vue-router'
import Dashboard from './components/Dashboard'
import CallLogs from './components/CallLogs'
import Search from './components/Search'
import {store} from './store'

const routes = [
    {
        path: '/',
        component: Dashboard
    },
    {
        path: '/logs',
        component: CallLogs
    }
]
Vue.use(VueRouter)
const router = new VueRouter({
    routes
})
const app = new Vue({
    el: '#app',
    router,
    store,
    components: {
        Search
    },
    mounted() {
        this.$store.dispatch('fetchDepartments')
        this.$store.dispatch('fetchAgents')
    }
});
