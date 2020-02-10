import axios from 'axios'
export const customerPurchase = {
    state: {
        seller_name: '',
        email: '',
        phone: '',
        address: '',
        date: {
            from: '',
            to: ''
        },
        list: []
    },
    getters: {
    },
    mutations: {
        setCustomerPurchase(state, data) {
            Object.keys(data).map(key => {
                state[key] = data[key]
            })
        }
    },
    actions: {
        getCustomerPurchase(context, payload) {
            context.commit('loadingState', {name: 'customer_purchase', isLoading: true})
            axios.get('ledger/purchase/'+payload.table+'/'+payload.id).then(response => {
                context.commit('setCustomerPurchase', response.data)
                context.commit('loadingState', {name: 'customer_purchase', isLoading: false})
            }).catch(error => {
                if (error.response) {
                    context.commit('setError', error.response.data)
                }
                context.commit('loadingState', {name: 'customer_purchase', isLoading: false})
            })
        }
    }
}