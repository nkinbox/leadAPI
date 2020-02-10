<template>
    <div>
        <h4>All Booking Ledger</h4>
        <div class="pb-1 w-50">
            <div class="d-flex align-items-center justify-content-between">
                <div>Date Period : </div>
                <div><input class="form-control-plaintext" type="date" v-model="date_start"></div>
                <div>to</div>
                <div><input class="form-control-plaintext" type="date" v-model="date_end"></div>
                <div>
                    <button class="btn btn-success btn-sm" @click="refresh">Refresh</button>
                </div>
            </div>
        </div>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Party Name</th>
                    <th>Voucher Type</th>
                    <th>Booking Type</th>
                    <th>Booking ID</th>
                    <th>Invoice No</th>
                    <th class="cell-shrink">Total Booking Amount</th>
                </tr>
            </thead>
            <tbody>
                <template v-if="loading">
                    <tr>
                        <td colspan="8">
                            <div class="spinner-border text-primary" role="status"></div>
                        </td>
                    </tr>
                </template>
                <template v-else>
                    <tr v-for="(item, index) in list.data" :key="index">
                        <th scope="row">{{list.meta.from+index}}</th>
                        <td>{{item.date | dateFormat}}</td>
                        <td><router-link :to="{ name: 'CustomerLedger', params: { table: item.booking_type.toLowerCase(), id: item.lead_id }}">{{item.customer_name}}</router-link></td>
                        <td>Sale</td>
                        <td>{{item.booking_type}}</td>
                        <td><a :href="item.booking_url" target="_blank">{{item.booking_id}}</a></td>
                        <td><router-link :to="{name:'SalesInvoice', params: {table: item.booking_type.toLowerCase(), id: item.lead_id}}">{{item.customer_invoice}}</router-link></td>
                        <td class="text-right">₹{{item.booking_amount}}</td>
                    </tr>
                    <tr class="text-right">
                        <th colspan="7">Total</th>
                        <td>₹{{total}}</td>
                    </tr>
                </template>
            </tbody>
        </table>
        <paginate :links="list.links" :meta="list.meta" :action="'getLedgerList'"></paginate>
    </div>
</template>

<script>
import Paginate from '../Paginate'
import moment from 'moment'

export default {
    name: 'sales-ledger',
    components: {
        Paginate
    },
    computed: {
        loading() {
            return this.$store.state.loading.sales_purchases_ledger
        },
        list() {
            return this.$store.state.salesPurchasesLedger.List
        },
        total() {
            return this.list.data.reduce((sum, item) => sum += item.booking_amount, 0)
        },
        date_start: {
            get() {
                return this.$store.state.salesPurchasesLedger.filters.date_start
            },
            set(val) {
                this.$store.commit('setLedgerFilterValue', {field: 'date_start', value: ((val)?moment(val).format('YYYY-MM-DD'):'')})
            }
        },
        date_end: {
            get() {
                return this.$store.state.salesPurchasesLedger.filters.date_end
            },
            set(val) {
                this.$store.commit('setLedgerFilterValue', {field: 'date_end', value: ((val)?moment(val).format('YYYY-MM-DD'):'')})
            }
        }
    },
    methods: {
        refresh() {
            this.$store.dispatch('getLedgerList', '/ledger/sales_purchases')
        }
    },
    mounted() {
        if(this.list.data.length == 0 && !this.loading) {
            this.$store.dispatch('getLedgerList', '/ledger/sales_purchases')
        }
    }
}
</script>

<style>
    .cell-shrink {
        width: 1px;
    }
</style>