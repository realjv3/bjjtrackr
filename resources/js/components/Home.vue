<template>
    <v-app>
        <v-navigation-drawer
            v-model="drawer"
            :clipped="$vuetify.breakpoint.lgAndUp"
            app
        >
            <v-list>
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

                    <v-list-item v-else-if="item.allowed" :key="item.text" link @click="show = item.text">
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
                    <v-btn v-on="on" class="indigo lighten-2" :depressed="true">
                        <span>{{user.name}}</span>
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

        <v-main>
            <v-container class="fill-height" fluid>
                <v-row align="center" justify="center">

                    <People v-show="( ! isStudentOnly(user)) && show === 'People'" ref="people" @edit-person="onEditPerson" />

                    <Clients v-show="isSuperAdmin(user) && show === 'Clients'" ref="clients" @edit-client="onEditClient"/>

                    <Schedule v-show="show === 'Schedule'" ref="schedule" @edit-event="onEditEvent" />

                    <Checkins v-show="show === 'Check-ins'" ref="checkins" @edit-checkin="onEditCheckin" />

                    <QRCodes v-show="show === 'QRCodes'" @save-checkin="onEditCheckin" ref="qrcodes" />

                    <Reports v-show="show === 'Reports'"/>

                    <Settings v-show="(isSuperAdmin(user) || isAdmin(user)) && show === 'Settings'"/>

                    <Feedback
                        v-show="(isSuperAdmin(user) || isAdmin(user) || isInstructor(user)) && show === 'Send feedback'"
                    />
                </v-row>

                <Client v-show="isSuperAdmin(user)" ref="client" @save-client="onSaveClient"/>

                <Person v-show="isSuperAdmin(user) || isAdmin(user)" ref="person" @save-person="onSavePerson"/>

                <Event v-show="isSuperAdmin(user) || isAdmin(user)" ref="event" @save-event="onSaveEvent"/>

                <Checkin v-show="! isStudentOnly(user)" ref="checkin" @save-checkin="onSaveCheckin"/>

                <v-speed-dial
                    v-if="! isStudentOnly(user)"
                    v-model="speedDial"
                    bottom right fixed open-on-hover
                >

                    <template v-slot:activator>
                        <v-btn v-model="speedDial" color="pink" dark fab>
                            <v-icon v-if="speedDial">mdi-close</v-icon>
                            <v-icon v-else>mdi-plus</v-icon>
                        </v-btn>
                    </template>

                    <v-tooltip v-if="! isStudentOnly(user)" left>
                        <template v-slot:activator="{ on }">
                            <v-btn @click="$refs.checkin.setCheckin(null)" fab dark small color="primary" v-on="on">
                                <v-icon>mdi-history</v-icon>
                            </v-btn>
                        </template>
                        <span>Check-in</span>
                    </v-tooltip>

                    <v-tooltip v-if="isAdmin(user) || isSuperAdmin(user)" left>
                        <template v-slot:activator="{ on }">
                            <v-btn @click="$refs.person.show = true" fab dark small color="primary" v-on="on">
                                <v-icon>mdi-contacts</v-icon>
                            </v-btn>
                        </template>
                        <span>Person</span>
                    </v-tooltip>

                    <v-tooltip v-if="isAdmin(user) || isSuperAdmin(user)" left>
                        <template v-slot:activator="{ on }">
                            <v-btn @click="$refs.event.show.event = true" fab dark small color="primary" v-on="on">
                                <v-icon>mdi-calendar-month-outline</v-icon>
                            </v-btn>
                        </template>
                        <span>Class</span>
                    </v-tooltip>

                    <v-tooltip v-if="isSuperAdmin(user)" left>
                        <template v-slot:activator="{ on }">
                            <v-btn @click="$refs.client.show = true" fab dark small color="primary" v-on="on">
                                <v-icon>mdi-account-cash</v-icon>
                            </v-btn>
                        </template>
                        <span>Client</span>
                    </v-tooltip>

                </v-speed-dial>

                <v-snackbar v-model="snackbar.show" :bottom="true" :multi-line="true">{{snackbar.text}}</v-snackbar>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
