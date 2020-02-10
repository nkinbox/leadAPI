import axios from 'axios'
export const salesInvoice = {
    state: {
        customer_name: '',
        email: '',
        phone: '',
        date: '',
        hotel_name: '',
        particular: '',
        checkin: '',
        checkout: '',
        no_of_rooms: '',
        adults: '',
        children: '',
        extra_bed: '',
        booking_amount: '',
        commission: '',
        booking_id: '',
        booking_url: '',
    },
    getters: {
    },
    mutations: {
        setSalesInvoice(state, data) {
            Object.keys(data).map(key => {
                state[key] = data[key]
            })
        }
    },
    actions: {
        getSalesInvoice(context, payload) {
            context.commit('loadingState', {name: 'sales_invoice', isLoading: true})
            axios.get('ledger/invoice/sale/'+payload.table+'/'+payload.id).then(response => {
                context.commit('setSalesInvoice', response.data)
                context.commit('loadingState', {name: 'sales_invoice', isLoading: false})
            }).catch(error => {
                if (error.response) {
                    context.commit('setError', error.response.data)
                }
                context.commit('loadingState', {name: 'sales_invoice', isLoading: false})
            })
        }
    }
}