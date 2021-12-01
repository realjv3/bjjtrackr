<template>
    <v-app>
        <v-main>
            <v-stepper v-model="step">
                <v-stepper-header>
                    <v-stepper-step :complete="step > 1" step="1">Welcome</v-stepper-step>

                    <v-divider></v-divider>

                    <v-stepper-step step="2" :complete="step > 2">Privacy Policy</v-stepper-step>

                    <v-divider></v-divider>

                    <v-stepper-step step="3" :complete="step > 3">Personal & Academy Info</v-stepper-step>

                    <v-divider></v-divider>

                    <v-stepper-step step="4" :complete="step > 4">Terms of Service</v-stepper-step>

                    <v-divider></v-divider>

                    <v-stepper-step step="5">Payment Method</v-stepper-step>
                </v-stepper-header>

                <v-stepper-items>
                    <v-stepper-content step="1">
                        <v-card id="carousel">
                            <v-card-title class="grey darken-2 mb-2">
                                Welcome to FlowRolled - jiu jitsu academy management software-as-a-service
                            </v-card-title>

                            <p class="ma-12">FlowRolled provides operators and instructors of Brazilian Jiu Jitsu academies a central, convenient place to manage students, schedules, documents, attendance, and belt promotions.</p>

                            <h4 class="ma-12">What FlowRolled can do for you:</h4>

                            <v-carousel height="420" show-arrows-on-hover>
                                <v-carousel-item key="1">
                                    <v-img class="mt-6 mx-auto" src="https://flowrolled.nyc3.digitaloceanspaces.com/public/BjjTrackr-Reports.png" width="520" />
                                    <p class="text-center ma-8">See all of your members' time and attendance reports organized in one place.</p>
                                </v-carousel-item>

                                <v-carousel-item key="2">
                                    <v-row>
                                        <v-col class="pa-10" cols="6">
                                            Set custom promotion eligibility criteria by belt rank, e.g. set how many training days or classes it takes to be considered for the next stripe or belt.
                                        </v-col>
                                        <v-col class="">
                                            <v-img src="https://flowrolled.nyc3.digitaloceanspaces.com/public/BjjTrackr-Settings.png" width="375"/>
                                        </v-col>
                                    </v-row>
                                </v-carousel-item>

                                <v-carousel-item key="3">
                                    <v-row>
                                        <v-col class="pl-16" cols="4">
                                            <v-img class="mt-6 mx-auto" src="https://flowrolled.nyc3.digitaloceanspaces.com/public/phone-doc.jpeg" width="164"/>
                                        </v-col>
                                        <v-col class="text-center pa-16">
                                            Digitally send, sign, and store waivers or any other documents. You send docs to their email or phone, they sign, it's that simple. Documents are stored securely and can be downloaded anytime.
                                        </v-col>
                                    </v-row>
                                </v-carousel-item>

                                <v-carousel-item key="4">
                                    <v-img class="mt-6 mx-auto" src="https://flowrolled.nyc3.digitaloceanspaces.com/public/BjjTrackr-QR.png" width="520"/>
                                    <p class="text-center ma-8">Efficient QR code scans or keyboard input for class checkins.</p>
                                </v-carousel-item>

                                <v-carousel-item key="5">
                                    <v-img class="mt-6 mx-auto" src="https://flowrolled.nyc3.digitaloceanspaces.com/public/BjjTrackr-Schedule.png" width="520"/>
                                    <p class="text-center ma-8">Set and publish your class schedule.</p>
                                </v-carousel-item>

                                <v-carousel-item key="6">
                                    <p class="text-center ma-8">
                                        <v-icon class="text-h1" color="secondary">mdi-party-popper</v-icon>
                                    </p>
                                    <p class="text-center ma-16">
                                        Increase student retention and motivation with regular stripe and belt promotions, enabled by detailed attendance tracking reports.
                                    </p>
                                </v-carousel-item>

                                <v-carousel-item key="7">
                                    <p class="text-center ma-8">
                                        <v-icon class="text-h1" color="secondary">mdi-account-heart</v-icon>
                                    </p>
                                    <p class="text-center ma-16">
                                        Reduce member churn with automated contact reminders when a student has been absent for a while.
                                    </p>
                                </v-carousel-item>

                                <v-carousel-item key="8">
                                    <p class="text-center mt-5 mx-auto">
                                        <v-icon class="text-h1" color="secondary">mdi-account-cash</v-icon>
                                    </p>
                                    <p class="text-center ma-8">On the roadmap - Manage memberships and payments</p>
                                </v-carousel-item>

                                <v-carousel-item key="9">
                                    <p class="text-center mt-5 mx-auto">
                                        <v-icon class="text-h1" color="secondary">mdi-currency-usd</v-icon>
                                    </p>
                                    <p class="text-center ma-16">Pay only for what you use. The price is $3 per active student per month. Cancel anytime.</p>
                                </v-carousel-item>
                            </v-carousel>

                            <v-row justify="end" class="mx-2">
                                <v-card-actions>
                                    <v-btn text href="/">Back</v-btn>
                                    <v-btn color="primary" @click="step = 2">Continue</v-btn>
                                </v-card-actions>
                            </v-row>

                        </v-card>

                    </v-stepper-content>

                    <v-stepper-content step="2">
                        <PrivacyPolicy />
                        <v-row justify="end" class="mx-2">
                            <v-card-actions>
                                <v-btn text @click="step = 1">Back</v-btn>
                                <v-btn color="primary" @click="step = 3">Continue</v-btn>
                            </v-card-actions>
                        </v-row>
                    </v-stepper-content>

                    <v-stepper-content step="3">
                        <v-card class="ma-16" >
                            <v-card-title class="grey darken-2 mb-2">Personal & Academy Info</v-card-title>
                            <div class="text-subtitle-1 ma-4">
                                Please tell us a little bit about yourself and your academy.
                            </div>
                            <v-row class="mx-2">
                                <v-col cols="6">
                                    <v-text-field
                                        v-model="person.name"
                                        :error-messages="errors['person.name']"
                                        placeholder="required"
                                        label="Name"
                                        :disabled="saving"
                                    />
                                </v-col>

                                <v-col>
                                    <v-text-field
                                        v-model="person.email"
                                        :error-messages="errors['person.email']"
                                        type="email"
                                        placeholder="required"
                                        label="Email"
                                        :disabled="saving"
                                    />
                                </v-col>
                            </v-row>
                            <v-row class="mx-2">
                                <v-col cols="6">
                                    <v-text-field
                                        v-model="person.password"
                                        :error-messages="errors['person.password']"
                                        type="password"
                                        :placeholder="editing ? '••••••••' : 'required'"
                                        label="Password"
                                        :disabled="saving"
                                    />
                                </v-col>
                                <v-col cols="6">
                                    <v-text-field
                                        v-model="person.password_confirmation"
                                        :error-messages="errors['person.password_confirmation']"
                                        type="password"
                                        :placeholder="editing ? '••••••••' : 'required'"
                                        label="Confirm Password"
                                        :disabled="saving"
                                    />
                                </v-col>
                            </v-row>
                            <v-row class="mx-2">
                                <v-col cols="4">
                                    <v-select
                                        v-model="person.rank.belt_id"
                                        :items="[
                                            {value: 1, text: 'White'},
                                            {value: 2, text: 'Blue'},
                                            {value: 3, text: 'Purple'},
                                            {value: 4, text: 'Brown'},
                                            {value: 5, text: 'Black'},
                                        ]"
                                        label="Belt"
                                        value="White"
                                        :error-messages="errors['person.belt_id']"
                                        :disabled="saving"
                                    />
                                </v-col>
                                <v-col cols="2">
                                    <v-select
                                        v-model="person.rank.stripes"
                                        :items="[0, 1, 2, 3, 4]"
                                        label="Stripes"
                                        value="0"
                                        :error-messages="errors['person.stripes']"
                                        :disabled="saving"
                                    />
                                </v-col>
                                <v-col cols="4">
                                    <v-text-field
                                        label="Last ranked up"
                                        :value="person.rank.last_ranked_up"
                                        @click="show.pickRankedDate = true"
                                        prepend-inner-icon="mdi-calendar-month-outline"
                                        :error-messages="errors['person.rank.last_ranked_up']"
                                        :disabled="saving"
                                    />
                                </v-col>

                                <v-dialog v-model="show.pickRankedDate" class="mx-auto" width="290px">
                                    <v-date-picker v-model="person.rank.last_ranked_up" />
                                </v-dialog>
                            </v-row>

                            <v-row class="mx-2">
                                <v-col cols="5">
                                    <v-text-field
                                        v-model="client.name"
                                        :error-messages="errors['client.name']"
                                        placeholder="Academy name (required)"
                                        :disabled="saving"
                                    />
                                </v-col>

                                <v-col cols="2">
                                    <v-text-field
                                        label="Member since"
                                        :value="person.start_date"
                                        @click="show.pickStartDate = true"
                                        prepend-inner-icon="mdi-calendar-month-outline"
                                        :error-messages="errors['person.start_date']"
                                        :disabled="saving"
                                    />
                                </v-col>

                                <v-dialog v-model="show.pickStartDate" class="mx-auto" width="290px">
                                    <v-date-picker v-model="person.start_date" :disabled="saving"/>
                                </v-dialog>

                                <v-col cols="5">
                                    <v-text-field
                                        v-model="client.affiliation"
                                        placeholder="Affiliation"
                                        :disabled="saving"
                                    />
                                </v-col>

                                <v-col cols="12">
                                    <v-text-field v-model="client.address1" placeholder="Address 1" :disabled="saving"/>
                                </v-col>

                                <v-col cols="12">
                                    <v-text-field v-model="client.address2" placeholder="Address 2" :disabled="saving"/>
                                </v-col>

                                <v-col cols="6">
                                    <v-text-field v-model="client.city" placeholder="City" :disabled="saving" />
                                </v-col>

                                <v-col cols="4">
                                    <v-text-field v-model="client.state" placeholder="State/Province" :disabled="saving"/>
                                </v-col>

                                <v-col cols="2">
                                    <v-text-field v-model="client.zip" placeholder="Postal code" :disabled="saving"/>
                                </v-col>

                                <v-col cols="6">
                                    <v-text-field v-model="client.country" placeholder="Country" :disabled="saving"/>
                                </v-col>
                            </v-row>

                            <v-row justify="end" class="mx-2">
                                <v-card-actions>
                                    <v-btn text @click="step = 2" :loading="saving">Back</v-btn>
                                    <v-btn color="primary" @click="savePersonClient" :loading="saving">Continue</v-btn>
                                </v-card-actions>
                            </v-row>
                        </v-card>

                    </v-stepper-content>

                    <v-stepper-content step="5">
                        <v-card class="ma-16" height="74vh">
                            <v-card-title class="grey darken-2 mb-2">Payment Method</v-card-title>
                            <v-row class="ma-6">
                                <div class="text-body-1">
                                    Please input your card info.<br/>
                                    After a 30 day free trial, the monthly subscription fee will be charged to the card. The price will be calculated as $3 per active student. Cancel anytime.
                                </div>
                            </v-row>
                            <v-row class="ma-6">
                                <v-col cols="5">
                                    <PaymentMethods @created-payment-method="redirectHome" />
                                </v-col>
                            </v-row>
                        </v-card>
                    </v-stepper-content>
                </v-stepper-items>
            </v-stepper>
        </v-main>
    </v-app>
