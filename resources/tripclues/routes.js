import SalesLedger from './components/Ledger/SalesLedger'
import PurchasesLedger from './components/Ledger/PurchasesLedger'
import CustomerLedger from './components/Customer/CustomerLedger'
import CustomerPurchase from './components/Customer/CustomerPurchase'
import SalesInvoice from './components/Invoice/SalesInvoice'
import PurchaseInvoice from './components/Invoice/PurchaseInvoice'
import Home from './components/Home'

export const routes = [
    {
        path: '/',
        component: Home
    },
    {
        path: '/ledger/sales',
        component: SalesLedger
    },
    {
        path: '/ledger/purchases',
        component: PurchasesLedger
    },
    {
        name: 'CustomerLedger',
        path: '/customer/ledger/:table/:id',
        component: CustomerLedger,
        props: true
    },
    {
        name: 'CustomerPurchase',
        path: '/customer/purchase/:table/:id',
        component: CustomerPurchase,
        props: true
    },
    {
        name: 'SalesInvoice',
        path: '/invoice/sale/:table/:id',
        component: SalesInvoice,
        props: true
    },
    {
        name: 'PurchaseInvoice',
        path: '/invoice/purchase/:table/:id',
        component: PurchaseInvoice,
        props: true
    }
]