<template>
    <v-app>
        <v-navigation-drawer
            v-model="drawer"
            :clipped="$vuetify.breakpoint.lgAndUp"
            app
        >
            <v-list>
                <template v-if="initializing">
                    <v-skeleton-loader
                        v-for="i in Array(10).fill(0)"
                        type="list-item-avatar"
                        :key="i"
                    />
                </template>

                <template v-else v-for="item in items">

                    <v-list-group
                        v-if="item.allowed && item.children"
                        :key="item.text"
                        v-model="item.model"
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

                    <v-list-item :id="item.text.toLowerCase()" v-else-if="item.allowed" :key="item.text" link @click="show = item.text">
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
                <span class="hidden-sm-and-down">FlowRolled</span>
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
            <v-skeleton-loader v-if="initializing" type="card" class="ma-16"/>

            <v-container v-else class="fill-height" fluid>
                <v-row align="center" justify="center">

                    <People v-show="( ! isStudentOnly(user)) && show === 'People'" ref="people" @edit-person="onEditPerson" />

                    <Clients v-if="isSuperAdmin(user) && show === 'Clients'" ref="clients" @edit-client="onEditClient"/>

                    <Schedule v-show="show === 'Schedule'" ref="schedule" @edit-event="onEditEvent" />

                    <Checkins v-show="show === 'Check-ins'" ref="checkins" @edit-checkin="onEditCheckin" />

                    <QRCodes v-show="show === 'QRCodes'" @save-checkin="onEditCheckin" />

                    <Reports v-show="show === 'Reports'"/>

                    <Documents v-show="show === 'Documents'"/>

                    <Settings v-show="(isSuperAdmin(user) || isAdmin(user)) && show === 'Settings'" @edit-client="onEditClient"/>

                    <Feedback
                        v-show="(isSuperAdmin(user) || isAdmin(user) || isInstructor(user)) && show === 'Send feedback'"
                    />

                    <Help v-show="(isSuperAdmin(user) || isAdmin(user) || isInstructor(user)) && show === 'Help'" />
                </v-row>

                <Client v-show="isSuperAdmin(user)" ref="client"/>

                <Person v-show="isSuperAdmin(user) || isAdmin(user)" ref="person" @save-person="onSavePerson"/>

                <Event v-show="isSuperAdmin(user) || isAdmin(user)" ref="event" @save-event="onSaveEvent"/>

                <Checkin v-show="! isStudentOnly(user)" ref="checkin" @save-checkin="onSaveCheckin"/>

                <v-speed-dial
                    id="speed-dial"
                    v-if="! isStudentOnly(user)"
                    v-model="speedDial"
                    bottom right fixed
                >

                    <template v-slot:activator>
                        <v-btn v-model="speedDial" color="pink" dark fab>
                            <v-icon v-if="speedDial">mdi-close</v-icon>
                            <v-icon v-else>mdi-plus</v-icon>
                        </v-btn>
                    </template>

                    <v-tooltip ref="addCheckin" v-if="! isStudentOnly(user)" left>
                        <template v-slot:activator="{ on }">
                            <v-btn @click="$refs.checkin.setCheckin(null)" fab dark small color="primary" v-on="on">
                                <v-icon>mdi-history</v-icon>
                            </v-btn>
                        </template>
                        <span>Check-in</span>
                    </v-tooltip>

                    <v-tooltip ref="addClass" v-if="isAdmin(user) || isSuperAdmin(user)" left>
                        <template v-slot:activator="{ on }">
                            <v-btn @click="$refs.event.show.event = true" fab dark small color="primary" v-on="on">
                                <v-icon>mdi-calendar-month-outline</v-icon>
                            </v-btn>
                        </template>
                        <span>Class</span>
                    </v-tooltip>

                    <v-tooltip ref="addPerson" v-if="isAdmin(user) || isSuperAdmin(user)" left>
                        <template v-slot:activator="{ on }">
                            <v-btn @click="$refs.person.show = true" fab dark small color="primary" v-on="on">
                                <v-icon>mdi-contacts</v-icon>
                            </v-btn>
                        </template>
                        <span>Person</span>
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

        <Tour @step="handleTour"/>
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
import Help from "components/Help";
import QRCodes from "components/QRCodes";
import Reports from "components/Reports";
import Schedule from "components/Schedule";
import Settings from "components/Settings";
import Tour from 'components/Tour';
import Event from 'components/Event';
import Feedback from "components/Feedback";
import store from "../store";
import Documents from "./Documents";

