<template>
    <v-container fluid>
        <v-row justify="center">
            <v-sheet width="900">
                <v-calendar
                    ref="calendar"
                    :now="today"
                    :value="today"
                    :events="events"
                    color="primary"
                    type="week"
                    :event-ripple="true"
                    first-interval="5"
                    interval-count="17"
                    @click:event="clickEvent"
                >
                </v-calendar>
            </v-sheet>
        </v-row>
    </v-container>

</template>

<script>
import {headers} from '../authorization';

export default {
    name: "Schedule",
    data: () => ({
        today: `${new Date().getFullYear()}-${new Date().getMonth() + 1}-${new Date().getDate()}`,
        events: [],
    }),
    computed: {
        user() {
            return this.$store.state.user;
        },
    },
    methods: {
        clickEvent(e) {
            this.$emit('edit-event', e.event);
        },
        refresh() {
            fetch(`/events/${this.user.client_id}`, {headers, credentials: "same-origin"})
                .then(resp => resp.json())
                .then(events => {
                    events = events.map(event => {
                        let dateStr = this.today;
                        const
                            now = new Date(),
                            curDay = now.getDay(),
                            diffDaysToEventDay = curDay - event.day_id;

                        if (diffDaysToEventDay < 0) {
                            // event is on a weekday after today
                            now.setDate(now.getDate() + Math.abs(diffDaysToEventDay));
                            dateStr = `${now.getFullYear()}-${now.getMonth() + 1}-${now.getDate()}`;
                        } else if (diffDaysToEventDay > 0) {
                            // event is on a weekday prior to today
                            now.setDate(now.getDate() - diffDaysToEventDay);
                            dateStr = `${now.getFullYear()}-${now.getMonth() + 1}-${now.getDate()}`;
                        }
                        return {
                            id: event.id,
                            name: event.name,
                            start: `${dateStr} ${event.start}`,
                            end: `${dateStr} ${event.end}`,
                        };
                    });
                    this.events = events;
                });
        },
    },
    created() {
        this.refresh();
    }
}
</script>

<style scoped>

</style>
