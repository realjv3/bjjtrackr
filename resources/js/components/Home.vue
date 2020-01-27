<template>
    <v-app>
        <v-navigation-drawer
            v-model="drawer"
            :clipped="$vuetify.breakpoint.lgAndUp"
            app
        >
            <v-list dense>

                <v-list-item v-if="isSuperAdmin() || isAdmin()" key="clients" link @click="show = 'Clients'">
                    <v-list-item-action>
                        <v-icon>mdi-account-cash</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>Clients</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <template v-for="item in items">

                    <v-row v-if="item.heading" :key="item.heading" align="center">
                        <v-col cols="6">
                            <v-subheader v-if="item.heading">{{ item.heading }}</v-subheader>
                        </v-col>
                        <v-col cols="6" class="text-center">
                            <a href="#!" class="body-2 black--text">EDIT</a>
                        </v-col>
                    </v-row>

                    <v-list-group
                        v-else-if="item.children"
                        :key="item.text"
                        v-model="item.model"
                        :prepend-icon="item.model ? item.icon : item['icon-alt']"
                        append-icon=""
                    >
                        <template v-slot:activator>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ item.text }}
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </template>
                        <v-list-item
                            v-for="(child, i) in item.children"
                            :key="i"
                            link
                        >
                            <v-list-item-action v-if="child.icon">
                                <v-icon>{{ child.icon }}</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title>
                                    {{ child.text }}
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list-group>

                    <v-list-item v-else :key="item.text" link @click="show = item.text">
                        <v-list-item-action>
                            <v-icon>{{ item.icon }}</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>
                                {{ item.text }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar
            :clipped-left="$vuetify.breakpoint.lgAndUp"
            app
            color="indigo lighten-2"
            dark
        >
            <v-app-bar-nav-icon @click.stop="drawer = !drawer" />

            <v-toolbar-title
                style="width: 300px"
                class="ml-0 pl-4"
            >
                <span class="hidden-sm-and-down">BJJ Trackr</span>
            </v-toolbar-title>

            <v-spacer />

            <v-menu offset-y>

                <template v-slot:activator="{on}">
                    <v-btn icon v-on="on">
                        <v-icon>mdi-chevron-down</v-icon>
                    </v-btn>
                </template>

                <v-list-item key="logout" link href="/logout">

                    <v-list-item-icon>
                        <v-icon>mdi-logout</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Log out</v-list-item-title>
                    </v-list-item-content>

                </v-list-item>

            </v-menu>
        </v-app-bar>

        <v-content>
            <v-container class="fill-height" fluid>
                <v-row align="center" justify="center">

                    <People
                        v-if="(isSuperAdmin() || isAdmin() || isInstructor()) && show === 'People'"
                        ref="people"
                        @edit-person="onEditPerson"
                    />

                    <Clients v-if="isSuperAdmin() && show === 'Clients'" ref="clients" @edit-client="onEditClient"/>
                </v-row>
            </v-container>
        </v-content>

        <v-speed-dial v-model="speedDial" bottom right fixed open-on-hover>

            <template v-slot:activator>
                <v-btn v-model="speedDial" color="pink" dark fab>
                    <v-icon v-if="speedDial">mdi-close</v-icon>
                    <v-icon v-else>mdi-plus</v-icon>
                </v-btn>
            </template>

            <v-tooltip left>
                <template v-slot:activator="{ on }">
                    <v-btn fab dark small color="primary" v-on="on">
                        <v-icon>mdi-history</v-icon>
                    </v-btn>
                </template>
                <span>Check-in</span>
            </v-tooltip>

            <v-tooltip v-if="isAdmin() || isSuperAdmin()" left>
                <template v-slot:activator="{ on }">
                    <v-btn @click="$refs.person.show = true" fab dark small color="primary" v-on="on">
                        <v-icon>mdi-contacts</v-icon>
                    </v-btn>
                </template>
                <span>Person</span>
            </v-tooltip>

            <v-tooltip v-if="isSuperAdmin()" left>
                <template v-slot:activator="{ on }">
                    <v-btn @click="$refs.client.show = true" fab dark small color="primary" v-on="on">
                        <v-icon>mdi-account-cash</v-icon>
                    </v-btn>
                </template>
                <span>Client</span>
            </v-tooltip>

        </v-speed-dial>

        <Client ref="client" @save-client="onSaveClient"/>

        <Person ref="person" @save-person="onSavePerson"/>
    </v-app>
</template>

<script>
    import {isSuperAdmin, isAdmin, isInstructor} from "../authorization";
    import People from "components/People";
    import Person from "components/Person";
    import Clients from "components/Clients";
    import Client from "components/Client";

    export default {
        components: {Client, Clients, People, Person},
        props: {
            source: String,
        },
        data: () => ({
            drawer: null,
            items: [
                { icon: 'mdi-contacts', text: 'People' },
                { icon: 'mdi-history', text: 'Check-ins' },
                { icon: 'mdi-barcode', text: 'Barcodes' },
                {
                    icon: 'mdi-chevron-up',
                    'icon-alt': 'mdi-chevron-down',
                    text: 'More',
                    model: false,
                    children: [
                        { text: 'Import' },
                        { text: 'Export' },
                        { text: 'Print' },
                        { text: 'Undo changes' },
                        { text: 'Other contacts' },
                    ],
                },
                { icon: 'mdi-settings', text: 'Settings' },
                { icon: 'mdi-message', text: 'Send feedback' },
                { icon: 'mdi-help-circle', text: 'Help' },
            ],
            show: 'People',
            speedDial: false,
        }),
        methods: {
            isSuperAdmin,
            isAdmin,
            isInstructor,
            onSaveClient() {
                this.$refs.clients.refresh();
            },
            onEditClient(client) {
                this.$refs.client.client = Object.assign({}, client);
                this.$refs.client.show = true;
            },
            onSavePerson() {
                this.$refs.people.refresh();
            },
            onEditPerson(person) {
                this.$refs.person.person = Object.assign({}, person);
                this.$refs.person.show = true;
            },
        },
    }
</script>

<style>
    td {
        color: #d3d3d3;
    }
</style>
