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
export default {
    name: "Schedule",
    computed: {
        events() {
            return this.$store.state.events;
        },
        today() {
            const d = new Date();
            return `${d.getFullYear()}-${d.getMonth() + 1}-${d.getDate()}`;
        },
        user() {
            return this.$store.state.user;
        },
    },
    methods: {
        clickEvent(e) {
            this.$emit('edit-event', e.event);
        },
        refresh() {
            this.$store.dispatch('getEvents');
        },
    },
    created() {
        this.refresh();
    }
}
</script>

<style scoped>

</style>
