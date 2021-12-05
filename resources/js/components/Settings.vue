<template>
    <v-container fluid>
        <v-row justify="center">
            <v-card>
                <v-card-title class="grey darken-2">Settings</v-card-title>
                <v-card-text>
                    <v-container fluid class="mx-lg-2">

                        <v-row>
                            <v-col>
                                <h3>Promotion Eligibility</h3>
                            </v-col>
                        </v-row>
                        <v-row style="border-bottom: 1px solid white">
                            <v-col>
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
                                    label="Sessions until promotion"
                                    style="width: 150px"
                                    :error-messages="errors.sessions_til_stripe[0]"
                                    @change="update"
                                />
                                <v-text-field
                                    v-model="settings[belt].weeks_absent_til_contact"
                                    type="number"
                                    min="1"
                                    max="255"
                                    label="Weeks absent until contact"
                                    style="width: 150px"
                                    :error-messages="errors.weeks_absent_til_contact[0]"
                                    @change="update"
                                />
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col>
                                <h3>Academy profile</h3>
                            </v-col>
                        </v-row>
                        <v-row justify="center" style="border-bottom: 1px solid white">
                            <v-col cols="4">
                                <v-btn @click="$emit('edit-client', {...client, settings: true})">
                                    Edit profile
                                </v-btn>
                            </v-col>
                        </v-row>

                        <template v-if="isAdmin(user)">
                            <v-row class="my-5">
                                <v-col>
                                    <h3>Payment Info</h3>
                                </v-col>
                            </v-row>
                            <v-row justify="center">
                                <v-col cols="9">
                                    <PaymentMethods />
                                </v-col>
                            </v-row>
                        </template>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-row>

    </v-container>
</template>

<script>

import {headers} from "../authorization";
import {isAdmin} from "../authorization";
import Fetches from "../fetches";
import PaymentMethods from "components/PaymentMethods";

const fetches = new Fetches();

export default {
    name: "Settings",
    components: {PaymentMethods},
    data: () => ({
        belt: 1,
        belts: [
            {value: 1, text: 'White'},
            {value: 2, text: 'Blue'},
            {value: 3, text: 'Purple'},
            {value: 4, text: 'Brown'},
        ],
        errors: {
            sessions_til_stripe: [],
            weeks_absent_til_contact: [],
        },
        saving: false,
    }),
    computed: {
        client() {
            return this.$store.state.clients.find(client => client.id === this.$store.state.user.client_id);
        },
        sessionLabel() {
            return this.settings[this.belt].combine_same_day_checkins ?
                `1 day's training equals 1 session` : `1 class equals 1 session`;
        },
        settings() {
            return this.$store.state.settings;
        },
        user() {
            return this.$store.state.user;
        },
    },
    methods: {
        isAdmin,
        update() {
            this.errors = {
                sessions_til_stripe: [],
                weeks_absent_til_contact: [],
            };
            fetches.cancelFetches();
            fetch(`/settings/${this.settings[this.belt].id}`, {
                method: 'POST',
                headers,
                credentials: "same-origin",
                signal: fetches.getSignal(),
                body: JSON.stringify({
                    sessions_til_stripe: this.settings[this.belt].sessions_til_stripe,
                    weeks_absent_til_contact: this.settings[this.belt].weeks_absent_til_contact,
                    combine_same_day_checkins: this.settings[this.belt].combine_same_day_checkins,
                }),
            })
                .then( resp => resp.json())
                .then( json => {
                    if (json.errors) {
                        if (json.errors.sessions_til_stripe) {
                            this.errors.sessions_til_stripe = json.errors.sessions_til_stripe;
                        }
                        if (json.errors.weeks_absent_til_contact) {
                            this.errors.weeks_absent_til_contact = json.errors.weeks_absent_til_contact;
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
}
</script>
