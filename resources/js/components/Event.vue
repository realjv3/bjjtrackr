<template>
    <div>
        <v-dialog v-model="show.event" width="800px" :persistent="true">
            <v-card>
                <v-card-title class="grey darken-2">{{(event.id ? 'Edit a ' : 'New ') + 'class'}}</v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <v-col cols="10">
                            <v-select
                                v-model="event.client_id"
                                :items="clients"
                                :error-messages="error.client_id"
                                label="Academy"
                                item-text="name"
                                item-value="id"
                            />
                        </v-col>
                    </v-row>
                    <v-row class="mx-2">
                        <v-col cols="8">
                            <v-text-field v-model="event.name" label="Name" :error-messages="error.name"/>
                        </v-col>
                    </v-row>
                    <v-row class="mx-2">
                        <v-col cols="3">
                            <v-select
                                v-model="event.day_id"
                                :items="days"
                                :error-messages="error.day_id"
                                label="Day"
                                item-text="name"
                                item-value="id"
                            />
                        </v-col>
                        <v-col cols="3">
                            <v-text-field
                                label="Start Time"
                                :outlined="true"
                                :value="timeToLocale(event.start)"
                                :error-messages="error.start"
                                @click="show.startTimePicker = true"
                                prepend-inner-icon="mdi-clock-outline"
                            />
                        </v-col>
                        <v-col cols="3">
                            <v-text-field
                                label="End Time"
                                :outlined="true"
                                :value="timeToLocale(event.end)"
                                :error-messages="error.end"
                                @click="show.endTimePicker = true"
                                prepend-inner-icon="mdi-clock-outline"
                            />
                        </v-col>
                    </v-row>
                    <v-row justify="end">
                        <v-card-actions>
                            <v-btn text @click="clickSave" :loading="saving">Save</v-btn>
                            <v-btn v-if="event.id" text @click="clickDelete" :disabled="saving" color="red">Delete</v-btn>
                            <v-btn text color="primary" @click="close">Close</v-btn>
                        </v-card-actions>
                    </v-row>
                </v-container>
            </v-card>
        </v-dialog>
        <v-dialog v-model="show.startTimePicker" class="mx-auto" width="290px">
            <v-time-picker v-model="event.start" ampm-in-title :allowed-hours="hoursAllowed" />
        </v-dialog>
        <v-dialog v-model="show.endTimePicker" class="mx-auto" width="290px">
            <v-time-picker v-model="event.end" ampm-in-title :allowed-hours="hoursAllowed" />
        </v-dialog>
    </div>
</template>

<script>
import {headers} from '../authorization';
import {timeToLocale} from '../datetime_converters';

/**
 * @emits save-event
 */
export default {
    name: "Event",
    data: function() {
        return  {
            event: {
                id: null,
                client_id: this.$store.state.user.client_id,
                name: null,
                day_id: 1,
                start: '18:00',
                end: '19:00',
            },
            error: {
                day_id: null,
                name: null,
                start: null,
                end: null,
            },
            hoursAllowed: [5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21],
            saving: false,
            show: {
                event: false,
                startTimePicker: false,
                endTimePicker: false,
            },
        };
    },
    computed: {
        clients() {
            return this.$store.state.clients;
        },
        days() {
            return this.$store.state.days;
        },
        user() {
            return this.$store.state.user;
        },
    },
    methods: {
        clickDelete() {
            this.saving = true;
            this.resetErrors();

            fetch(`/event/${this.event.id}`, {method: 'DELETE', headers, credentials: "same-origin"})
                .then(resp => resp.json())
                .then(json => {
                    if (json.errors) {
                        this.error = Object.assign(this.error, json.errors);
                        this.saving = false;
                    } else {
                        this.$emit('save-event', 'deleted');
                        this.close();
                    }
                });
        },
        clickSave() {
            this.saving = true;
            this.resetErrors();

            fetch(`/event/` + (this.event.id ? this.event.id : ''), {
                method: 'POST',
                headers,
                credentials: "same-origin",
                body: JSON.stringify(this.event),
            })
                .then(resp => resp.json())
                .then(json => {
                    if (json.errors) {
                        this.error = Object.assign(this.error, json.errors);
                        this.saving = false;
                    } else if (json.id) {
                        this.$emit('save-event', 'scheduled');
                        this.close();
                    }
                });
        },
        close() {
            this.event = {
                id: null,
                client_id: user().client_id,
                name: null,
                day_id: 1,
                start: '18:00',
                end: '19:00',
            };
            this.show = {
                event: false,
                startTimePicker: false,
                endTimePicker: false,
            };
            this.saving = false;
            this.resetErrors();
        },
        resetErrors() {
            this.error = {
                day_id: null,
                name: null,
                start: null,
                end: null,
            };
        },
        async setEvent(e) {
            if (e.id) {
                const
                    resp = await fetch(`/events/${this.user.client_id}/${e.id}`, {headers, credentials: "same-origin"}),
                    event = await resp.json();
                if (resp.ok) {
                    this.event = event;
                    this.show.event = true;
                }
            }
        },
        timeToLocale,
    },
}
</script>
