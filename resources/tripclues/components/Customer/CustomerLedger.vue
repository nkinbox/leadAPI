<template>
    <div>
        <h4>Customer Ledger</h4>
        <table class="table table-borderless table-sm">
            <tbody>
                <tr>
                    <th colspan="5">Sales</th>
                </tr>
                <tr>
                    <th>Customer Name</th>
                    <td>{{customerName}}</td>
                    <th>Period</th>
                    <td class="text-center">{{date.from | dateFormat}}</td>
                    <td class="text-center">{{date.to | dateFormat}}</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{email}}</td>
                    <th>Phone</th>
                    <td colspan="2">{{phone}}</td>
                </tr>
            </tbody>
        </table>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Particular</th>
                    <th>Voucher Type</th>
                    <th>Booking ID</th>
                    <th>Bill No</th>
                    <th class="cell-shrink">Booking Amount Debit</th>
                    <th class="cell-shrink">Received Amount Credit</th>
                </tr>
            </thead>
            <tbody>
                <template v-if="loading">
                    <tr>
                        <td colspan="7">
                            <div class="spinner-border text-primary" role="status"></div>
                        </td>
                    </tr>
                </template>
                <template v-else>
                    <tr v-for="(item, index) in list" :key="index" :class="{'table-success':item.type == 'credit','table-warning':item.type == 'debit'}">
                        <th scope="row">{{index+1}}</th>
                        <td>{{item.date | dateFormat}}</td>
                        <td>{{item.particular}}</td>
                        <td>{{item.voucher}}</td>
                        <td><a :href="item.booking_url" target="_blank">{{item.booking_number}}</a></td>
                        <td><router-link :to="{name:'SalesInvoice', params: {table: item.booking_type.toLowerCase(), id: item.bill_id}}">HTSL{{item.bill_id}}</router-link></td>
                        <td class="text-right">{{item.type=='debit'?'₹'+item.amount:'0'}}</td>
                        <td class="text-right">{{item.type=='credit'?'₹'+item.amount:'0'}}</td>
                    </tr>
                    <tr class="text-right">
                        <th colspan="5">Current Total</th>
                        <td>₹{{total.debit}}</td>
                        <td>₹{{total.credit}}</td>
                    </tr>
                    <tr class="text-right">
                        <th colspan="5">Closing Total</th>
                        <td :class="{'bg-warning':(closingTotal.type == 'debit' && closingTotal.amount>0)}">{{closingTotal.type == 'debit'?'₹'+closingTotal.amount:'Nil'}}</td>
                        <td :class="{'bg-danger text-white':(closingTotal.type == 'credit' && closingTotal.amount>0)}">{{closingTotal.type == 'credit'?(closingTotal.amount>0)?'₹'+closingTotal.amount:'Nil':'Nil'}}</td>
                    </tr>
                </template>
                
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: 'customer-ledger',
    props: {
        table: String,
        id: [String, Number]
    },
    computed: {
        loading() {
            return this.$store.state.loading.customer_ledger
        },
        customerName() {
            return this.$store.state.customerLedger.customer_name
        },
        email() {
            return this.$store.state.customerLedger.email
        },
        phone() {
            return this.$store.state.customerLedger.phone
        },
        date() {
            return this.$store.state.customerLedger.date
        },
        list() {
            return this.$store.state.customerLedger.list
        },
        total() {
            return this.list.reduce((sum, item) => {
                if(item.type == 'credit')
                sum.credit += item.amount
                else
                sum.debit += item.amount
                return sum
            }, {credit: 0, debit: 0})
        },
        closingTotal() {
            let diff = this.total.debit - this.total.credit
            return {
                type: (diff > 0)?'debit':'credit',
                amount: Math.abs(diff)
            }
        }
    },
    mounted() {
        this.$store.dispatch('getCustomerLedger', {table: this.table, id: this.id})
    }
}
</script>

<style>

</style>