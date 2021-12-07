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
        <v-lazy v-model="show.cards">
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
        </v-lazy>
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
 * @emits created-payment-method
 */
export default {
    name: "PaymentMethods",
    data: function () {
        return {
            card: null,
            cards: [],
            default_payment_method: null,
            errors: {card: null},
            loading: false,
            show: {
                cardInputs: false,
                cards: false,
            },
        }
    },
    watch: {
        'show.cards': function(show) {
            show && this.getPaymentMethods();
        },
    },
    methods: {
        changeCard({error, empty, complete}) {
            this.errors.card = error ? error.message : '';
            if ( ! empty && complete) {
                this.card.update({ disabled: true });
                this.findOrCreateStripeCustomer();
            }
        },
        changeDefault(paymentMethodId) {
            if (confirm('Are you sure you want to set this card as default payment method?')) {
                this.loading = true;
                fetches.cancelFetches();
                this.show.cardInputs = false;
                fetch('/payment_method', {
                    method: 'POST',
                    headers,
                    credentials: 'same-origin',
                    signal: fetches.getSignal(),
                    body: JSON.stringify({ paymentMethodId }),
                })
                    .then( resp => resp.json())
                    .then( json => {
                        this.cards = json.data;
                        this.default_payment_method = json.default_payment_method;
                        this.loading = false;
                    });
            }
        },
        clickDelCard(paymentMethodId) {
            if (confirm('Are you sure you want to delete this card?')) {
                this.loading = true;
                fetch(`/payment_method/${paymentMethodId}`, {
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
         */
        createPaymentMethod(custId) {
            return stripe.createPaymentMethod({
                type: 'card',
                card: this.card,
            })
                .then( result => {
                    if (result.error) {
                        this.errors.card = result.error;
                    } else {
                        fetches.cancelFetches();
                        this.show.cardInputs = false;
                        fetch('/payment_method', {
                            method: 'POST',
                            headers,
                            credentials: 'same-origin',
                            signal: fetches.getSignal(),
                            body: JSON.stringify({
                                custId,
                                paymentMethodId: result.paymentMethod.id,
                            }),
                        })
                            .then( resp => resp.json())
                            .then( json => {
                                this.cards = json.data;
                                this.default_payment_method = json.default_payment_method;
                                this.upsertSubscription({custId});
                            });
                    }
                });
        },
        findOrCreateStripeCustomer() {
            this.loading = true;
            if ( ! this.errors.card) {
                fetches.cancelFetches();
                fetch(`/customer`, {
                    headers,
                    credentials: "same-origin",
                    signal: fetches.getSignal(),
                })
                    .then(resp => resp.json())
                    .then(json => {
                        if (json.id && json.object === 'customer') {
                            this.createPaymentMethod(json.id);
                        } else {
                            this.errors.card = "There was a problem creating the customer record."
                        }
                    });
            }
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
            fetch(`/payment_methods`, {headers, credentials: 'same-origin'})
                .then(resp => resp.json())
                .then(json => {
                    this.cards = json.data;
                    this.default_payment_method = json.default_payment_method;
                    this.loading = false;
                });
        },
        /**
         * Hits backend to create Stripe subscription
         *
         * @param custId {String} - Stripe customer id
         */
        upsertSubscription({custId}) {
            fetches.cancelFetches();
            return (
                fetch('/subscription', {
                    method: 'POST',
                    headers,
                    credentials: 'same-origin',
                    signal: fetches.getSignal(),
                    body: JSON.stringify({custId}),
                })
                    .then( resp => resp.json())
                    .then( subscription => {
                        if (subscription.error) {
                            this.errors.card = subscription.error;
                        }
                        if (subscription.status === 'active') {
                            this.$emit('created-payment-method');
                        }
                        this.loading = false;
                    })
            );
        },
        async showCardInputs() {
            this.show.cardInputs = true;
            stripe = await loadStripe(STRIPE_PUB_KEY);
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
}
</script>

<style scoped>

#card-errors {
    color: #ff5252;
    margin-left: 31px;
}

</style>
