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

    export default {
		name: "People",
        data: () => ({
            headers: [
                { text: 'Name', align: 'left', value: 'name' },
                { text: 'Belt', value: 'belt' },
                { text: 'Stripes', value: 'stripes' },
                { text: 'Email', value: 'email' },
                { text: 'Client', value: 'client' },
                { text: 'Last Check-in', value: 'lastcheckin' },
                { text: 'Actions', value: 'action', sortable: false },
            ],
            loading: true,
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
        },
        created() {
		    this.refresh();
        },
    }
</script>
