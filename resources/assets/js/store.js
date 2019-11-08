import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import moment from 'moment'
let token = document.head.querySelector('meta[name="csrf-token"]')
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
if(token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
}
Vue.use(Vuex)

export const store = new Vuex.Store({
    state: {
        loading: {
            department: false,
            agent: false,
            call_register: false
        },
        departments: [],
        agents: [],
        selected_department_id: 0,
        selected_agent: {
            department_id: 0,
            department_name: '',
            name: '',
            user_name: ''
        },
        search_query: '',
        date: {
            start: moment().startOf('day'),
            end: moment().endOf('day')
        },
        duration: {
            text: 'Any Duration',
            start: 0,
            end: 0
        },
        call_register: {
            current_page: 0,
            has_next: 0,
            logs: []
        }
    },
    getters: {
        agentsByDepartment(state) {
            return state.agents.filter((agent) => {
                return state.selected_department_id == 0 || agent.department_id == state.selected_department_id
            })
        },
        filters(state) {
            let filter = {}
            if(state.search_query) {
                if(isNaN(state.search_query)) {
                    filter.saved_name = state.search_query

                } else {
                    filter.phone_number = state.search_query
                }
            } else {
                filter.start_datetime = state.date.start.second(0).format('YYYY-MM-DD HH:mm:ss')
                filter.end_datetime = state.date.end.second(59).format('YYYY-MM-DD HH:mm:ss')
                if(state.duration.start || state.duration.end) {
                    filter.duration_start = state.duration.start
                    filter.duration_end = state.duration.end
                }
                if(state.selected_agent.user_name) {
                    filter.user_name = state.selected_agent.user_name
                    filter.department_id = state.selected_agent.department_id
                } else if(state.selected_department_id){
                    filter.department_id = state.selected_department_id
                }
                // filter.page = state.call_register.current_page + 1
            }
            return filter
        }
    },
    mutations: {
        setDepartments(state, departments) {
            state.departments = departments
        },
        selectDepartment(state, department_id) {
            state.selected_department_id = department_id
        },
        setAgents(state, agents) {
            state.agents = agents
        },
        selectAgent(state, agent) {
            state.selected_agent = agent
        },
        loadingState(state, loading) {
            state.loading[loading.name] = loading.isLoading
        },
        setCallRegister(state, call_register) {
            state.call_register = call_register
        },
        setDuration(state, duration) {
            state.duration = duration
        },
        setDate(state, date) {
            switch (date.fn) {
                case 'year':
                    state.date[date.type] = moment(state.date[date.type]).year(date.value)
                    break;
                case 'month':
                    state.date[date.type] = moment(state.date[date.type]).month(date.value)
                    break;
                case 'date':
                    state.date[date.type] = moment(state.date[date.type]).date(date.value)
                    break;
                case 'hour':
                    state.date[date.type] = moment(state.date[date.type]).hour(date.value)
                    break;
                case 'minute':
                    state.date[date.type] = moment(state.date[date.type]).minute(date.value)
                    break;
            }
        }
    },
    actions: {
        fetchDepartments(context) {
            context.commit('loadingState', {name: 'department', isLoading: true})
            axios.get('https://www.tripclues.in/leadAPI/public/api/logger/departments').then(response => {
                context.commit('setDepartments', response.data.departments)
                context.commit('loadingState', {name: 'department', isLoading: false})
            }).catch(error => {
                console.log(error)
                context.commit('loadingState', {name: 'department', isLoading: false})
            })
        },
        fetchAgents(context) {
            context.commit('loadingState', {name: 'agent', isLoading: true})
            axios.get('https://www.tripclues.in/leadAPI/public/api/logger/agents').then(response => {
                context.commit('setAgents', response.data.agents)
                context.commit('loadingState', {name: 'agent', isLoading: false})
            }).catch(error => {
                console.log(error)
                context.commit('loadingState', {name: 'agent', isLoading: false})
            })
        },
        fetchCallRegister(context) {
            context.commit('loadingState', {name: 'call_register', isLoading: true})
            axios.get('https://www.tripclues.in/leadAPI/public/api/logger/display', {
                params: context.getters.filters
            }).then(response => {
                context.commit('setCallRegister', {
                    current_page: 0,
                    has_next: 0,
                    logs: response.data.logs
                })
                context.commit('loadingState', {name: 'call_register', isLoading: false})
            }).catch(error => {
                console.log(error)
                context.commit('loadingState', {name: 'call_register', isLoading: false})
            })
        },
        setDepartment(context, department_id) {
            context.commit('selectDepartment', department_id)
            if(context.state.selected_agent.department_id != department_id)
            context.commit('selectAgent', {
                department_id: 0,
                department_name: '',
                name: '',
                user_name: ''
            })
        },
        setAgent(context, agent) {
            context.commit('selectAgent', agent)
            context.commit('selectDepartment', agent.department_id)
        }
    }
})