<template>
    <v-container fluid>

        <v-row justify="center" class="my-11 mx-7">
            <v-card>
                <v-card-title>
                    Clients
                    <v-spacer></v-spacer>
                    <v-text-field
                        v-model="search"
                        append-icon="search"
                        label="Search"
                        single-line
                        hide-details
                    ></v-text-field>
                </v-card-title>
                <v-data-table
                    :headers="headers"
                    :items="clients"
                    :items-per-page="10"
                    class="elevation-1"
                    :search="search"
                    :loading="loading"
                >
                    <template v-slot:item.action="{ item }">
                        <v-icon small class="mr-2" @click="$emit('edit-client', item)">edit</v-icon>
                        <v-icon small @click="delClient(item)">delete</v-icon>
                    </template>
                </v-data-table>
            </v-card>
        </v-row>

        <Log />

    </v-container>
</template>

<script>
    import {headers} from '../authorization';
    import Log from './Log';

    export default {
		name: "Clients",
        components: {Log},
        data: () => ({
            headers: [
                { text: 'Name', align: 'left', value: 'name' },
                { text: 'Affiliation', value: 'affiliation' },
                { text: 'Active Members', value: 'activeMembers' },
                { text: 'City', value: 'city' },
                { text: 'State', value: 'state' },
                { text: 'Actions', value: 'action', sortable: false },
            ],
            loading: false,
            search: '',
        }),
        computed: {
            clients() {
                return this.$store.state.clients;
            },
        },
        methods: {
		    async refresh() {
		        this.loading = true;
                await this.$store.dispatch('getClients');
		        this.loading = false;
            },
		    delClient(client) {
                ! this.loading && confirm('Are you sure you want to delete this client?') &&
                fetch(`/clients/${client.id}`, {
                    method: 'DELETE',
                    headers,
                    credentials: "same-origin",
                })
                    .then( resp => {
                        if (resp.ok) {
                            this.refresh();
                        }
                    });
            },
        },
    }
</script>