import {isSuperAdmin, isAdmin, isInstructor, isStudentOnly} from "../authorization";
import People from "components/People";
import Person from "components/Person";
import Clients from "components/Clients";
import Client from "components/Client";
import Checkins from "components/Checkins";
import Checkin from "components/Checkin";
import QRCodes from "components/QRCodes";
import Reports from "components/Reports";
import Schedule from "components/Schedule";
import Settings from "components/Settings";
import Event from 'components/Event';
import Feedback from "components/Feedback";

export default {
    components: {
        Client, Clients, Event, People, Person, Checkins, Checkin, QRCodes, Reports, Schedule, Settings, Feedback},
    props: {
        source: String,
    },
    data: () => ({
        drawer: null,
        items: [],
        show: 'Reports',
        snackbar: {
            show: false,
            text: '',
        },
        speedDial: false,
    }),
    computed: {
        user() {
            return this.$store.state.user;
        },
    },
    methods: {
        isSuperAdmin,
        isAdmin,
        isInstructor,
        isStudentOnly,
        onSaveClient() {
            if (this.$refs.clients) {
                this.$refs.clients.refresh();
            }
        },
        onEditClient(client) {
            if (this.$refs.client && isSuperAdmin(this.user)) {
                this.$refs.client.client = Object.assign({}, client);
                this.$refs.client.show = true;
            }
        },
        onSavePerson() {
            if (this.$refs.people && (isSuperAdmin(this.user) || isAdmin(this.user))) {
                this.$refs.people.refresh();
            }
        },
        onEditPerson(person) {
            if (this.$refs.person && (isSuperAdmin(this.user) || isAdmin(this.user))) {
                this.$refs.person.person = Object.assign({}, person);
                this.$refs.person.show = true;
            }
        },
        onSaveCheckin(snackbarText) {
            this.snackbar.text = snackbarText;
            this.snackbar.show = true;
            if (this.$refs.checkins) {
                this.$refs.checkins.refresh();
            }
            this.$refs.people.refresh();
        },
        onEditCheckin(checkin) {
            if (this.$refs.checkin && (! isStudentOnly(this.user))) {

                this.$refs.checkin.setCheckin(checkin);
            }
        },
        onEditEvent(event) {
            if (this.$refs.event && (isSuperAdmin(this.user) || isAdmin(this.user))) {

                this.$refs.event.setEvent(event);
            }
        },
        onSaveEvent(verb) {
            this.snackbar.text = `The class has been ${verb}.`;
            this.snackbar.show = true;
            if (this.$refs.schedule) {
                this.$refs.schedule.refresh();
            }
        },
    },
    created() {
        this.items = [
            { icon: 'mdi-account-cash', text: 'Clients', allowed: this.isSuperAdmin(this.user) },
            { icon: 'mdi-contacts', text: 'People', allowed: this.isSuperAdmin(this.user) || this.isAdmin(this.user) },
            { icon: 'mdi-calendar-month-outline', text: 'Schedule', allowed: true },
            { icon: 'mdi-history', text: 'Check-ins', allowed: true },
            { icon: 'mdi-qrcode', text: 'QRCodes', allowed: true },
            { icon: 'mdi-file-chart', text: 'Reports', allowed: true },
            { icon: 'mdi-settings', text: 'Settings', allowed: this.isSuperAdmin(this.user) || this.isAdmin(this.user) },
            {
                icon: 'mdi-message',
                text: 'Send feedback',
                allowed:  this.isSuperAdmin(this.user) || this.isAdmin(this.user) || this.isInstructor(this.user),
            },
            {
                icon: 'mdi-chevron-up',
                'icon-alt': 'mdi-chevron-down',
                text: 'More',
                allowed: this.isSuperAdmin(this.user) || this.isAdmin(this.user),
                model: false,
                children: [
                    { text: 'Import' },
                    { text: 'Export' },
                ],
            },
            { icon: 'mdi-help-circle', text: 'Help', allowed: true },
        ];
    },
    mounted() {
        document.addEventListener('keypress', this.$refs.qrcodes.onKeypress);
    }
}
</script>

<style>
    td {
        color: #d3d3d3;
    }
</style>
