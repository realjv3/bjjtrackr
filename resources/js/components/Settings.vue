<template>
    <v-container fluid>
        <v-row justify="center">
            <v-card>
                <v-card-title>
                    Settings
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row class="mx-2">
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
                    </v-container>
                </v-card-text>
            </v-card>
        </v-row>

    </v-container>
</template>

<script>

import {headers} from "../authorization";
import Fetches from "../fetches";

const fetches = new Fetches();

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
        errors: {
            sessions_til_stripe: [],
            times_absent_til_contact: [],
        },
    }),
    computed: {
        sessionLabel() {
            return this.$store.state.settings[this.belt].combine_same_day_checkins ?
                `1 day's training equals 1 session` : `1 class equals 1 session`;
        },
        settings() {
            return this.$store.state.settings;
        },
    },
    methods: {
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
                        this.refresh();
                    }
                });
        },
        async refresh() {
            await this.$store.dispatch('getSettings');
        },
    },
}
</script>
