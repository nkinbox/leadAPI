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
            call_register: false,
            search_call_register: false,
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
        show_search_result: false,
        date: {
            start: moment().startOf('day'),
            end: moment().endOf('day')
        },
        duration: {
            text: 'Any Duration',
            start: 0,
            end: 0
        },
        filter_logs: 'overview',
        search_filter_logs: 'overview',
        call_register: {
            current_page: 0,
            has_next: 0,
            summary: {},
            logs: []
        },
        search_call_register: {
            current_page: 0,
            has_next: 0,
            summary: {},
            logs: []
        },
        chartData: {
            series: [],
            categories: []
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
        },
        searchFilters(state) {
            let filter = {}
            if(state.search_query) {
                if(isNaN(state.search_query)) {
                    filter.saved_name = state.search_query
                } else {
                    filter.phone_number = state.search_query
                }
            }
            return filter
        },
        filteredLogs(state) {
            return (state.filter_logs == 'overview')?state.call_register.logs:state.call_register.logs.filter((log) => log.call_type == state.filter_logs)
        },
        searchFilteredLogs(state) {
            return (state.search_filter_logs == 'overview')?state.search_call_register.logs:state.search_call_register.logs.filter((log) => log.call_type == state.search_filter_logs)
        }
    },
    mutations: {
        setChartData(state, chartData) {
            state.chartData = chartData
        },
        setShowSearchResult(state, show_search_result) {
            state.show_search_result = show_search_result
        },
        setSearchQuery(state, search_query) {
            state.search_query = search_query
        },
        setFilterLog(state, filterBy) {
            state.filter_logs = filterBy
        },
        setSearchFilterLog(state, filterBy) {
            state.search_filter_logs = filterBy
        },
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
        setSearchCallRegister(state, search_call_register) {
            state.search_call_register = search_call_register
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
            context.commit('setFilterLog', 'overview')
            context.commit('setCallRegister', {
                current_page: 0,
                has_next: 0,
                summary: {},
                logs: []
            })
            axios.get('https://www.tripclues.in/leadAPI/public/api/logger/display', {
                params: context.getters.filters
            }).then(response => {
                context.commit('setCallRegister', {
                    current_page: 0,
                    has_next: 0,
                    summary: response.data.summary,
                    logs: response.data.logs
                })
                context.commit('loadingState', {name: 'call_register', isLoading: false})
            }).catch(error => {
                console.log(error)
                context.commit('loadingState', {name: 'call_register', isLoading: false})
            })
        },
        fetchSearchCallRegister(context) {
            context.commit('loadingState', {name: 'search_call_register', isLoading: true})
            context.commit('setSearchFilterLog', 'overview')
            context.commit('setSearchCallRegister', {
                current_page: 0,
                has_next: 0,
                summary: {},
                logs: []
            })
            axios.get('https://www.tripclues.in/leadAPI/public/api/logger/display', {
                params: context.getters.searchFilters
            }).then(response => {
                context.commit('setSearchCallRegister', {
                    current_page: 0,
                    has_next: 0,
                    summary: response.data.summary,
                    logs: response.data.logs
                })
                context.commit('loadingState', {name: 'search_call_register', isLoading: false})
            }).catch(error => {
                console.log(error)
                context.commit('loadingState', {name: 'search_call_register', isLoading: false})
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
        },
        setSearchQuery(context, search_query) {
            if(context.state.search_query != search_query) {
                context.commit('setShowSearchResult', 1)
                context.commit('setSearchQuery', search_query)
                context.dispatch('fetchSearchCallRegister')
            }
        },
        fetchAnalytics(context) {
            // context.commit('loadingState', {name: 'agent', isLoading: true})
            axios.get('https://www.tripclues.in/leadAPI/public/api/logger/analytics?type=time&date=2019-11-09').then(response => {
                context.commit('setChartData', response.data)
            }).catch(error => {
                console.log(error)
                // context.commit('loadingState', {name: 'agent', isLoading: false})
            })
        }
    }
})