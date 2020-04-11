<template>
    <v-app>
        <v-navigation-drawer
            v-model="drawer"
            :clipped="$vuetify.breakpoint.lgAndUp"
            app
        >
            <v-list>

                <v-list-item v-if="isSuperAdmin()" key="clients" link @click="show = 'Clients'">
                    <v-list-item-action>
                        <v-icon>mdi-account-cash</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>Clients</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item v-if="isSuperAdmin() || isAdmin()" key="people" link @click="show = 'People'">
                    <v-list-item-action>
                        <v-icon>mdi-contacts</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>People</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item key="checkins" link @click="show = 'Check-ins'">
                    <v-list-item-action>
                        <v-icon>mdi-history</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>Check-ins</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item key="qrcodes" link @click="show = 'QRCodes'">
                    <v-list-item-action>
                        <v-icon>mdi-qrcode</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>QR Codes</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item key="reports" link @click="show = 'Reports'">
                    <v-list-item-action>
                        <v-icon>mdi-file-chart</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>Reports</v-list-item-title>
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
                        v-show="( ! isStudentOnly()) && show === 'People'"
                        ref="people"
                        @edit-person="onEditPerson"
                    />

                    <Clients v-show="isSuperAdmin() && show === 'Clients'" ref="clients" @edit-client="onEditClient"/>

                    <Checkins v-show="show === 'Check-ins'" ref="checkins" @edit-checkin="onEditCheckin" />

                    <QRCodes v-show="show === 'QRCodes'" @edit-person="onEditPerson" />

                    <Reports v-show="show === 'Reports'"/>

                    <Settings v-show="show === 'Settings'"/>
                </v-row>

                <v-speed-dial
                    v-if="! isStudentOnly()"
                    v-model="speedDial"
                    bottom right fixed open-on-hover
                >

                    <template v-slot:activator>
                        <v-btn v-model="speedDial" color="pink" dark fab>
                            <v-icon v-if="speedDial">mdi-close</v-icon>
                            <v-icon v-else>mdi-plus</v-icon>
                        </v-btn>
                    </template>

                    <v-tooltip v-if="! isStudentOnly()" left>
                        <template v-slot:activator="{ on }">
                            <v-btn @click="$refs.checkin.setCheckin(null)" fab dark small color="primary" v-on="on">
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

                <Client v-show="isSuperAdmin()" ref="client" @save-client="onSaveClient"/>

                <Person v-show="isSuperAdmin() || isAdmin()" ref="person" @save-person="onSavePerson"/>

                <Checkin v-show="! isStudentOnly()" ref="checkin" @save-checkin="onSaveCheckin"/>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
    import {isSuperAdmin, isAdmin, isStudentOnly} from "../authorization";
    import People from "components/People";
    import Person from "components/Person";
    import Clients from "components/Clients";
    import Client from "components/Client";
    import Checkins from "components/Checkins";
    import Checkin from "components/Checkin";
    import QRCodes from "components/QRCodes";
    import Reports from "components/Reports";
    import Settings from "components/Settings";

    export default {
        components: {Client, Clients, People, Person, Checkins, Checkin, QRCodes, Reports, Settings},
        props: {
            source: String,
        },
        data: () => ({
            drawer: null,
            items: [
                { icon: 'mdi-settings', text: 'Settings' },
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
                { icon: 'mdi-message', text: 'Send feedback' },
                { icon: 'mdi-help-circle', text: 'Help' },
            ],
            show: 'Reports',
            speedDial: false,
        }),
        methods: {
            isSuperAdmin,
            isAdmin,
            isStudentOnly,
            onSaveClient() {
                if (this.$refs.clients) {
                    this.$refs.clients.refresh();
                }
            },
            onEditClient(client) {
                if (this.$refs.client && isSuperAdmin()) {
                    this.$refs.client.client = Object.assign({}, client);
                    this.$refs.client.show = true;
                }
            },
            onSavePerson() {
                if (this.$refs.people && (isSuperAdmin() || isAdmin())) {
                    this.$refs.people.refresh();
                }
            },
            onEditPerson(person) {
                if (this.$refs.person && (isSuperAdmin() || isAdmin())) {
                    this.$refs.person.person = Object.assign({}, person);
                    this.$refs.person.show = true;
                }
            },
            onSaveCheckin() {
                if (this.$refs.checkins) {
                    this.$refs.checkins.refresh();
                }
            },
            onEditCheckin(checkin) {
                if (this.$refs.checkin && (! isStudentOnly())) {

                    this.$refs.checkin.setCheckin(checkin);
                }
            },
        },
    }
</script>

<style>
    td {
        color: #d3d3d3;
    }
</style>
