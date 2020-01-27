<template>
    <v-dialog v-model="show" width="800px">
        <v-card>
            <v-card-title class="grey darken-2">Edit a person</v-card-title>
            <v-container>
                <v-row class="mx-2">
                    <v-col cols="12">
                        <v-text-field
                            v-model="person.name"
                            :error-messages="error.name"
                            placeholder="required"
                            label="Name"
                        />
                    </v-col>

                    <v-col cols="12">
                        <v-text-field
                            v-model="person.email"
                            :error-messages="error.email"
                            type="email"
                            placeholder="required"
                            label="Email"
                        />
                    </v-col>
                </v-row>
                <v-row class="mx-2">
                    <v-col cols="6">
                        <v-text-field
                            v-model="person.password"
                            :error-messages="error.password"
                            type="password"
                            :placeholder="editing ? '••••••••' : 'required'"
                            label="Password"
                        />
                    </v-col>
                    <v-col cols="6">
                        <v-text-field
                            v-model="person.password_confirmation"
                            :error-messages="error.password_confirmation"
                            type="password"
                            :placeholder="editing ? '••••••••' : 'required'"
                            label="Confirm Password"
                        />
                    </v-col>
                </v-row>
                <v-row class="mx-2">
                    <v-col cols="4">
                        <v-select
                            v-model="person.belt"
                            :items="['White', 'Blue', 'Purple', 'Brown', 'Black']"
                            label="Belt"
                            value="White"
                        />
                    </v-col>

                    <v-col cols="2">
                        <v-select
                            v-model="person.stripes"
                            :items="[0, 1, 2, 3, 4, 5, 6]"
                            label="Stripes"
                            value="0"
                        />
                    </v-col>

                    <v-col cols="6">
                        <v-select
                            v-model="person.roles"
                            :items="roles"
                            label="Roles"
                            :error-messages="error.roles"
                            multiple
                        />
                    </v-col>
                </v-row>
                <v-row class="mx-2">
                    <v-col cols="10">
                        <v-select
                            v-model="person.client_id"
                            :value="editing ? person.client_id : null"
                            :items="clients"
                            :error-messages="error.client_id"
                            label="Academy"
                        />
                    </v-col>
                </v-row>
                <v-row class="mx-2">
                    <v-col cols="12">
                        <v-textarea v-model="person.notes" label="Notes" outlined/>
                    </v-col>
                </v-row>
                <v-row justify="end">
                    <v-card-actions>
                        <v-btn text @click="clickSave" :loading="saving">Save</v-btn>
                        <v-btn text color="primary" @click="close">Cancel</v-btn>
                    </v-card-actions>
                </v-row>
            </v-container>

        </v-card>
    </v-dialog>
</template>

<script>
    import {isSuperAdmin, isAdmin} from "../authorization";

    export default {
		name: "Person",
        data: function() {
            return  {
                clients: [],
                person: {
                    name: null,
                    email: null,
                    belt: 'White',
                    stripes: 0,
                    roles: null,
                    client_id: null,
                    password: null,
                    password_confirmation: null,
                },
                error: {
                    name: null,
                    email: null,
                    password: null,
                    password_confirmation: null,
                    roles: null,
                    client_id: null,
                },
                roles: [],
                saving: false,
                show: false,
            };
        },
        computed: {
		    editing: function() {
		        return this.person.id
            },
        },
        methods: {
		    clickSave() {
                this.saving = true;
                this.resetErrors();
                fetch('/users' + (this.person.hasOwnProperty('id') ? `/${this.person.id}` : ''), {
                    method: 'POST',
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": CSRFToken,
                    },
                    credentials: "same-origin",
                    body: JSON.stringify(this.person),
                })
                    .then( resp => {
                        if (resp.status === 422) {
                            return resp.json();
                        } else {
                            this.$emit('save-person');
                            this.close();
                        }
                    })
                    .then( json => {
                        if (json && json.errors) {
                            this.error = Object.assign(this.error, json.errors);
                            this.saving = false;
                        }
                    });
            },
            close() {
                this.show = false;
                this.saving = false;
                this.resetPerson();
                this.resetErrors();
            },
            getClients() {
                fetch('/clients', {
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": CSRFToken,
                    },
                    credentials: "same-origin",
                })
                    .then( resp => {
                        if (resp.ok) {
                            return resp.json();
                        }
                    })
                    .then( json => {
                        this.clients = json.map( client => ({text: client.name, value: client.id}));
                    })
            },
            resetErrors() {
                this.error = {
                    name: null,
                    email: null,
                    password: null,
                    password_confirmation: null,
                    roles: null,
                    client_id: null,
                };
            },
            resetPerson() {
                this.person = {
                    name: null,
                    email: null,
                    belt: 'White',
                    stripes: 0,
                    roles: null,
                    client_id: null,
                    password_confirmation: null,
                };
            },
            setRoles() {
		        const roles = [];
		        if (isSuperAdmin()) {
		            roles.push({value: 1, text: 'Super Admin'});
                }
		        if (isSuperAdmin() || isAdmin()) {
		            roles.push({value: 2, text: 'Administrator'});
		            roles.push({value: 3, text: 'Instructor'});
                }
		        roles.push({value: 4, text: 'Student'});
		        this.roles = roles;
            },
        },
        created() {
		    this.getClients();
		    this.setRoles();
        },
    }
</script>
