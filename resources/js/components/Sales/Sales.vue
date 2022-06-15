<template>
    <div>
        <v-card>
            <v-card-title>
                <div class="d-flex flex-column">
                    <div class="mb-3">Sales</div>
                    <v-btn x-small @click="show.saleModal = true">+Make sale</v-btn>
                </div>

                <v-spacer></v-spacer>

                <v-text-field
                    v-model="searchSales"
                    append-icon="search"
                    label="Search"
                    single-line
                    hide-details
                />
            </v-card-title>

            <v-data-table
                :headers="salesHeaders"
                :items="sales"
                :items-per-page="10"
                class="elevation-1"
                :loading="loading"
                :search="searchSales"
            >
                <template v-slot:item.metadata="{ item }">
                    <a @click="setSoldItems(item.id)">Items</a>
                </template>

                <template v-slot:item.created_at="{ item }">
                    {{ utcDateTimeToLocal(item.created_at) }}
                </template>

                <template v-slot:item.action="{ item }">
                    <v-icon class="ml-2" @click="handleRefund(item)" :disabled="item.status === 'refunded'">
                        mdi-cash-refund
                    </v-icon>
                </template>
            </v-data-table>
        </v-card>

        <v-dialog v-model="show.itemsModal" :persistent="true" max-width="600">
            <v-card>
                <v-card-title class="grey darken-2">Items</v-card-title>

                <v-container fluid>

                    <Items :items="soldItems"/>

                    <v-row justify="end">
                        <v-card-actions>
                            <v-btn text color="primary" @click="closeItemsModal">Close</v-btn>
                        </v-card-actions>
                    </v-row>

                </v-container>
            </v-card>
        </v-dialog>

        <v-dialog v-model="show.saleModal" :persistent="true" max-width="500">
            <v-card>
                <v-card-title class="grey darken-2">Make Sale</v-card-title>

                <v-container fluid>

                    <span v-if="errors" class="red--text">{{errors.message}}</span>

                    <v-form v-model="valid">

                        <v-row v-for="(item, i) in sale.items" class="mx-2 mt-2" :key="i">

                            <v-row class="mx-2">

                                <v-col cols="2">
                                    <v-text-field
                                        label="Count"
                                        type="number"
                                        min="1"
                                        step="1"
                                        v-model="item.count"
                                        :rules="rules.number"
                                    />
                                </v-col>

                                <v-col cols="8">
                                    <v-select
                                        label="Product"
                                        v-model="item.price_id"
                                        :items="productOptions"
                                        required
                                        :rules="rules.required"
                                    />
                                </v-col>

                                <v-col class="pt-10">
                                    <v-icon small @click="sale.items.splice(i, 1)" title="Remove Item">delete</v-icon>
                                </v-col>

                            </v-row>

                        </v-row>

                        <v-btn x-small @click="addItem" class="ma-2">+Add item</v-btn>

                        <v-row class="ma-5" v-if="sale.items.length">

                            <div class="text-body-1">Total: ${{total}}</div>
                        </v-row>

                        <v-row v-if="sale.items.length" class="mx-5 mt-4">

                            <v-switch
                                v-model="anonSale"
                                :label="`Sell to ${anonSale ? 'non-' : ''}member`"
                                color="secondary"
                                @change="resetBuyer"
                            />
                        </v-row>

                        <v-row v-if="sale.items.length && !anonSale" class="mx-2">

                            <v-col cols="8">
                                <v-select
                                    label="Member"
                                    v-model="sale.user_id"
                                    :items="people"
                                    required
                                    :rules="rules.required"
                                />
                            </v-col>
                        </v-row>

                        <v-row v-if="sale.user_id" class="mx-2">

                            <PaymentMethodsMembers
                                :client="client"
                                :member="sale"
                                @cust-id="setCustID"
                                @payment-method="setHasPaymentMethod"
                            />
                        </v-row>

                        <v-row v-else-if="anonSale && sale.items.length" class="mx-2">

                            <PaymentMethod
                                :client="client"
                                ref="paymentMethod"
                                @payment-method="setHasPaymentMethod"
                            />
                        </v-row>

                        <v-row justify="end">
                            <v-card-actions>
                                <v-btn
                                    text
                                    @click="addSale"
                                    :loading="loading"
                                    :disabled="!valid || !sale.has_payment_method"
                                >
                                    Make Sale
                                </v-btn>
                                <v-btn text color="primary" @click="reset">Cancel</v-btn>
                            </v-card-actions>
                        </v-row>

                    </v-form>
                </v-container>
            </v-card>
        </v-dialog>

        <v-dialog v-model="show.refundModal" :persistent="true" max-width="600">
            <v-card>
                <v-card-title class="grey darken-2">Refund</v-card-title>

                <v-container fluid>

                    <v-form v-model="valid" v-if="refund">

                        <span v-if="errors" class="red--text">{{errors}}</span>

                        <Items :items="refund.metadata.items" />

                        <hr class="my-12" />

                        <v-row class="mx-2">
                            <v-col cols="8">
                                <v-text-field label="Amount" v-model="refund.amount" required :rules="rules.amount"/>
                            </v-col>
                        </v-row>

                        <v-row class="mx-2">

                            <v-col cols="8">
                                <v-text-field label="Payment Method" v-model="refund.payment_method" disabled />
                            </v-col>
                        </v-row>

                        <v-row v-if="refund.user" class="mx-2">

                            <v-col cols="10">
                                <v-text-field label="Member" v-model="refund.user.name" disabled />
                            </v-col>
                        </v-row>

                        <v-row justify="end">
                            <v-card-actions>
                                <v-btn text @click="doRefund" :loading="loading" :disabled="!valid">Issue Refund</v-btn>
                                <v-btn text color="primary" @click="reset">Cancel</v-btn>
                            </v-card-actions>
                        </v-row>

                    </v-form>
                </v-container>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import {headers} from "../../authorization";
