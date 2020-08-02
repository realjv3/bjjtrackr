<template>
    <v-container fluid>
        <v-row justify="center">
            <v-card>
                <v-card-title>
                    People
                    <v-spacer></v-spacer>
                    <v-text-field
                        v-model="search"
                        append-icon="search"
                        label="Search"
                        single-line
                        hide-details
                    ></v-text-field>
                </v-card-title>
                <v-data-table
                    :headers="headers"
                    :items="users"
                    :items-per-page="5"
                    class="elevation-1"
                    :loading="loading"
                    :search="search"
                >
                    <template v-slot:item.last_checkin="{ item }">
                        <span>{{utcToLocal(item.last_checkin)}}</span>
                    </template>

                    <template v-slot:item.action="{ item }">
                        <v-icon small class="mr-2" @click="$emit('edit-person', item)">edit</v-icon>
                        <v-icon small @click="delPerson(item)">delete</v-icon>
                    </template>
                </v-data-table>
            </v-card>
        </v-row>

    </v-container>
</template>

<script>
import {headers} from '../authorization';
import {utcDateTimeToLocal} from "../datetime_converters";

export default {
    name: "People",
    data: () => ({
        headers: [
            { text: 'Name', align: 'left', value: 'name' },
            { text: 'Belt', value: 'rank.belt' },
            { text: 'Stripes', value: 'rank.stripes' },
            { text: 'Email', value: 'email' },
            { text: 'Client', value: 'client' },
            { text: 'Last Check-in', value: 'last_checkin' },
            { text: 'Actions', value: 'action', sortable: false },
        ],
        loading: false,
        search: '',
    }),
    computed: {
        users() {
            return this.$store.state.people;
        },
    },
    methods: {
        async refresh() {
            this.loading = true;
            await this.$store.dispatch('getPeople');
            this.loading = false;
        },
        delPerson(person) {
            confirm('Are you sure you want to delete this person?') &&
            fetch(`/users/${person.id}`, {method: 'DELETE', headers, credentials: "same-origin"})
                .then( resp => {
                    if (resp.ok) {
                        this.refresh();
                    }
                });
        },
        utcToLocal: utcDateTimeToLocal,
    },
}
</script>
