<template>
    <v-container fluid>
        <v-row justify="center">
            <v-card>
                <v-card-title>
                    Check-ins
                    <v-spacer></v-spacer>
                    <v-text-field
                        v-model="search"
                        append-icon="search"
                        label="Search"
                        single-line
                        hide-details
                    ></v-text-field>
                </v-card-title>
                <v-row class="mx-2" justify="center">
                    <v-col cols="8">
                        <v-select v-model="clientId" :items="clients" item-text="name" item-value="id"></v-select>
                    </v-col>
                </v-row>
                <v-data-table
                    :headers="headers"
                    :items="checkins"
                    class="elevation-1"
                    :loading="loading"
                    :search="search"
                    sort-by="checked_in_at"
                    :sort-desc="true"
                >
                    <template v-slot:item.checked_in_at="{ item }">
                        <span>{{utcToLocal(item.checked_in_at)}}</span>
                    </template>

                    <template v-slot:item.action="{ item }">
                        <v-icon small class="mr-2" @click="$emit('edit-checkin', item)">edit</v-icon>
                        <v-icon small @click="delCheckin(item)">delete</v-icon>
                    </template>
                </v-data-table>
            </v-card>
        </v-row>

    </v-container>
</template>

<script>

import {headers, isStudentOnly} from "../authorization";
import {utcToLocal} from "../datetime_converters";

export default {
    name: "Checkins",
    data: () => ({
        clientId: null,
        headers: [
            { text: 'Name', align: 'left', value: 'user.name' },
            { text: 'Check-in', value: 'checked_in_at' },
            { text: 'Actions', value: 'action', sortable: false },
        ],
        loading: false,
        search: '',
    }),
    computed: {
        checkins() {
            if ( ! this.$store.state.checkins.length) {
                return [];
            }
            return this.$store.state.checkins.filter(checkin => checkin.user.client_id === this.clientId);
        },
        clients() {
            return this.$store.state.clients;
        },
    },
    watch: {
        clients(newClients) {
            if (newClients.length) {
                this.clientId = newClients[0].id;
            }
        },
    },
    methods: {
        utcToLocal,
        delCheckin(checkin) {
            if (! isStudentOnly()) {
                confirm('Are you sure you want to delete this checkin?') &&
                fetch(`/checkin/${checkin.id}`, {
                    method: 'DELETE',
                    headers,
                    credentials: "same-origin",
                })
                    .then( resp => {
                        if (resp.ok) {
                            this.refresh();
                        }
                    });
            }
        },
        async refresh() {
            this.loading = true;
            await this.$store.dispatch('getCheckins');
            this.loading = false;
        },
    },
    created() {
        this.refresh();
    }
}
</script>
