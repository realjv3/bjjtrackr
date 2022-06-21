<template>
    <v-container fluid>
        <v-row justify="center">
            <v-col class="col-lg-4">
                <v-select
                    label="Documentation for"
                    v-model="selected"
                    :items="topics"
                    item-value="id"
                    item-text="name"
                    :return-object="true"
                    class="d-inline-block my-8"
                    id="person-select"
                    @change="change"
                />
            </v-col>
        </v-row>
        <v-row justify="center">

            <HelpPeople class="col-sm-12" v-show="selected.id === 'people'"/>

            <HelpSales class="col-sm-12" v-show="selected.id === 'sales'"/>

            <HelpMembers class="col-sm-12" v-show="selected.id === 'members'"/>

            <HelpSchedule class="col-sm-12" v-show="selected.id === 'schedule'"/>

            <HelpCheckins class="col-sm-12" v-show="selected.id === 'checkins'"/>

            <HelpDocuments class="col-sm-12" v-show="selected.id === 'documents'"/>

            <HelpSettings class="col-sm-12" v-show="selected.id === 'settings'"/>
        </v-row>

    </v-container>
</template>

<script>
import HelpCheckins from "./HelpCheckins";
import HelpDocuments from "./HelpDocuments";
import HelpMembers from "./HelpMembers";
import HelpPeople from "./HelpPeople";
import HelpSales from "./HelpSales";
import HelpSchedule from "./HelpSchedule";
import HelpSettings from "./HelpSettings";

export default {
    name: "Help",
    components: {HelpCheckins, HelpDocuments, HelpMembers, HelpPeople, HelpSales, HelpSchedule, HelpSettings},
    data() {
        const topics = [
            {id: 'people', name: 'People'},
            {id: 'members', name: 'Memberships'},
            {id: 'sales', name: 'Sales'},
            {id: 'schedule', name: 'Schedule'},
            {id: 'checkins', name: 'Attendance'},
            {id: 'documents', name: 'Documents'},
            {id: 'settings', name: 'Settings'},
        ];

        return ({
            selected: topics[0],
            topics,
        });
    },
}
</script>

