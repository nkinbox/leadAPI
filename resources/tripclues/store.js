import Vue from 'vue'
import Vuex from 'vuex'

import {customerLedger} from './modules/customerLedger'
import {customerPurchase} from './modules/customerPurchase'
import {purchaseInvoice} from './modules/purchaseInvoice'
import {salesInvoice} from './modules/salesInvoice'
import {salesPurchasesLedger} from './modules/salesPurchasesLedger'

Vue.use(Vuex)

export const store = new Vuex.Store({
    modules: {
        customerLedger,
        customerPurchase,
        purchaseInvoice,
        salesInvoice,
        salesPurchasesLedger
    },
    state: {
        toast: {
            message: '',
            errors: []
        },
        loading: {
            sales_purchases_ledger: false,
            customer_ledger: false,
            customer_purchase: false,
            purchase_invoice: false,
            sales_invoice: false,
        }
    },
    getters: {
        
    },
    mutations: {
        loadingState(state, loading) {
            state.loading[loading.name] = loading.isLoading
        },
        setError(state, error) {
            let log = {
                message: '',
                errors: []
            }
            let keys = Object.keys(error)
            if(keys.length) {
                keys.forEach((key) => {
                    log[key] = error[key]
                })
            } else {
                log['errors'] = [['Some Error Occured']]
            }
            state.toast = log
        }
    },
    actions: {
        getUsers(context) {
            axios.get('/user').then(response => {
                context.commit('setUsers', response.data)
            }).catch(error => {
                if (error.response) {
                    context.commit('setError', error.response.data)
                }
            })
        }
    }
})