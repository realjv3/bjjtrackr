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
                                <v-text-field
                                    v-model="settings[belt].classes_til_stripe"
                                    type="number"
                                    label="Classes until next stripe"
                                    style="width: 150px"
                                    @change="update"
                                />
                                <v-text-field
                                    v-model="settings[belt].times_absent_til_contact"
                                    type="number"
                                    label="Times absent until contact"
                                    style="width: 150px"
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
    }),
    computed: {
        settings() {
            return this.$store.state.settings;
        },
    },
    methods: {
        update() {
            fetch(`/settings/${this.$store.state.user.client_id}`, {
                method: 'POST',
                headers,
                credentials: "same-origin",
                body: JSON.stringify({
                    belt: this.belt,
                    classes_til_stripe: this.settings[this.belt].classes_til_stripe,
                    times_absent_til_contact: this.settings[this.belt].times_absent_til_contact,
                }),
            })
                .then( resp => resp.json())
                .then( () => this.refresh() );
        },
        async refresh() {
            await this.$store.dispatch('getSettings');
        },
    },
}
</script>
