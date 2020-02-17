<template>
    <div>
        <h4>Sales Invoice</h4>
        <table class="table table-borderless table-sm">
            <tr>
                <th class="cell-shrink">CustomerName</th>
                <td>{{customer_name}}</td>
                <th class="cell-shrink">BillNo</th>
                <td>HTSL{{id}}</td>
            </tr>
            <tr>
                <th>Date</th>
                <td>{{date | dateFormat}}</td>
                <th class="text-nowrap">Booking Ref</th>
                <td><a :href="booking_url">{{booking_id}}</a></td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{email}}</td>
                <th>Phone</th>
                <td>{{phone}}</td>
            </tr>
            <tr>
                <th colspan="4">{{particular}}</th>
            </tr>
        </table>
        <table class="table table-bordered table-sm">
            <thead>
                <th colspan="2" class="text-center">Description</th>
                <th class="cell-shrink">Quantity</th>
                <th class="cell-shrink text-nowrap">Rate Per</th>
                <th class="cell-shrink">Amount</th>
            </thead>
            <tbody>
                <tr>
                    <td class="text-right">CheckIn Date</td>
                    <td>{{checkin | dateFormat}}</td>
                    <td rowspan="4"></td>
                    <td rowspan="4"></td>
                    <td rowspan="4" class="align-middle">₹{{purchaseAmount}}</td>
                </tr>
                <tr>
                    <td class="text-right">CheckOut Date</td>
                    <td>{{checkout | dateFormat}}</td>
                </tr>
                <tr>
                    <td class="text-right">No of Rooms</td>
                    <td>{{no_of_rooms}}</td>
                </tr>
                <tr>
                    <td class="text-right">Guests</td>
                    <td>{{adults}}</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-right">Total</td>
                    <td></td>
                    <td></td>
                    <td>₹{{purchaseAmount}}</td>
                </tr>
                <tr>
                    <td class="text-right" rowspan="2">Commission</td>
                    <td rowspan="2">₹{{commission}}</td>
                    <td colspan="2">Taxable value</td>
                    <td>₹{{taxableValue}}</td>
                </tr>
                <tr>
                    <td colspan="2">GST Rate 18%</td>
                    <td>₹{{tax}}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Gross Amount</td>
                    <td></td>
                    <td>₹{{booking_amount}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: 'sales-invoice',
    props: {
        table: String,
        id: [Number, String]
    },
    computed: {
        customer_name() { return this.$store.state.salesInvoice.customer_name },
        email() { return this.$store.state.salesInvoice.email },
        phone() { return this.$store.state.salesInvoice.phone },
        date() { return this.$store.state.salesInvoice.date },
        hotel_name() { return this.$store.state.salesInvoice.hotel_name },
        particular() { return this.$store.state.salesInvoice.particular },
        checkin() { return this.$store.state.salesInvoice.checkin },
        checkout() { return this.$store.state.salesInvoice.checkout },
        no_of_rooms() { return this.$store.state.salesInvoice.no_of_rooms },
        adults() { return this.$store.state.salesInvoice.adults },
        children() { return this.$store.state.salesInvoice.children },
        extra_bed() { return this.$store.state.salesInvoice.extra_bed },
        booking_amount() { return this.$store.state.salesInvoice.booking_amount },
        booking_id() { return this.$store.state.salesInvoice.booking_id },
        booking_url() { return this.$store.state.salesInvoice.booking_url },
        commission() { return this.$store.state.salesInvoice.commission },
        taxableValue() {
            return Math.round(Number((this.commission*100)/118))
        },
        tax() {
            return Math.round(Number(this.taxableValue*0.18))
        },
        purchaseAmount() {
            return this.booking_amount - this.commission
        }
    },
    mounted() {
        this.$store.dispatch('getSalesInvoice', {table: this.table, id: this.id})
    }
}
</script>