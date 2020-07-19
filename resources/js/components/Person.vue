<template>
    <div>
        <v-dialog v-model="show" width="800px" :persistent="true">
            <v-card>
                <v-card-title class="grey darken-2">Edit a person</v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <v-col cols="6">
                            <v-text-field
                                v-model="person.name"
                                :error-messages="error.name"
                                placeholder="required"
                                label="Name"
                            />
                        </v-col>

                        <v-col>
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
                                :disabled="disablePassword"
                            />
                        </v-col>
                        <v-col cols="6">
                            <v-text-field
                                v-model="person.password_confirmation"
                                :error-messages="error.password_confirmation"
                                type="password"
                                :placeholder="editing ? '••••••••' : 'required'"
                                label="Confirm Password"
                                :disabled="disablePassword"
                            />
                        </v-col>
                    </v-row>
                    <v-row class="mx-2">
                        <v-col cols="4">
                            <v-select
                                v-model="person.rank.belt"
                                :items="[
                                    {value: 1, text: 'White'},
                                    {value: 2, text: 'Blue'},
                                    {value: 3, text: 'Purple'},
                                    {value: 4, text: 'Brown'},
                                    {value: 5, text: 'Black'},
                                ]"
                                label="Belt"
                                value="White"
                                :error-messages="error.belt"
                            />
                        </v-col>
                        <v-col cols="2">
                            <v-select
                                v-model="person.rank.stripes"
                                :items="[0, 1, 2, 3, 4]"
                                label="Stripes"
                                value="0"
                                :error-messages="error.stripes"
                            />
                        </v-col>
                        <v-col cols="4">
                            <v-text-field
                                label="Last ranked up"
                                :value="person.rank.last_ranked_up"
                                @click="pickRankedDate = true"
                                prepend-inner-icon="mdi-calendar-month-outline"
                                :error-messages="error['rank.last_ranked_up'][0]"
                            />
                        </v-col>
                    </v-row>
                    <v-row class="mx-2">
                        <v-col cols="6">
                            <v-select
                                v-model="person.client_id"
                                :value="editing ? person.client_id : null"
                                :items="clients"
                                :error-messages="error.client_id"
                                label="Academy"
                                item-text="name"
                                item-value="id"
                            />
                        </v-col>
                        <v-col>
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
                        <v-col cols="4">
                            <v-text-field
                                label="Member since"
                                :value="person.start_date"
                                @click="pickStartDate = true"
                                prepend-inner-icon="mdi-calendar-month-outline"
                                :error-messages="error.start_date"
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

        <v-dialog v-model="pickRankedDate" class="mx-auto" width="290px">
            <v-date-picker v-model="person.rank.last_ranked_up" />
        </v-dialog>

        <v-dialog v-model="pickStartDate" class="mx-auto" width="290px">
            <v-date-picker v-model="person.start_date" />
        </v-dialog>
    </div>
</template>

<script>
    import {isSuperAdmin, isAdmin, headers} from "../authorization";

    export default {
		name: "Person",
        data: function() {
            return  {
                person: {
                    name: null,
                    email: null,
                    rank: {
                        belt: 1,
                        stripes: 0,
                        last_ranked_up: null,
                    },
                    roles: null,
                    client_id: null,
                    password: null,
                    password_confirmation: null,
                    start_date: null,
                },
                error: {
                    name: null,
                    email: null,
                    password: null,
                    password_confirmation: null,
                    belt: null,
                    stripes: null,
                    roles: null,
                    client_id: null,
                    start_date: null,
                    'rank.last_ranked_up': [],
                },
                roles: [],
                saving: false,
                show: false,
                pickRankedDate: false,
                pickStartDate: false,
            };
        },
        computed: {
		    editing() {
		        return this.person.id;
            },
            clients() {
		        return this.$store.state.clients;
            },
            disablePassword() {
		        if (this.person.roles) {
                    return !isSuperAdmin() && this.person.roles.includes(1);
                } else {
		            return false;
                }
            },
        },
        methods: {
		    clickSave() {
                this.saving = true;
                this.resetErrors();
                fetch('/users' + (this.person.hasOwnProperty('id') ? `/${this.person.id}` : ''), {
                    method: 'POST',
                    headers,
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
            resetErrors() {
                this.error = {
                    name: null,
                    email: null,
                    password: null,
                    password_confirmation: null,
                    roles: null,
                    client_id: null,
                    start_date: null,
                    'rank.last_ranked_up': [],
                };
            },
            resetPerson() {
                this.person = {
                    name: null,
                    email: null,
                    rank: {
                        belt: 'White',
                        stripes: 0,
                        last_ranked_up: null,
                    },
                    roles: null,
                    client_id: null,
                    start_date: null,
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
		    this.setRoles();
        },
    }
</script>
