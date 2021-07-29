<template>
    <v-container fluid>

        Documentation for
        <v-select
            v-model="selected"
            :items="topics"
            item-value="id"
            item-text="name"
            :return-object="true"
            class="d-inline-block ms-1"
            id="person-select"
            @change="change"
        ></v-select>

        <HelpPeople v-show="selected.id === 'people'"/>

        <HelpSchedule v-show="selected.id === 'schedule'"/>

        <HelpCheckins v-show="selected.id === 'checkins'"/>

        <HelpSettings v-show="selected.id === 'settings'"/>

    </v-container>
</template>

<script>
import HelpCheckins from "./HelpCheckins";
import HelpPeople from "./HelpPeople";
import HelpSchedule from "./HelpSchedule";
import HelpSettings from "./HelpSettings";

export default {
    name: "Help",
    components: {HelpCheckins, HelpSchedule, HelpPeople, HelpSettings},
    data() {
        const topics = [
            {id: 'people', name: 'People'},
            {id: 'schedule', name: 'Schedule'},
            {id: 'checkins', name: 'Attendance'},
            {id: 'settings', name: 'Settings'},
        ];

        return ({
            selected: topics[0],
            topics,
        });
    },
}
</script>