</template>

<script>

import {headers} from "../authorization";
import PaymentMethods from "components/PaymentMethods";
import Fetches from "../fetches";
import PrivacyPolicy from "components/PrivacyPolicy";

const fetches = new Fetches();

export default {
    name: "Signup",
    components: {PaymentMethods, PrivacyPolicy},
    data: function () {
        return {
            client: {
                name: null,
                affiliation: null,
                address1: null,
                address2: null,
                city: null,
                state: null,
                zip: null,
                country: null,
            },
            card: null,
            errors: {},
            person: {
                id: null,
                name: null,
                email: null,
                rank: {
                    belt_id: 1,
                    stripes: 0,
                    last_ranked_up: null,
                },
                password: null,
                password_confirmation: null,
                start_date: null,
            },
            saving: false,
            show: {
                pickRankedDate: false,
                pickStartDate: false,
            },
            step: 1,
        };
    },
    computed: {
        editing() {
            return this.person.id;
        },
        user() {
            if (this.$store) {
                return this.$store.state.user;
            }
        },
    },
    watch: {
        step: function(step) {
            if (step === 2) {
                window.scrollTo(0, 0);
            }
        },
    },
    methods: {
        redirectHome() {
            window.location = '/';
        },
        async savePersonClient() {
            this.saving = true;
            fetches.cancelFetches();
            this.errors = {};
            const
                resp = await fetch(`/signup`, {
                method: 'POST',
                headers,
                credentials: "same-origin",
                signal: fetches.getSignal(),
                body: JSON.stringify({client: this.client, person: this.person}),
            }),
                json = await resp.json();

            if (resp.status < 211) {
                window.location = '/tos';
            } else if (resp.status === 422) {
                this.errors = json.errors;
            }
            this.saving = false;
        },
    },
    created() {
        if (window.location.pathname === '/paymentmethod') {
            this.step = 5;
        }

        if (this.user) {
            this.person = this.user;
            this.client = this.$store.state.clients.find(client => client.id === this.user.client_id);
        }
    },
}
</script>

<style scoped>
#carousel {
    width: 75%;
    margin: 50px auto;
    padding: 100px;
}
</style>
