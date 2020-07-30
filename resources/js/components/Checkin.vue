<template>
    <div>
        <v-dialog v-model="show.checkin" width="800px" :persistent="true">
            <v-card>
                <v-card-title class="grey darken-2">{{(checkin.id ? 'Edit a ' : 'New ') + 'check-in'}}</v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <v-col cols="10">
                            <v-select
                                v-model="checkin.client_id"
                                :items="clients"
                                :error-messages="error.client_id"
                                label="Academy"
                                item-text="name"
                                item-value="id"
                            />
                        </v-col>
                    </v-row>
                    <v-row class="mx-2">
                        <v-col cols="10">
                            <v-select
                                v-model="checkin.user_id"
                                :items="people"
                                :error-messages="error.user_id"
                                label="Person"
                                :loading="loading.people"
                            />
                        </v-col>
                    </v-row>
                    <v-row class="mx-2">
                        <v-col cols="3">
                            <v-text-field
                                label="Date"
                                :outlined="true"
                                :value="new Date(date + ' ' + time).toLocaleDateString()"
                                @click="show.datepicker = true"
                                prepend-inner-icon="mdi-calendar-month-outline"
                            />
                        </v-col>
                        <v-col cols="3">
                            <v-text-field
                                label="Time"
                                :outlined="true"
                                :value="new Date(date + ' ' + time).toLocaleTimeString()"
                                @click="show.timepicker = true"
                                prepend-inner-icon="mdi-clock-outline"
                            />
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
        <v-dialog v-model="show.datepicker" class="mx-auto" width="290px">
            <v-date-picker v-model="date" />
        </v-dialog>
        <v-dialog v-model="show.timepicker" class="mx-auto" width="290px">
            <v-time-picker v-model="time" ampm-in-title />
        </v-dialog>
        <v-snackbar v-model="snackbar.show" :bottom="true" :multi-line="true">{{snackbar.text}}</v-snackbar>
    </div>
</template>

<script>
    import {headers} from '../authorization';
    import {dateToLocalSql, timeToLocalSql} from '../datetime_converters';

    export default {
		name: "Checkin",
        data: function() {
            return  {
                checkin: {
                    id: null,
                    client_id: null,
                    user_id: null,
                    checked_in_at: null,
                },
                date: null,
                error: {
                    client_id: null,
                    user_id: null,
                },
                loading: {
                    clients: false,
                    people: false,
                },
                saving: false,
                show: {
                    checkin: false,
                    datepicker: false,
                    timepicker: false,
                },
                snackbar: {
                    show: false,
                    text: '',
                },
                time: null,
            };
        },
        computed: {
            clients() {
                return this.$store.state.clients;
            },
            people() {
                return this.$store.state.people
                    .filter( person => person.client_id === this.checkin.client_id)
                    .map( person => ({text: person.name, value: person.id}));
            },
            user() {
                return this.$store.state.user;
            },
        },
        watch: {
		    'checkin.checked_in_at': function(newDt, oldDt) {

		        if (newDt && (newDt !== oldDt)) {
                    this.date = dateToLocalSql(this.checkin.checked_in_at + ' UTC');
                    this.time = timeToLocalSql(this.checkin.checked_in_at + ' UTC');
                }
            },
        },
        methods: {
		    clickSave() {
                this.saving = true;
                this.resetErrors();

                const isoStr = new Date(`${this.date} ${this.time}`).toISOString();
                this.checkin.checked_in_at = isoStr.substr(0, 10) + ' ' + isoStr.substr(11, 8);

                fetch(`/checkin/` + (this.checkin.id ? this.checkin.id : ''), {
                    method: 'POST',
                    headers,
                    credentials: "same-origin",
                    body: JSON.stringify(this.checkin),
                })
                    .then(resp => resp.json())
                    .then(json => {
                        if (json.errors) {
                            this.error = Object.assign(this.error, json.errors);
                            this.saving = false;
                        } else if (json.id) {
                            this.close();
                            const datetime = new Date(json.checked_in_at + ' UTC');
                            this.$emit('save-checkin', `Checked in at ${datetime.toLocaleString()}`);
                        }
                    });
            },
            close() {
                this.show = {
                    checkin: false,
                    datepicker: false,
                    timepicker: false,
                };
                this.saving = false;
                this.resetErrors();
            },
            resetErrors() {
                this.error = {
                    client_id: null,
                    user_id: null,
                };
            },
            setCheckin(checkin) {
		        if (checkin) {
                    this.checkin = Object.assign({}, checkin);
                } else {
		            const datetime = new Date();
		            this.checkin = {
		                id: null,
		                client_id: this.user.client_id,
		                user_id: null,
		                checked_in_at:
                            datetime.toISOString().substr(0, 10)
                            + ' '
                            + datetime.toISOString().substr(11, 8),
                    };
                }
                this.show.checkin = true;
            },
        },
    }
</script>
