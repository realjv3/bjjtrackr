<template>
    <v-dialog v-model="show" width="800px" :persistent="true">
        <v-card>
            <v-card-title class="grey darken-2" v-text="client.settings ? `Edit academy info` : `Edit a client`"/>
            <v-container>
                <v-row class="mx-2">
                    <v-col cols="6">
                        <v-text-field
                            v-model="client.name"
                            :error-messages="error"
                            placeholder="Academy name (required)"
                        />
                    </v-col>

                    <v-col cols="6">
                        <v-text-field v-model="client.affiliation" placeholder="Affiliation" />
                    </v-col>

                    <v-col cols="12">
                        <v-text-field v-model="client.address1" placeholder="Address 1" />
                    </v-col>

                    <v-col cols="12">
                        <v-text-field v-model="client.address2" placeholder="Address 2" />
                    </v-col>

                    <v-col cols="6">
                        <v-text-field v-model="client.city" placeholder="City" />
                    </v-col>

                    <v-col cols="4">
                        <v-text-field v-model="client.state" placeholder="State/Province" />
                    </v-col>

                    <v-col cols="2">
                        <v-text-field v-model="client.zip" placeholder="Postal code" />
                    </v-col>

                    <v-col cols="6">
                        <v-text-field v-model="client.country" placeholder="Country" />
                    </v-col>

                    <v-col cols="12">
                        <v-textarea v-model="client.notes" placeholder="Notes" outlined/>
                    </v-col>
                </v-row>
                <v-row justify="end">
                    <v-card-actions>
                        <v-btn text @click="clickSave" :loading="loading">Save</v-btn>
                        <v-btn text color="primary" @click="close">Cancel</v-btn>
                    </v-card-actions>
                </v-row>
            </v-container>

        </v-card>
    </v-dialog>
</template>

<script>
    import {headers} from '../authorization';

    /**
     * Form for client CRUD
     */
    export default {
		name: "Client",
        data: function() {
            return  {
                show: false,
                client: {
                    name: null,
                    affiliation: null,
                    address1: null,
                    address2: null,
                    city: null,
                    state: null,
                    zip: null,
                    country: null,
                    notes: null,
                    settings: false,
                },
                error: null,
                loading: false,
            };
        },
        methods: {
		    clickSave() {
                this.loading = true;
                fetch('/clients' + (this.client.hasOwnProperty('id') ? `/${this.client.id}` : ''), {
                    method: 'POST',
                    headers,
                    credentials: "same-origin",
                    body: JSON.stringify(this.client),
                })
                    .then( resp => resp.json())
                    .then( json => {
                        if (json.errors) {
                            this.error = json.errors.name;
                            this.loading = false;
                        } else {
                            const clients = this.$store.state.clients.filter(client => client.id !== json.id);
                            this.$store.commit('setClients', [...clients, json]);
                            this.close();
                        }
                    });
            },
            close() {
                this.show = false;
                this.loading = false;
                this.client = {
                    name: null,
                    affiliation: null,
                    address1: null,
                    address2: null,
                    city: null,
                    state: null,
                    zip: null,
                    country: null,
                    notes: null,
                    settings: false,
                };
                this.error = null;
            },
        },
	}
</script>
