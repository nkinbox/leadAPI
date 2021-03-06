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
            call_flow_chart: false,
            website: false,
            crm: false
        },
        departments: [],
        agents: [],
        selected_department_id: 0,
        selected_sim_id: '',
        selected_agent: {
            department_id: 0,
            department_name: '',
            name: '',
            user_name: '',
            sim_allocations: []
        },
        search_query: '',
        show_search_result: false,
        call_log_type: 'external',
        date: {
            start: moment().startOf('day'),
            end: moment().endOf('day')
        },
        duration: {
            text: 'Any Duration',
            start: 0,
            end: 0
        },
        filter_logs: 'overview_total',
        search_filter_logs: 'overview_total',
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
        call_flow_chart_type: 'time',
        call_flow_date: moment().startOf('day'),
        call_flow_chart: {
            series: [],
            categories: []
        },
        websites: [],
        selected_website_id: 0,
        selected_phone: '',
        lead_type: 'hotel'
    },
    getters: {
        callFlowChartFilter(state) {
            let filter = {}
            filter.type = state.call_flow_chart_type
            filter.date = state.call_flow_date.format('YYYY-MM-DD')
            if(state.selected_agent.agent_id) {
                filter.agent_id = state.selected_agent.agent_id
            } else if(state.selected_department_id){
                filter.department_id = state.selected_department_id
            }
            filter.call_log_type = state.call_log_type
            return filter
        },
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
                filter.call_log_type = state.call_log_type
                filter.sim_allocation_id = state.selected_sim_id
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
            switch (state.filter_logs) {
                case 'overview_unique':
                return state.call_register.logs.filter((log) => log.latest)
                case 'untouched_total':
                return state.call_register.logs.filter((log) => log.latest && !log.has_duration)
                case 'untouched_incoming':
                return state.call_register.logs.filter((log) => log.latest && !log.has_duration && (log.call_type == 'missed' || log.call_type == 'rejected'))
                case 'untouched_outgoing':
                return state.call_register.logs.filter((log) => log.latest && !log.has_duration && (log.call_type == 'busy'))
                case 'incoming_total':
                return state.call_register.logs.filter((log) => log.call_type == 'incoming')
                case 'incoming_unique':
                return state.call_register.logs.filter((log) => log.call_type == 'incoming' && log.call_type_latest)
                case 'outgoing_total':
                return state.call_register.logs.filter((log) => log.call_type == 'outgoing')
                case 'outgoing_unique':
                return state.call_register.logs.filter((log) => log.call_type == 'outgoing' && log.call_type_latest)
                case 'unattended_missed':
                return state.call_register.logs.filter((log) => log.call_type == 'missed' && log.latest && log.call_type_latest && log.has_duration)
                case 'unattended_rejected':
                return state.call_register.logs.filter((log) => log.call_type == 'rejected' && log.latest && log.has_duration && log.call_type_latest)
                case 'unattended_busy':
                return state.call_register.logs.filter((log) => log.call_type == 'busy' && log.latest && log.has_duration && log.call_type_latest)
                default:
                return state.call_register.logs
            }
        },
        searchFilteredLogs(state) {
            switch (state.search_filter_logs) {
                case 'overview_unique':
                return state.search_call_register.logs.filter((log) => log.latest)
                case 'untouched_total':
                return state.search_call_register.logs.filter((log) => log.latest && !log.has_duration)
                case 'untouched_incoming':
                return state.search_call_register.logs.filter((log) => log.latest && !log.has_duration && (log.call_type == 'missed' || log.call_type == 'rejected'))
                case 'untouched_outgoing':
                return state.search_call_register.logs.filter((log) => log.latest && !log.has_duration && (log.call_type == 'busy'))
                case 'incoming_total':
                return state.search_call_register.logs.filter((log) => log.call_type == 'incoming')
                case 'incoming_unique':
                return state.search_call_register.logs.filter((log) => log.call_type == 'incoming' && log.call_type_latest)
                case 'outgoing_total':
                return state.search_call_register.logs.filter((log) => log.call_type == 'outgoing')
                case 'outgoing_unique':
                return state.search_call_register.logs.filter((log) => log.call_type == 'outgoing' && log.call_type_latest)
                case 'unattended_missed':
                return state.search_call_register.logs.filter((log) => log.call_type == 'missed' && log.latest && log.call_type_latest && log.has_duration)
                case 'unattended_rejected':
                return state.search_call_register.logs.filter((log) => log.call_type == 'rejected' && log.latest && log.has_duration && log.call_type_latest)
                case 'unattended_busy':
                return state.search_call_register.logs.filter((log) => log.call_type == 'busy' && log.latest && log.has_duration && log.call_type_latest)
                default:
                return state.search_call_register.logs
            }
        },
        pushToCRMData(state) {
            let data = {}
            if(state.selected_phone && state.selected_agent.user_name) {
                data.phone_number = state.selected_phone
                data.user_name = state.selected_agent.user_name
            } else {
                return null
            }
            if(state.lead_type == 'hotel') {
                if(state.selected_website_id)
                data.id = state.selected_website_id
                else return null
            }
            data.type = state.lead_type
            return data
        }
    },
    mutations: {
        selectLeadType(state, lead_type) {
            state.lead_type = lead_type
        },
        selectPhone(state, phone_number) {
            state.selected_phone = phone_number
        },
        selectWebsite(state, website_id) {
            state.selected_website_id = website_id
        },
        setWebsites(state, websites) {
            state.websites = websites
        },
        setCallLogType(state, call_log_type) {
            state.call_log_type = call_log_type
        },
        setCallFlowChartType(state, type) {
            state.call_flow_chart_type = type
        },
        setCallFlowChart(state, data) {
            state.call_flow_chart = data
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
        selectSimID(state, sim_id) {
            state.selected_sim_id = sim_id
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
        },
        setCallFlowFilterDate(state, rawDate) {
            if(rawDate)
            state.call_flow_date = moment(rawDate)
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
                agent_id: 0,
                department_id: 0,
                department_name: '',
                name: '',
                user_name: '',
                sim_allocations: []
            })
            context.commit('selectSimID', '')
            context.commit('selectWebsite', 0)
            context.commit('setWebsites', [])
            context.dispatch('fetchCallRegister')
        },
        setAgent(context, agent) {
            context.commit('selectAgent', agent)
            context.commit('selectDepartment', agent.department_id)
            context.commit('selectSimID', '')
            context.commit('selectWebsite', 0)
            context.commit('setWebsites', [])
            context.dispatch('fetchCallRegister')
        },
        setSearchQuery(context, search_query) {
            if(context.state.search_query != search_query) {
                context.commit('setShowSearchResult', 1)
                context.commit('setSearchQuery', search_query)
                context.dispatch('fetchSearchCallRegister')
            }
        },
        fetchAnalytics(context) {
            context.commit('loadingState', {name: 'call_flow_chart', isLoading: true})
            context.commit('setCallFlowChart', {
                categories: [],
                series: []
            })
            axios.get('https://www.tripclues.in/leadAPI/public/api/logger/analytics', {
                params: context.getters.callFlowChartFilter
            }).then(response => {
                context.commit('setCallFlowChart', response.data)
                context.commit('loadingState', {name: 'call_flow_chart', isLoading: false})
            }).catch(error => {
                console.log(error)
                context.commit('loadingState', {name: 'call_flow_chart', isLoading: false})
            })
        },
        fetchWebsites(context) {
            context.commit('loadingState', {name: 'website', isLoading: true})
            axios.get('https://www.tripclues.in/leadAPI/public/api/logger/websites', {
                params: {user_name: context.state.selected_agent.user_name}
            }).then(response => {
                context.commit('setWebsites', response.data)
                context.commit('loadingState', {name: 'website', isLoading: false})
            }).catch(error => {
                console.log(error)
                context.commit('loadingState', {name: 'website', isLoading: false})
            })
        },
        pushToCRM(context) {
            context.commit('loadingState', {name: 'crm', isLoading: true})
            return new Promise((resolve, reject) => {
                axios.post('https://www.tripclues.in/leadAPI/public/api/logger/pushToCRM', context.getters.pushToCRMData).then(response => {
                    console.log(response)
                    context.commit('loadingState', {name: 'crm', isLoading: false})
                    context.commit('selectWebsite', 0)
                    context.commit('selectPhone', '')
                    resolve()
                })
                .catch(error => {
                    console.log(error)
                    context.commit('selectWebsite', 0)
                    context.commit('selectPhone', '')
                    context.commit('loadingState', {name: 'crm', isLoading: false})
                    reject()
                })
            })
        }
    }
})