export default {
    components: {
        Documents, Client, Clients, Event, Help, People, Person, Checkins, Checkin, QRCodes, Reports, Schedule,
        Settings, Feedback, Tour
    },
    data: () => ({
        drawer: null,
        items: [],
        show: 'Help',
        snackbar: {
            show: false,
            text: '',
        },
        speedDial: false,
    }),
    computed: {
        initializing() {
            return ! this.items.length;
        },
        user() {
            return this.$store.state.user;
        },
    },
    methods: {
        handleTour(event) {

            const
                showSpeedDial = async () => {

                    return await setTimeout(() => this.speedDial = true, 10);
                },
                qrNavItem = document.querySelector('#qrcodes'),
                reportsNavItem = document.querySelector('#reports');


            if ((event.step === 0 && event.dir === 'next') || (event.step === 2 && event.dir === 'prev')) {
                showSpeedDial()
                    .then(() =>setTimeout(() => this.$refs.addPerson.isActive = true, 10));

            } else if ((event.step === 1 && event.dir === 'next') || (event.step === 3 && event.dir === 'prev')) {
                showSpeedDial()
                    .then(() =>setTimeout(() => this.$refs.addClass.isActive = true, 10));

            } else if ((event.step === 2 && event.dir === 'next') || (event.step === 4 && event.dir === 'prev')) {
                showSpeedDial()
                    .then(() =>setTimeout(() => {
                        document.querySelector('.v-step').style.right = '80px';
                        this.$refs.addCheckin.isActive = true
                    }, 10));
            } else if ((event.step === 3 && event.dir === 'next') || (event.step === 5 && event.dir === 'prev')) {

                setTimeout(() => {
                    this.drawer = true;
                    reportsNavItem.style.background = '#363636';
                    qrNavItem.style.background = '#666';
                    qrNavItem.click();
                }, 10);

            } else if ((event.step === 4 && event.dir === 'next') || (event.step === 6 && event.dir === 'prev')) {

                setTimeout(() => {
                    this.drawer = true;
                    qrNavItem.style.background = '#363636';
                    reportsNavItem.style.background = '#666';
                    reportsNavItem.click();
                }, 10);
            }
        },
        isSuperAdmin,
        isAdmin,
        isInstructor,
        isStudentOnly,
        onEditClient(client) {
            if (this.$refs.client && (isSuperAdmin(this.user) || (isAdmin(this.user) && client.id === this.user.client_id))) {
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
        Promise.all([
            store.dispatch('getClients'),
            store.dispatch('getPeople'),
            store.dispatch('getUser'),
        ])
            .then(() => {
                this.items = [
                    {icon: 'mdi-account-cash', text: 'Clients', allowed: this.isSuperAdmin(this.user)},
                    {
                        icon: 'mdi-contacts',
                        text: 'People',
                        allowed: this.isSuperAdmin(this.user) || this.isAdmin(this.user)
                    },
                    {icon: 'mdi-calendar-month-outline', text: 'Schedule', allowed: true},
                    {icon: 'mdi-history', text: 'Check-ins', allowed: true},
                    {icon: 'mdi-qrcode', text: 'QRCodes', allowed: true},
                    {icon: 'mdi-file-chart', text: 'Reports', allowed: true},
                    {icon: 'mdi-file-document-edit-outline', text: 'Documents', allowed: true},
                    {
                        icon: 'mdi-settings',
                        text: 'Settings',
                        allowed: this.isSuperAdmin(this.user) || this.isAdmin(this.user)
                    },
                    {
                        icon: 'mdi-message',
                        text: 'Send feedback',
                        allowed: this.isSuperAdmin(this.user) || this.isAdmin(this.user) || this.isInstructor(this.user),
                    },
                    {
                        text: 'More',
                        allowed: this.isSuperAdmin(this.user) || this.isAdmin(this.user),
                        model: false,
                        children: [
                            {text: 'Import'},
                            {text: 'Export'},
                        ],
                    },
                    {
                        icon: 'mdi-help-circle',
                        text: 'Help',
                        allowed: this.isSuperAdmin(this.user) || this.isAdmin(this.user) || this.isInstructor(this.user),
                    },
                ];
                if (!this.user.toured && isAdmin(this.user)) {
                    this.$tours.tour.start();
                }
            });
    },
}
</script>

<style>
    td {
        color: #d3d3d3;
    }
</style>
