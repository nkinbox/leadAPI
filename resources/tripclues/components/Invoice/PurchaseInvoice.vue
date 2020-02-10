<template>
    <div>
        <h4>Purchase Invoice</h4>
        <table class="table table-borderless table-sm">
            <tr>
                <th class="cell-shrink">SellerName</th>
                <td>{{seller_name}}</td>
                <th class="cell-shrink">BillNo</th>
                <td>HTPR{{id}}</td>
            </tr>
            <tr>
                <th>Date</th>
                <td>{{date | dateFormat}}</td>
                <th class="text-nowrap">Booking Ref</th>
                <td><a :href="booking_url">{{booking_id}}</a></td>
            </tr>
            <tr>
                <th>Address</th>
                <td colspan="3">{{address}}</td>
            </tr>
            <tr>
                <th>City</th>
                <td colspan="3">{{city}}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{email}}</td>
                <th>Phone</th>
                <td>{{phone}}</td>
            </tr>
            <tr>
                <td colspan="4">{{particular}}</td>
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
                    <td>{{checkin}}</td>
                    <td rowspan="4"></td>
                    <td rowspan="4"></td>
                    <td rowspan="4" class="align-middle">₹{{booking_amount}}</td>
                </tr>
                <tr>
                    <td class="text-right">CheckOut Date</td>
                    <td>{{checkout}}</td>
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
        seller_name() { return this.$store.state.purchaseInvoice.seller_name },
        city() { return this.$store.state.purchaseInvoice.city },
        email() { return this.$store.state.purchaseInvoice.email },
        address() { return this.$store.state.purchaseInvoice.address },
        phone() { return this.$store.state.purchaseInvoice.phone },
        date() { return this.$store.state.purchaseInvoice.date },
        particular() { return this.$store.state.purchaseInvoice.particular },
        checkin() { return this.$store.state.purchaseInvoice.checkin },
        checkout() { return this.$store.state.purchaseInvoice.checkout },
        no_of_rooms() { return this.$store.state.purchaseInvoice.no_of_rooms },
        adults() { return this.$store.state.purchaseInvoice.adults },
        children() { return this.$store.state.purchaseInvoice.children },
        extra_bed() { return this.$store.state.purchaseInvoice.extra_bed },
        booking_amount() { return this.$store.state.purchaseInvoice.booking_amount },
        booking_id() { return this.$store.state.purchaseInvoice.booking_id },
        booking_url() { return this.$store.state.purchaseInvoice.booking_url }
    },
    mounted() {
        this.$store.dispatch('getPurchaseInvoice', {table: this.table, id: this.id})
    }
}
</script>