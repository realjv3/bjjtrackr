<template>
    <v-container fluid>
        <v-row align="center">
            <v-col style="min-width: 255px">
                <template v-if="!show.cardInputs">
                    <v-col>
                        <v-btn
                            v-model="show.cardInputs"
                            :disabled="loading"
                            :loading="loading"
                            color="pink"
                            dark
                            fab
                            small
                            @click="showCardInputs"
                        >
                            <v-icon>mdi-credit-card-plus</v-icon>
                        </v-btn>
                        <span class="text-body-1 pl-2">Add card</span>
                    </v-col>
                </template>
                <template v-else-if="show.cardInputs">
                    <v-col>
                        <div id="card"></div>
                        <div id="card-errors" role="alert">{{errors.card}}</div>
                    </v-col>
                </template>
            </v-col>
        </v-row>
        <v-list dense min-width="300">
            <v-list-item class="px-0" v-for="(card, i) in cards" :key="i">
                <v-skeleton-loader type="list-item-avatar" v-if="loading" style="right: 17px; width: 300px"/>
                <v-list-item-icon v-if="!loading">
                    <v-img :src="getCardImg(card.card.brand)" width="32" height="32" contain />
                </v-list-item-icon>
                <v-list-item-content v-if="!loading">
                    <v-list-item-title>
                        <span v-html="getCardText(card)"></span>
                        <span
                            v-if="card.id === default_payment_method"
                            style="float: right; background-color: darkgray; padding: 2px; border-radius: 2px"
                        >Default</span>
                        <a v-else @click="changeDefault(card.id)" class="text-caption" style="float: right">
                            Set default
                        </a>
                    </v-list-item-title>
                </v-list-item-content>
                <v-list-item-action v-if="!loading">
                    <v-icon @click="clickDelCard(card.id)">mdi-trash-can-outline</v-icon>
                </v-list-item-action>
            </v-list-item>
        </v-list>
    </v-container>
</template>

<script>
import {headers} from "../authorization";
import {loadStripe} from "@stripe/stripe-js";
import Fetches from "../fetches";

const fetches = new Fetches();
let stripe;

/**
 * Logic for payment method CRUD
 *
 * @emits cust-id
 */
export default {
    name: "PaymentMethodsMembers",
    data: function () {
        return {
            card: null,
            cards: [],
            default_payment_method: null,
            errors: {card: null},
            loading: false,
            show: {
                cardInputs: false,
            },
            stripeAccountId: null,
        }
    },
    props: {
        client: {type: Object, required: true},
        member: {type: Object, required: true},
    },
    watch: {
        'member.user_id': function() {
            this.findOrCreateStripeCustomer();
            this.getPaymentMethods();
        },
    },
    methods: {
        changeCard({error, empty, complete}) {
            this.errors.card = error ? error.message : '';
            if ( ! empty && complete) {
                this.card.update({ disabled: true });
                if ( ! this.errors.card) {
                    this.createPaymentMethod(this.member.cust_id, this.stripeAccountId);
                }
            }
        },
        changeDefault(paymentMethodId) {
            this.loading = true;
            this.show.cardInputs = false;
            fetches.cancelFetches();
            fetch(`member/payment_method/${this.client.id}/${this.member.user_id}`, {
                method: 'POST',
                headers,
                signal: fetches.getSignal(),
                body: JSON.stringify({ custId: this.member.cust_id, paymentMethodId }),
            })
                .then( resp => resp.json())
                .then( json => {
                    this.cards = json.data;
                    this.default_payment_method = json.default_payment_method;
                    this.loading = false;
                });
        },
        clickDelCard(paymentMethodId) {
            if (confirm('Are you sure you want to delete this card?')) {
                this.loading = true;
                fetch(`member/payment_method/${paymentMethodId}`, {
                    method: 'DELETE',
                    headers,
                    credentials: "same-origin",
                })
                    .then( resp => resp.json())
                    .then( json => {
                        this.cards = json.data;
                        this.default_payment_method = json.default_payment_method;
                        this.loading = false;
                    });
            }
        },
        /**
         * Creates a stripe payment method
         *
         * @param custId {String} - Stripe customer id
         * @param stripeAccountId {String}
         */
        createPaymentMethod(custId, stripeAccountId) {
            this.loading = true;
            return stripe.createPaymentMethod({type: 'card', card: this.card})
                .then( result => {
                    if (result.error) {
                        this.errors.card = result.error;
                    } else {
                        this.changeDefault(result.paymentMethod.id);
                    }
                    this.loading = false;
                });
        },
        findOrCreateStripeCustomer() {
            this.loading = true;
            fetches.cancelFetches();
            fetch(`member/customer/${this.client.id}/${this.member.user_id}`, {
                method: 'POST',
                headers,
                signal: fetches.getSignal(),
            })
                .then(resp => resp.json())
                .then(json => {
                    if (json.customer.id && json.customer.object === 'customer') {
                        this.stripeAccountId = json.stripe_account_id;
                    } else {
                        this.errors.card = "There was a problem creating the customer record."
                    }
                    this.loading = false;
                    this.$emit('cust-id', json.customer.id);

                });
        },
        getCardImg(brand) {
            let url = '';
            switch (brand) {
                case 'amex':
                    url = 'https://flowrolled.nyc3.digitaloceanspaces.com/public/Amex.png';
                    break;
                case 'diners':
                    url = 'https://flowrolled.nyc3.digitaloceanspaces.com/public/DinersClub.jpg';
                    break;
                case 'discover':
                    url = 'https://flowrolled.nyc3.digitaloceanspaces.com/public/Discover.jpg';
                    break;
                case 'mastercard':
                    url = 'https://flowrolled.nyc3.digitaloceanspaces.com/public/mastercard.png';
                    break;
                case 'visa':
                    url = 'https://flowrolled.nyc3.digitaloceanspaces.com/public/visa.png';
                    break;
                default:
                    url = 'https://flowrolled.nyc3.digitaloceanspaces.com/public/CreditCard.png';
            }
            return url;
        },
        getCardText(card) {
            return card.card.brand.charAt(0).toUpperCase() + card.card.brand.slice(1) + `&nbsp;`
                + `<small>XXXX-${card.card.last4} Exp. ${card.card.exp_month}/${card.card.exp_year}</small>`;
        },
        getPaymentMethods() {
            this.loading = true;
            fetch(`member/payment_methods/${this.client.id}/${this.member.user_id}`, {headers})
                .then(resp => resp.json())
                .then(json => {
                    this.cards = json.data;
                    this.default_payment_method = json.default_payment_method;
                    this.loading = false;
                });
        },
        async showCardInputs() {
            this.show.cardInputs = true;
            stripe = await loadStripe(STRIPE_PUB_KEY, {stripeAccount: this.stripeAccountId});
            const
                elements = stripe.elements(),
                style = {
                    base: {
                        color: "#FFF",
                        fontFamily: 'Roboto, sans-serif',
                        fontSmoothing: "antialiased",
                        fontSize: "16px",
                        "::placeholder": {
                            color: "#aab7c4"
                        }
                    },
                    invalid: {
                        color: "#ff5252",
                        iconColor: "#ff5252"
                    },
                };
            this.card = elements.create('card', {style});
            this.card.mount('#card');
            this.card.on('change', this.changeCard);
        },
    },
    created() {
        this.findOrCreateStripeCustomer();
        this.getPaymentMethods();
    },
}
</script>

<style scoped>

#card-errors {
    color: #ff5252;
    margin-left: 31px;
}

</style>