import {utcDateTimeToLocal} from "../../datetime_converters";
import PaymentMethod from "components/PaymentMethod"
import PaymentMethodsMembers from "components/PaymentMethodsMembers"
import Items from "components/Sales/Items";

function Item() {
    this.count = 1;
    this.price_id = null;
}

function Sale() {
    this.id = null;
    this.user_id = null;
    this.cust_id = null;
    this.has_payment_method = false;
    this.items = [];
    this.paymentIntent = null;
}

export default {
    name: 'Sales',
    components: {Items, PaymentMethod, PaymentMethodsMembers},
    props: {
        products: {type: Array, required: true},
    },
    data: () => ({
        anonSale: false,
        errors: null,
        loading: false,
        soldItems: [], // items in a completed sale, used for itemsModal
        refund: null,
        rules: {
            required: [v => !!v || 'Field is required'],
            number: [v => /\d+/.test(v) || 'Number is required'],
            amount: [v => /^\d+(\.\d{1,2})?$/.test(v) || 'Price must be valid'],
        },
        salesHeaders: [
            { text: 'Date', align: 'left', value: 'created_at' },
            { text: 'Name', align: 'left', value: 'user.name' },
            { text: 'Payment Method', align: 'left', value: 'payment_method' },
            { text: 'Items', align: 'left', value: 'metadata' },
            { text: 'Total', align: 'left', value: 'total' },
            { text: 'Status', value: 'status' },
            { text: 'Actions', value: 'action', sortable: false },
        ],
        sales: [],
        sale: new Sale(),
        searchSales: '',
        show: {
            itemsModal: false,
            refundModal: false,
            saleModal: false,
        },
        valid: false,
    }),
    computed: {
        client() {
            return this.$store.state.clients.find(client => client.id === this.user.client_id);
        },
        productOptions() {

            const productOptions = [];

            this.products.forEach( product => {

                product.prices.forEach( price => {

                    productOptions.push({text: `${product.name} - ${price.amount}`, value: price.id});
                });
            });

            return productOptions;
        },
        people() {
            return this.$store.state.people.map( person => ({text: person.name, value: person.id}));
        },
        total() {
            let total = 0;

            this.sale.items.forEach(item => {

                this.products.forEach(product => {

                    product.prices.forEach(price => {

                        if (price.id === item.price_id) {
                            total += item.count * Number(price.amount.slice(1));
                        }
                    });
                });
            });

            return total.toFixed(2);
        },
        user() {
            return this.$store.state.user;
        },
    },
    methods: {
        addItem() {
            this.sale.items.push(new Item());
        },
        async addSale() {

            this.loading = true;
            this.errors = null;

            if ( ! this.sale.has_payment_method) {

                this.errors = 'Payment method required.';
                return;
            }

            const res = await this.createPaymentIntent();

            if (res.error) {

                this.errors = res.error;
                return;
            } else {
                this.sale.paymentIntent = res.paymentIntent;
            }

            const
                options = this.anonSale ? {payment_method: {card: this.$refs.paymentMethod.card}} : {},
                resp = await this.$store.state.stripe.confirmCardPayment(this.sale.paymentIntent.client_secret, options);

            if (resp.error) {

                this.errors = resp.error;
            } else {
                // persist resp.paymentIntent
                fetch(`/sales/${this.client.id}`, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({
                        paymentIntent: {metadata: this.sale.paymentIntent.metadata, ...resp.paymentIntent},
                        sale: this.sale,
                    })
                })
                    .then( resp => {
                        if (resp.ok) {
                            return resp.json();
                        }
                    })
                    .then( json => {
                        if (json.error) {
                            this.errors = json.error;
                        } else {
                            this.reset();
                            this.refresh();
                        }
                    })
                    .finally(() => this.loading = false);
            }
        },
        cancelPaymentIntent() {

            if (!this.sale.paymentIntent.id) {
                return;
            }

            this.loading = true;
            this.errors = null;
            fetch(`/payment_intent/${this.client.id}}`, {
                method: 'DELETE',
                headers,
                body: JSON.stringify({paymentIntentId: this.sale.paymentIntent.id}),
            })
                .then( resp => {
                    if (resp.ok) {
                        return resp.json();
                    }
                })
                .then( json => {
                    if (json.error) {
                        this.errors = json.error;
                    } else {
                        this.sale.paymentIntent = null;
                    }
                })
                .finally(() => this.loading = false);
        },
        /**
         * Creates a Stripe payment intent
         *
         * @returns {Promise<any>} - {id: paymentIntentId, clientSecret: paymentIntentClientSecret}
         */
        async createPaymentIntent() {

            const resp = await fetch(
                `/payment_intent/${this.client.id}/${this.sale.cust_id ? this.sale.cust_id : ''}`, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({items: this.sale.items})
                }
            );

            return await resp.json();
        },
        closeItemsModal() {

            this.soldItems = [];
            this.show.itemsModal = false;
        },
        doRefund() {
            this.loading = true;
            confirm(
                'A refund request will be submitted to your customer’s card issuer.\n' +
                'Your customer will see the refund as a credit in approximately 5-10 business days.'
            ) &&
            fetch(`/sales/${this.client.id}/${this.refund.id}`, {
                method: 'DELETE',
                headers,
                body: JSON.stringify({amount: this.refund.amount})
            })
                .then(resp => resp.json())
                .then(json => {
                    this.loading = false;
                    if (json.errors) {
                        this.errors = JSON.stringify(json.errors);
                    } else if (json.error) {
                        this.errors = JSON.stringify(json.error);
                    } else {
                        this.reset();
                        this.refresh();
                    }
                });
        },
        handleRefund(sale) {
            this.refund = JSON.parse(JSON.stringify(sale));

            // remove the leading dollar sign from price
            const match = this.refund.total.match(/\$?([0-9]*.[0-9]{2})/);
            this.refund.amount = match[1];

            this.show.refundModal = true;
        },
        refresh() {
            this.loading = true;
            fetch(`/sales/${this.client.id}`)
                .then(resp => resp.json())
                .then(sales => {
                    this.sales = sales.map(sale => {

                        sale.metadata = JSON.parse(sale.metadata);
                        return sale;
                    });
                })
                .finally(() => this.loading = false)
        },
        reset() {
            this.errors = null;
            this.loading = false;
            this.sale = new Sale();
            this.show = {
                refundModal: false,
                saleModal: false,
            };
            this.refund = null;
        },
        resetBuyer() {
            if (this.anonSale) {
                this.sale.user_id = null;
                this.sale.cust_id = null;
                this.sale.has_payment_method = false;
            }
        },
        /**
         * @param custId {String} - Stripe customer id
         */
        setCustID(custId) {
            this.sale.cust_id = custId;
        },
        /**
         * @param hasPaymentMethod {Boolean}
         */
        setHasPaymentMethod(hasPaymentMethod) {
            this.sale.has_payment_method = hasPaymentMethod;
        },
        /**
         * Sets items of a sale for display in itemsModal
         * @param saleId {String}
         */
        setSoldItems(saleId) {
            this.soldItems = this.sales.find(sale => sale.id === saleId).metadata.items;
            this.show.itemsModal = true;
        },
        utcDateTimeToLocal
    },
    mounted() {
        this.refresh();
    },
}
</script>
