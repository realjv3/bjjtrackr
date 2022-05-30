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

        <v-dialog v-model="show.saleModal" :persistent="true" max-width="400">
            <v-card>
                <v-card-title class="grey darken-2">Make Sale</v-card-title>

                <v-container fluid>

                    <span v-if="errors" class="red--text">{{errors.message}}</span>

                    <v-form v-model="valid">

                        <v-row class="mx-2">
                            <v-col cols="8">
                                <v-select
                                    label="Product"
                                    v-model="sale.product_id"
                                    :items="productOptions"
                                    required
                                    :rules="rules.required"
                                    @change="resetBuyer"
                                />
                            </v-col>
                        </v-row>

                        <div v-if="sale.product_id">
                            <v-row class="mx-2">
                                <v-col cols="8">
                                    <v-select
                                        label="Price"
                                        v-model="sale.price_id"
                                        :items="priceOptions"
                                        required
                                        :rules="rules.required"
                                    />
                                </v-col>
                            </v-row>
                        </div>

                        <v-row v-if="sale.price_id" class="mx-2">
                            <v-switch
                                v-model="anonSale"
                                :label="`Sell to ${anonSale ? 'non-' : ''}member`"
                                color="secondary"
                                @change="resetBuyer"
                            />

                            <v-col cols="8" v-if="!anonSale">
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

                        <v-row v-else-if="anonSale && sale.price_id" class="mx-2">

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

        <v-dialog v-model="show.refundModal" :persistent="true" max-width="400">
            <v-card>
                <v-card-title class="grey darken-2">Refund</v-card-title>

                <v-container fluid>

                    <v-form v-model="valid" v-if="refund">

                        <span v-if="errors" class="red--text">{{errors}}</span>

                        <v-row class="mx-2">
                            <v-col>
                                <v-text-field label="Product" v-model="refund.product.name" disabled />
                            </v-col>
                        </v-row>

                        <v-row class="mx-2">
                            <v-col cols="8">
                                <v-text-field label="Amount" v-model="refund.price.amount" required :rules="rules.price"/>
                            </v-col>
                        </v-row>

                        <v-row class="mx-2">

                            <v-col cols="8">
                                <v-text-field label="Member" v-model="refund.payment_method" disabled />
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
import {headers} from "../authorization";
import {utcDateTimeToLocal} from "../datetime_converters";
import PaymentMethod from "components/PaymentMethod"
import PaymentMethodsMembers from "components/PaymentMethodsMembers"

function Sale() {
    this.id = null;
    this.user_id = null;
    this.cust_id = null;
    this.has_payment_method = false;
    this.product_id = null;
    this.price_id = null;
    this.paymentIntent = {
        id: null,
        clientSecret: null,
    };
}

export default {
    name: 'Sales',
    components: {PaymentMethod, PaymentMethodsMembers},
    props: {
        products: {type: Array, required: true},
    },
    data: () => ({
        anonSale: false,
        errors: null,
        loading: false,
        refund: null,
        rules: {
            required: [v => !!v || 'Field is required'],
            number: [v => /\d+/.test(v) || 'Number is required'],
            price: [v => /^\d+(\.\d{1,2})?$/.test(v) || 'Price must be valid'],
        },
        salesHeaders: [
            { text: 'Date', align: 'left', value: 'created_at' },
            { text: 'Name', align: 'left', value: 'user.name' },
            { text: 'Payment Method', align: 'left', value: 'payment_method' },
            { text: 'Product', align: 'left', value: 'product.name' },
            { text: 'Price', value: 'price.amount'},
            { text: 'Status', value: 'status' },
            { text: 'Actions', value: 'action', sortable: false },
        ],
        sales: [],
        sale: new Sale(),
        searchSales: '',
        show: {
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
            return this.products
                .filter(m => m.active)
                .map(m => ({text: m.name, value: m.id}));
        },
        priceOptions() {
            const product = this.products.find(m => m.id === this.sale.product_id);
            return product.prices
                .filter(p => p.active)
                .map(p => ({text: `${p.amount}`, value: p.id}));
        },
        people() {
            return this.$store.state.people.map( person => ({text: person.name, value: person.id}));
        },
        user() {
            return this.$store.state.user;
        },
    },
    methods: {
        async addSale() {

            this.loading = true;
            this.errors = null;

            if ( ! this.sale.has_payment_method) {

                this.errors = 'Payment method required.';
                return;
            }

            const paymentIntent = await this.createPaymentIntent();

            if (paymentIntent.error) {

                this.errors = paymentIntent.error;
                return;
            } else {
                this.sale.paymentIntent = paymentIntent;
            }

            const
                options = this.anonSale ? {payment_method: {card: this.$refs.paymentMethod.card}} : {},
                resp = await this.$store.state.stripe.confirmCardPayment(this.sale.paymentIntent.clientSecret, options);

            if (resp.error) {

                this.errors = resp.error;
            } else {
                // persist resp.paymentIntent
                fetch(`/sales/${this.client.id}`, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({
                        paymentIntent: resp.paymentIntent,
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
                        this.sale.paymentIntent = {
                            id: null,
                            clientSecret: null,
                        };
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
                `/payment_intent/${this.client.id}/${this.sale.price_id}/${this.sale.cust_id ? this.sale.cust_id : ''}`,
                {headers}
            );

            return await resp.json();
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
                body: JSON.stringify({amount: this.refund.price.amount})
            })
                .then(resp => resp.json())
                .then(json => {
                    if (json.errors) {
                        this.errors = JSON.stringify(json.errors);
                    } else if (json.error) {
                        this.errors = JSON.stringify(json.error);
                    }
                })
                .finally(() => {
                    this.reset();
                    this.refresh();
                });
        },
        handleRefund(sale) {
            this.refund = JSON.parse(JSON.stringify(sale));

            // remove the leading dollar sign from price
            const match = this.refund.price.amount.match(/\$?([0-9]*.[0-9]{2})/);
            this.refund.price.amount = match[1];

            this.show.refundModal = true;
        },
        refresh() {
            this.loading = true;
            fetch(`/sales/${this.client.id}`)
                .then(resp => resp.json())
                .then(sales => this.sales = sales)
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
        utcDateTimeToLocal
    },
    mounted() {
        this.refresh();
    },
}
</script>
