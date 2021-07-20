<template>
    <v-row class="my-10" justify="center">
        <v-card>

            <v-card-title>
                Log
                <v-spacer></v-spacer>

                <v-text-field
                    v-model="search"
                    append-icon="search"
                    label="Search"
                    single-line
                    hide-details
                ></v-text-field>

                <v-spacer></v-spacer>

                <v-icon id="refresh" small @click="refresh">refresh</v-icon>
            </v-card-title>

            <v-data-table
                :headers="headers"
                :items="log"
                :items-per-page="15"
                :sort-by="['date']"
                :sort-desc="true"
                class="elevation-1"
                :search="search"
                :loading="loading"
            >
                <template v-slot:item.date="{ item }">
                    <span>{{ utcToLocal(item.date) }}</span>
                </template>
            </v-data-table>
        </v-card>
    </v-row>
</template>

<script>

import {isSuperAdmin} from "../authorization";
import {utcDateTimeToLocal} from "../datetime_converters";

export default {
    name: "Log",
    data: () => ({
        headers: [
            { text: 'Date', value: 'date' },
            { text: 'Client', value: 'client_id' },
            { text: 'User', value: 'user_id' },
            { text: 'Type', value: 'type' },
            { text: 'Action', value: 'action' },
        ],
        loading: false,
        log: [],
        search: '',
    }),
    computed: {
        user() {
            return this.$store.state.user;
        },
    },
    methods: {
        isSuperAdmin,
        async refresh() {
            if (isSuperAdmin(this.user)) {
                this.loading = true;
                const resp = await fetch('/log');
                this.log = await resp.json();
                this.loading = false;
            }
        },
        utcToLocal: utcDateTimeToLocal,
    },
    created() {
        this.refresh();
    }
}
</script>

<style scoped>
#refresh {
    top: 9px;
    position: relative;
}
</style>
