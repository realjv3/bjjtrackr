<template>
    <v-container fluid>
        <v-row justify="center">
            <v-card style="min-width: 33vw">
                <v-card-title class="grey darken-2">Settings</v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row class="mx-2">
                            <v-col cols="12">
                                <h3>Promotion Eligibility</h3>
                            </v-col>
                        </v-row>
                        <v-row class="mx-2" style="border-bottom: 1px solid white">
                            <v-col cols="12">
                                <v-select v-model="belt" :items="belts" label="Belt" style="width: 100px"></v-select>
                                <v-switch
                                    v-model="settings[belt].combine_same_day_checkins"
                                    :label="sessionLabel"
                                    @change="update"
                                ></v-switch>
                                <v-text-field
                                    v-model="settings[belt].sessions_til_stripe"
                                    type="number"
                                    min="1"
                                    max="255"
                                    label="Sessions until next stripe"
                                    style="width: 150px"
                                    :error-messages="errors.sessions_til_stripe[0]"
                                    @change="update"
                                />
                                <v-text-field
                                    v-model="settings[belt].times_absent_til_contact"
                                    type="number"
                                    min="1"
                                    max="255"
                                    label="Times absent until contact"
                                    style="width: 150px"
                                    :error-messages="errors.times_absent_til_contact[0]"
                                    @change="update"
                                />
                            </v-col>
                        </v-row>
                        <v-row class="mx-2 my-5">
                            <v-col cols="12">
                                <h3>Payment Info</h3>
                            </v-col>
                        </v-row>
                        <v-row class="mx-2">
                            <v-col cols="11">
                                <div id="card"></div>
                            </v-col>
                            <v-col cols="1">
                                <v-tooltip bottom>
                                    <template v-slot:activator="{ on }">
                                        <v-btn
                                            color="indigo lighten-2"
                                            elevation="2"
                                            icon
                                            small
                                            :disabled="saving"
                                            :loading="saving"
                                            v-on="on"
                                            style="bottom: 6px"
                                            @click="clickSaveCard"
                                        >
                                            <v-icon dark>mdi-content-save</v-icon>
                                        </v-btn>
                                    </template>
                                    <span>Save Card</span>
                                </v-tooltip>
                            </v-col>
                        </v-row>
                        <v-row class="mx-4>">
                            <span id="card-errors" role="alert">{{errors.card}}</span>
                        </v-row>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-row>

    </v-container>
</template>

<script>

import {headers} from "../authorization";
import Fetches from "../fetches";
import {loadStripe} from '@stripe/stripe-js';

const fetches = new Fetches();
let stripe;

export default {
    name: "Settings",
    data: () => ({
        belt: 1,
        belts: [
            {value: 1, text: 'White'},
            {value: 2, text: 'Blue'},
            {value: 3, text: 'Purple'},
            {value: 4, text: 'Brown'},
        ],
        card: null,
        errors: {
            card: '',
            sessions_til_stripe: [],
            times_absent_til_contact: [],
        },
        saving: false,
    }),
    computed: {
        sessionLabel() {
            return this.settings[this.belt].combine_same_day_checkins ?
                `1 day's training equals 1 session` : `1 class equals 1 session`;
        },
        settings() {
            return this.$store.state.settings;
        },
    },
    methods: {
        changeCard(event) {
            this.errors.card = event.error ? event.error.message : '';
        },
        clickSaveCard() {
            this.saving = true;
            if ( ! this.errors.card) {
                fetches.cancelFetches();
                fetch(`/settings/${this.settings[this.belt].id}`, {
                    method: 'POST',
                    headers,
                    credentials: "same-origin",
                    signal: fetches.getSignal(),
                    body: JSON.stringify({
                        sessions_til_stripe: this.settings[this.belt].sessions_til_stripe,
                        times_absent_til_contact: this.settings[this.belt].times_absent_til_contact,
                        combine_same_day_checkins: this.settings[this.belt].combine_same_day_checkins,
                    }),
                })
                    .then( resp => resp.json())
            }
        },
        update() {
            this.errors = {
                sessions_til_stripe: [],
                times_absent_til_contact: [],
            };
            fetches.cancelFetches();
            fetch(`/settings/${this.settings[this.belt].id}`, {
                method: 'POST',
                headers,
                credentials: "same-origin",
                signal: fetches.getSignal(),
                body: JSON.stringify({
                    sessions_til_stripe: this.settings[this.belt].sessions_til_stripe,
                    times_absent_til_contact: this.settings[this.belt].times_absent_til_contact,
                    combine_same_day_checkins: this.settings[this.belt].combine_same_day_checkins,
                }),
            })
                .then( resp => resp.json())
                .then( json => {
                    if (json.errors) {
                        if (json.errors.sessions_til_stripe) {
                            this.errors.sessions_til_stripe = json.errors.sessions_til_stripe;
                        }
                        if (json.errors.times_absent_til_contact) {
                            this.errors.times_absent_til_contact = json.errors.times_absent_til_contact;
                        }
                    } else {
                        this.$store.commit('setSettings', json);
                    }
                });
        },
        async refresh() {
            await this.$store.dispatch('getSettings');
        },
    },
    async mounted() {
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
}
</script>

<style scoped>

#card-errors {
    color: #ff5252;
    margin-left: 31px;
}
</style>
