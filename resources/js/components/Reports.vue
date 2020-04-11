<template>
    <v-container fluid>
        <v-row justify="center">
            <v-card>
                <v-card-title>
                    Reports
                    <v-spacer></v-spacer>
                    <v-select
                        v-model="user"
                        :items="users"
                        item-value="id"
                        item-text="name"
                        :return-object="true"
                        style="width: 100px"
                    ></v-select>
                </v-card-title>
                <v-card-text>
                </v-card-text>
            </v-card>
        </v-row>

    </v-container>
</template>

<script>
import {isStudentOnly} from "../authorization";

export default {
    name: "Reports",
    data: () => ({
        user: {},
    }),
    computed: {
        users() {
            if (isStudentOnly()) {
                return this.$store.state.people.filter(person => person.id === user().id);
            }
            return this.$store.state.people.filter(person => person.roles.includes(4));
        },
    },
    watch: {
        users(newUsers) {
            this.user = newUsers[0];
        }
    },
}
</script>
