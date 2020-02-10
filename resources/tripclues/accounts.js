import Vue from 'vue'
import VueRouter from 'vue-router'
import {store} from './store'
import {routes} from './routes'

import Toast from './components/Toast'
import axios from 'axios'
import moment from 'moment'

axios.defaults.baseURL = 'https://www.tripclues.in/leadAPI/public/api'
Vue.use(VueRouter)
const router = new VueRouter({
    routes
})
Vue.filter('dateFormat', function (date) {
    return date?moment(date).format('DD-MMM-YY'):'NA'
})
Vue.filter('dateTimeFormat', function (date) {
    return date?moment(date).format('DD-MMM-YY h:mm a'):'NA'
})
const app = new Vue({
    el: '#app',
    router,
    store,
    components: {
        Toast
    },
    methods: {
        goBack() {
            this.$router.go(-1)
        }
    },
    mounted() {
        // this.$store.dispatch('getUsers')
    }
});
