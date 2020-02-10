import axios from 'axios'
export const customerLedger = {
    state: {
        customer_name: '',
        email: '',
        phone: '',
        date: {
            from: '',
            to: ''
        },
        list: []
    },
    getters: {
    },
    mutations: {
        setCustomerLedger(state, data) {
            Object.keys(data).map(key => {
                state[key] = data[key]
            })
        }
    },
    actions: {
        getCustomerLedger(context, payload) {
            context.commit('loadingState', {name: 'customer_ledger', isLoading: true})
            axios.get('ledger/customer/'+payload.table+'/'+payload.id).then(response => {
                context.commit('setCustomerLedger', response.data)
                context.commit('loadingState', {name: 'customer_ledger', isLoading: false})
            }).catch(error => {
                if (error.response) {
                    context.commit('setError', error.response.data)
                }
                context.commit('loadingState', {name: 'customer_ledger', isLoading: false})
            })
        }
    }
}