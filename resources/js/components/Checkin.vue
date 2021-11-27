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
                    <v-row class="mx-2">
                        <v-col cols="8">
                            <v-select
                                v-model="checkin.event_id"
                                :items="daysEvents"
                                :item-text="item =>
                                `${item.name} - ${timeToLocale(item.start.slice(-8))} - ${timeToLocale(item.end.slice(-8))}`"
                                item-value="id"
                                label="Class"
                                @change="selectClass"
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
            <v-date-picker v-model="date" :allowed-dates="allowedDates"/>
        </v-dialog>
        <v-dialog v-model="show.timepicker" class="mx-auto" width="290px">
            <v-time-picker v-model="time" :allowed-hours="allowedHours" :allowed-minutes="allowedMinutes" ampm-in-title />
        </v-dialog>
        <v-snackbar v-model="snackbar.show" :bottom="true" :multi-line="true">{{snackbar.text}}</v-snackbar>
    </div>
</template>

<script>
    import {headers, isStudentOnly} from '../authorization';
    import {
        addMinutes,
        utcDateTimeToLocalYMD,
        utcDateTimeToLocal24Time,
        timeToLocale,
        utcDateTimeToLocal
    } from '../datetime_converters';

    export default {
		name: "Checkin",
        data: function() {
            return  {
                checkin: {
                    id: null,
                    client_id: null,
                    user_id: null,
                    event_id: null,
                    checked_in_at: null,
                },
                date: null,
                error: {
                    client_id: null,
                    user_id: null,
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
            classesTilStripe() {
                return Number(this.settings[this.selUser.rank.belt_id].sessions_til_stripe);
            },
            clients() {
                return this.$store.state.clients;
            },
            combineSameDayCheckins() {
                return this.settings[this.selUser.rank.belt_id].combine_same_day_checkins;
            },
            daysEvents() {
                if (this.date) {
                    const d = new Date();
                    d.setDate(Number(this.date.slice(-2)));
                    d.setMonth(Number(this.date.slice(5, 7)) - 1);
                    const dayId = d.getDay();
                    return this.$store.state.events.filter(event => event.day_id === dayId);
                } else {
                    return [];
                }
            },
            people() {
                if (isStudentOnly(this.user)) {
                    return this.$store.state.people
                        .filter(person => person.id === this.user.id)
                        .map( person => ({text: person.name, value: person.id}));
                }
                return this.$store.state.people
                    .filter( person => person.client_id === this.checkin.client_id)
                    .map( person => ({text: person.name, value: person.id}));
            },
            selUser() {
                return this.$store.state.people.find(person => person.id === this.checkin.user_id);
            },
            settings() {
                return this.$store.state.settings;
            },
            user() {
                return this.$store.state.user;
            },
        },
        watch: {
		    'checkin.checked_in_at': function(newDt, oldDt) {

		        if (newDt && (newDt !== oldDt)) {
                    this.date = utcDateTimeToLocalYMD(this.checkin.checked_in_at);
                    this.time = utcDateTimeToLocal24Time(this.checkin.checked_in_at);
                }
            },
        },
        methods: {
            /**
             * Students can only checkin today
             *
             * @param date {string}
             * @return {boolean}
             */
            allowedDates(date) {
                if (isStudentOnly(this.user)) {
                    const
                        today = new Date(),
                        dt = Number(date.slice(-2)),
                        month = Number(date.slice(5, 7)),
                        year = Number(date.slice(0, 4));
                    return dt === today.getDate()
                        && month === today.getMonth() + 1
                        && year === today.getFullYear();
                }
                return true;
            },
            /**
             * Students can only check 45m from their last checkin
             *
             * @param hour {string}
             * @return {boolean}
             */
            allowedHours(hour) {

                if (isStudentOnly(this.user) && this.selUser) {
                    const
                        lastCheckin = new Date(this.selUser.last_checkin + ' UTC'),
                        lastCheckinPlus45m = addMinutes(lastCheckin, 45);

                    return hour >= lastCheckinPlus45m.getHours();
                }
                return true;
            },
            /**
             * Students can only check 45m from their last checkin
             *
             * @param minute {string}
             * @return {boolean}
             */
            allowedMinutes(minute) {

                if (isStudentOnly(this.user) && this.selUser) {
                    const
                        lastCheckin = new Date(this.selUser.last_checkin + ' UTC'),
                        lastCheckinPlus45m = addMinutes(lastCheckin, 45),
                        selHour = Number(this.time.slice(0, 2));

                    if (selHour === lastCheckinPlus45m.getHours()) {
                        return minute >= lastCheckinPlus45m.getMinutes();
                    }
                    return true;
                }
                return true;
            },
		    clickSave() {
                this.saving = true;
                this.resetErrors();

                const isoStr = new Date(`${this.date} ${this.time}`).toISOString();

                fetch(`/checkin/` + (this.checkin.id ? this.checkin.id : ''), {
                    method: 'POST',
                    headers,
                    credentials: "same-origin",
                    body: JSON.stringify({
                        ...this.checkin,
                        checked_in_at: isoStr.substr(0, 10) + ' ' + isoStr.substr(11, 8)
                    }),
                })
                    .then(resp => resp.json())
                    .then(json => {
                        if (json.errors) {
                            this.error = Object.assign(this.error, json.errors);
                            this.saving = false;
                        } else if (json.id) {
                            this.notifyIfEligibleForPromo(this.selUser.id);
                            this.close();
                            this.$emit('save-checkin', `Checked in at ${utcDateTimeToLocal(json.checked_in_at)}`);
                        }
                    });
            },
            close() {
                this.checkin = {
                    id: null,
                    client_id: null,
                    user_id: null,
                    event_id: null,
                    checked_in_at: null,
                };
                this.show = {
                    checkin: false,
                    datepicker: false,
                    timepicker: false,
                };
                this.saving = false;
                this.date = null;
                this.time = null;
                this.resetErrors();
            },
            notifyIfEligibleForPromo(userId) {
                const selUsersCheckins = this.$store.state.checkins.filter(checkin =>
                    checkin.user_id === this.selUser.id && checkin.checked_in_at.slice(0, 10) > this.selUser.rank.last_ranked_up
                );
                let
                    result = [],
                    aggrUsersCheckins = [];

                if (selUsersCheckins.length && this.combineSameDayCheckins) {
                    for (let i = 0; i < selUsersCheckins.length; i++) {
                        if (selUsersCheckins[i]) {
                            let
                                j = 1,
                                combinedCheckins = [selUsersCheckins[i]];
                            // if there more checkins with same date,
                            // combine into array at index of first of them & delete the subsequent same-date checkins
                            const dateStr = utcDateTimeToLocal(selUsersCheckins[i].checked_in_at).slice(0, 10);
                            while (
                                selUsersCheckins[i + j]
                                && utcDateTimeToLocal(selUsersCheckins[i + j].checked_in_at).slice(0, 10) === dateStr
                                ) {
                                combinedCheckins.push(selUsersCheckins[i + j]);
                                j++;
                            }
                            if (combinedCheckins.length > 1) {
                                aggrUsersCheckins.push(combinedCheckins);
                                i = i + j - 1;
                            } else {
                                aggrUsersCheckins.push(selUsersCheckins[i]);
                            }
                        }
                    }
                }
                result = aggrUsersCheckins.length ? aggrUsersCheckins : selUsersCheckins;
                if (result.length === this.classesTilStripe - 2) {
                    fetch(`/eligible/` + userId, {
                        headers,
                        credentials: "same-origin",
                    });
                }
            },
            resetErrors() {
                this.error = {
                    client_id: null,
                    user_id: null,
                };
            },
            selectClass() {
		        const event = this.daysEvents.find(event => this.checkin.event_id === event.id);
		        this.time = event.start.slice(11);
            },
            setCheckin(checkin) {
		        if (checkin) {
                    this.checkin = Object.assign({}, checkin);
                } else {
		            const
                        now = new Date(),
                        ISONow = now.toISOString();
		            this.checkin = {
		                id: null,
		                client_id: this.user.client_id,
		                user_id: null,
		                event_id: null,
		                checked_in_at: ISONow.substr(0, 10) + ' ' + ISONow.substr(11, 8),
                    };
                }
                this.show.checkin = true;
            },
            timeToLocale,
        },
    }
</script>
