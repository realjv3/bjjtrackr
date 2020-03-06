<template>
    <v-container fluid>
        <v-row justify="center">
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
                    :items-per-page="5"
                    class="elevation-1"
                    :loading="loading"
                    :search="search"
                >
                    <template v-slot:item.action="{ item }">
                        <v-icon small class="mr-2" @click="$emit('edit-client', item)">edit</v-icon>
                        <v-icon small @click="delClient(item)">delete</v-icon>
                    </template>
                </v-data-table>
            </v-card>
        </v-row>

    </v-container>
</template>

<script>
    import {headers} from '../authorization';

    export default {
		name: "Clients",
        data: () => ({
            clients: [],
            headers: [
                { text: 'Name', align: 'left', value: 'name' },
                { text: 'Affiliation', value: 'affiliation' },
                { text: 'Address1', value: 'address1' },
                { text: 'Address2', value: 'address2' },
                { text: 'City', value: 'city' },
                { text: 'State', value: 'state' },
                { text: 'Postal Code', value: 'zip' },
                { text: 'Country', value: 'country' },
                { text: 'Actions', value: 'action', sortable: false },
            ],
            loading: true,
            search: '',
        }),
        methods: {
		    refresh() {
		        this.loading = true;
                fetch('/clients', {
                    headers,
                    credentials: "same-origin",
                })
                    .then( resp => {
                        if (resp.ok) {
                            return resp.json();
                        }
                    })
                    .then( json => {
                        this.clients = json;
                        this.loading = false;
                    });
            },
		    delClient(client) {
                confirm('Are you sure you want to delete this client?') &&
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
        created() {
		    this.refresh();
        },
    }
</script>
