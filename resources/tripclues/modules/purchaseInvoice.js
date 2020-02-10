import axios from 'axios'
export const purchaseInvoice = {
    state: {
        seller_name: '',
        city: '',
        email: '',
        address: '',
        phone: '',
        date: '',
        particular: '',
        checkin: '',
        checkout: '',
        no_of_rooms: '',
        adults: '',
        children: '',
        extra_bed: '',
        booking_amount: '',
        booking_id: '',
        booking_url: ''
    },
    getters: {
    },
    mutations: {
        setPurchaseInvoice(state, data) {
            Object.keys(data).map(key => {
                state[key] = data[key]
            })
        }
    },
    actions: {
        getPurchaseInvoice(context, payload) {
            context.commit('loadingState', {name: 'purchase_invoice', isLoading: true})
            axios.get('ledger/invoice/purchase/'+payload.table+'/'+payload.id).then(response => {
                context.commit('setPurchaseInvoice', response.data)
                context.commit('loadingState', {name: 'purchase_invoice', isLoading: false})
            }).catch(error => {
                if (error.response) {
                    context.commit('setError', error.response.data)
                }
                context.commit('loadingState', {name: 'purchase_invoice', isLoading: false})
            })
        }
    }
}