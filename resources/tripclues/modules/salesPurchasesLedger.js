import axios from 'axios'
import moment from 'moment'

export const salesPurchasesLedger = {
    state: {
        List: {
            data: [],
            links: {
                first: null,
                last: null,
                prev: null,
                next: null
            },
            meta: {
                current_page: 1,
                from: null,
                last_page: 1,
                path: '',
                per_page: 0,
                to: null,
                total: 0
            }
        },
        filters: {
            date_start: moment().format('YYYY-MM-DD'),
            date_end: moment().format('YYYY-MM-DD')
        }
    },
    getters: {
    },
    mutations: {
        setLedgerFilterValue(state, payload) {
            let filters = {...state.filters}
            filters[payload.field] = payload.value
            state.filters = filters
        },
        setLedgerList(state, List) {
            state.List = List
        }
    },
    actions: {
        getLedgerList(context, page) {
            context.commit('loadingState', {name: 'sales_purchases_ledger', isLoading: true})
            axios.get(page, {
                params: context.state.filters
            }).then(response => {
                context.commit('setLedgerList', response.data)
                context.commit('loadingState', {name: 'sales_purchases_ledger', isLoading: false})
            }).catch(error => {
                if (error.response) {
                    context.commit('setError', error.response.data)
                }
                context.commit('loadingState', {name: 'sales_purchases_ledger', isLoading: false})
            })
        }
    }
}