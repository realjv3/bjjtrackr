<template>
    <v-app>
        <v-main>
            <v-stepper v-model="step">
                <v-stepper-header>
                    <v-stepper-step :complete="step > 1" step="1">Welcome</v-stepper-step>

                    <v-divider></v-divider>

                    <v-stepper-step step="2" :complete="step > 2">Privacy Policy</v-stepper-step>

                    <v-divider></v-divider>

                    <v-stepper-step :complete="step > 3" step="3">Personal & Academy Info</v-stepper-step>

                    <v-divider></v-divider>

                    <v-stepper-step step="4" :complete="step > 4">Payment Method</v-stepper-step>
                </v-stepper-header>

                <v-stepper-items>
                    <v-stepper-content step="1">
                        <v-card class="mb-12">
                            <v-card-title class="grey darken-2 mb-2">
                                Welcome to FlowRolled - a customer relationship manager for jiu jitsu academies
                            </v-card-title>

                            <p class="ml-4">As your grappling family and school grows in size, it can become increasingly unwieldy and complex to keep track of all of your students' progress.</p>
                            <p class="ml-4">FlowRolled's mission is to make tracking time, attendance, and eligibility for promotions easy for operators, instructors, and students of Brazilian Jiu Jitsu academies.</p>

                            <h4 class="mt-4 ml-4">What FlowRolled can do for you:</h4>

                            <v-carousel height="420" show-arrows-on-hover>
                                <v-carousel-item key="1">
                                    <v-img class="mt-6 mx-auto" src="/storage/img/BjjTrackr-Reports.png" width="520" />
                                    <p class="text-center mt-5 mx-auto">See all of your members' time and attendance reports organized in one place</p>
                                </v-carousel-item>

                                <v-carousel-item key="2">
                                    <v-img class="mt-6 mx-auto" src="/storage/img/BjjTrackr-Settings.png" width="255"/>
                                    <p class="text-center mt-5 mx-auto">Set custom promotion eligiblity criteria by belt rank, e.g. set how many training days or classes to be considered for the next stripe or belt</p>
                                </v-carousel-item>

                                <v-carousel-item key="3">
                                    <v-img class="mt-6 mx-auto" src="/storage/img/BjjTrackr-QR.png" width="520"/>
                                    <p class="text-center mt-5 mx-auto">Efficent QR code scans or keyboard input for class checkins</p>
                                </v-carousel-item>

                                <v-carousel-item key="4">
                                    <v-img class="mt-6 mx-auto" src="/storage/img/BjjTrackr-Schedule.png" width="520"/>
                                    <p class="text-center mt-5 mx-auto">Set and publish your class schedule</p>
                                </v-carousel-item>

                                <v-carousel-item key="5">
                                    <p class="text-center mt-5 mx-auto">
                                        <v-icon class="text-h1" color="secondary">mdi-party-popper</v-icon>
                                    </p>
                                    <p class="text-center mt-5 mx-auto">
                                        Increase student retention and motivation with regular promotions
                                    </p>
                                </v-carousel-item>

                                <v-carousel-item key="6">
                                    <p class="text-center mt-5 mx-auto">
                                        <v-icon class="text-h1" color="secondary">mdi-account-heart</v-icon>
                                    </p>
                                    <p class="text-center mt-5 mx-auto px-4">
                                        Reduce member churn with automated contact reminders when a student has been absent for a while
                                    </p>
                                </v-carousel-item>

                                <v-carousel-item key="7">
                                    <p class="text-center mt-5 mx-auto">
                                        <v-icon class="text-h1" color="secondary">mdi-file-document-edit</v-icon>
                                    </p>
                                    <p class="text-center mt-5 mx-auto">Coming Soon - Digitally sign and store waivers</p>
                                </v-carousel-item>

                                <v-carousel-item key="8">
                                    <p class="text-center mt-5 mx-auto">
                                        <v-icon class="text-h1" color="secondary">mdi-account-cash</v-icon>
                                    </p>
                                    <p class="text-center mt-5 mx-auto">On the roadmap - Manage memberships and payments</p>
                                </v-carousel-item>

                                <v-carousel-item key="9">
                                    <p class="text-center mt-5 mx-auto">
                                        <v-icon class="text-h1" color="secondary">mdi-currency-usd</v-icon>
                                    </p>
                                    <p class="text-center mt-5 mx-auto">Pay only for what you use. The price is $3 per active student per month. Cancel anytime.</p>
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
                        <v-card class="mb-12">
                            <v-card-title class="gray mb-2">Privacy Policy</v-card-title>
                            <v-card-text class="mb-12 pa-12">
                                <div class="text-body-2">
                                    Your privacy is important to us. It is Flowrolled's policy to respect your privacy and comply with any applicable law and regulation regarding any personal information we may collect about you, including across our website, https://flowrolled.com, and other sites we own and operate.
                                    <br/>
                                    This policy is effective as of 1 July 2021 and was last updated on 6 July 2021.
                                </div>
                                <br/>
                                <div class="text-h6">Information We Collect</div>
                                <br/>
                                <div class="text-body-2">
                                    Information we collect includes both information you knowingly and actively provide us when using or participating in any of our services and promotions, and any information automatically sent by your devices in the course of accessing our products and services.
                                </div>
                                <br/>
                                <div class="text-h6">Log Data</div>
                                <br/>
                                <div class="text-body-2">
                                    When you visit our website, our servers may automatically log the standard data provided by your web browser. It may include your device’s Internet Protocol (IP) address, your browser type and version, the pages you visit, the time and date of your visit, the time spent on each page, other details about your visit, and technical details that occur in conjunction with any errors you may encounter.
                                    <br/>
                                    Please be aware that while this information may not be personally identifying by itself, it may be possible to combine it with other data to personally identify individual persons.
                                </div>
                                <br/>
                                <div class="text-h6">Personal Information</div>
                                <br/>
                                <div class="text-body-2">
                                    We may ask for personal information which may include one or more of the following:
                                    <ul>
                                        <li>Name</li>
                                        <li>Email</li>
                                        <li>Phone/mobile number</li>
                                    </ul>
                                </div>
                                <br/>
                                <div class="text-h6">Legitimate Reasons for Processing Your Personal Information</div>
                                <br/>
                                <div class="text-body-2">
                                    We only collect and use your personal information when we have a legitimate reason for doing so. In which instance, we only collect personal information that is reasonably necessary to provide our services to you.
                                </div>
                                <br/>
                                <div class="text-h6">Collection and Use of Information</div>
                                <br/>
                                <div class="text-body-2">
                                    We may collect personal information from you when you do any of the following on our website:
                                    <ul>
                                        <li>Use a mobile device or web browser to access our content</li>
                                        <li>Contact us via email, social media, or on any similar technologies</li>
                                        <li>When you mention us on social media</li>
                                    </ul>
                                    <br/>
                                    We may collect, hold, use, and disclose information for the following purposes, and personal information will not be further processed in a manner that is incompatible with these purposes:
                                    <ul>
                                        <li>to enable you to customise or personalise your experience of our website</li>
                                        <li>to enable you to access and use our website, associated applications, and associated social media platforms</li>
                                    </ul>
                                    <br/>
                                    Please be aware that we may combine information we collect about you with general information or research data we receive from other trusted sources.
                                </div>
                                <br/>
                                <div class="text-h6">Security of Your Personal Information</div>
                                <br/>
                                <div class="text-body-2">
                                    When we collect and process personal information, and while we retain this information, we will protect it within commercially acceptable means to prevent loss and theft, as well as unauthorized access, disclosure, copying, use, or modification. Although we will do our best to protect the personal information you provide to us, we advise that no method of electronic transmission or storage is 100% secure, and no one can guarantee absolute data security. We will comply with laws applicable to us in respect of any data breach. You are responsible for selecting any password and its overall security strength, ensuring the security of your own information within the bounds of our services.
                                </div>
                                <br/>
                                <div class="text-h6">How Long We Keep Your Personal Information</div>
                                <br/>
                                <div class="text-body-2">
                                    We keep your personal information only for as long as we need to. This time period may depend on what we are using your information for, in accordance with this privacy policy. If your personal information is no longer required, we will delete it or make it anonymous by removing all details that identify you. However, if necessary, we may retain your personal information for our compliance with a legal, accounting, or reporting obligation or for archiving purposes in the public interest, scientific, or historical research purposes or statistical purposes.
                                </div>
                                <br/>
                                <div class="text-h6">Children’s Privacy</div>
                                <br/>
                                <div class="text-body-2">
                                    We do not aim any of our products or services directly at children under the age of 13, and we do not knowingly collect personal information about children under 13.
                                </div>
                                <br/>
                                <div class="text-h6">International Transfers of Personal Information</div>
                                <br/>
                                <div class="text-body-2">
                                    The personal information we collect is stored and/or processed where we or our partners, affiliates, and third-party providers maintain facilities. Please be aware that the locations to which we store, process, or transfer your personal information may not have the same data protection laws as the country in which you initially provided the information. If we transfer your personal information to third parties in other countries: (i) we will perform those transfers in accordance with the requirements of applicable law; and (ii) we will protect the transferred personal information in accordance with this privacy policy.
                                </div>
                                <br/>
                                <div class="text-h6">Your Rights and Controlling Your Personal Information</div>
                                <br/>
                                <div class="text-body-2">
                                    <p>You always retain the right to withhold personal information from us, with the understanding that your experience of our website may be affected. We will not discriminate against you for exercising any of your rights over your personal information. If you do provide us with personal information you understand that we will collect, hold, use and disclose it in accordance with this privacy policy. You retain the right to request details of any personal information we hold about you.</p>
                                    <p>If we receive personal information about you from a third party, we will protect it as set out in this privacy policy. If you are a third party providing personal information about somebody else, you represent and warrant that you have such person’s consent to provide the personal information to us.</p>
                                    <p>If you have previously agreed to us using your personal information for direct marketing purposes, you may change your mind at any time. We will provide you with the ability to unsubscribe from our email-database or opt out of communications. Please be aware we may need to request specific information from you to help us confirm your identity.</p>
                                    <p>If you believe that any information we hold about you is inaccurate, out of date, incomplete, irrelevant, or misleading, please contact us using the details provided in this privacy policy. We will take reasonable steps to correct any information found to be inaccurate, incomplete, misleading, or out of date.</p>
                                    <p>If you believe that we have breached a relevant data protection law and wish to make a complaint, please contact us using the in app feedback form and provide us with full details of the alleged breach. We will promptly investigate your complaint and respond to you, in writing, setting out the outcome of our investigation and the steps we will take to deal with your complaint. You also have the right to contact a regulatory body or data protection authority in relation to your complaint.</p>
                                </div>
                                <br/>
                                <div class="text-h6">Use of Cookies</div>
                                <br/>
                                <div class="text-body-2">
                                    <p>You always retain the right to withhold personal information from us, with the understanding that your experience of our website may be affected. We will not discriminate against you for exercising any of your rights over your personal information. If you do provide us with personal information you understand that we will collect, hold, use and disclose it in accordance with this privacy policy. You retain the right to request details of any personal information we hold about We use “cookies” to collect information about you and your activity across our site. A cookie is a small piece of data that our website stores on your computer, and accesses each time you visit, so we can understand how you use our site. This helps us serve you content based on preferences you have specified.</p>
                                </div>
                                <br/>
                                <div class="text-h6">Limits of Our Policy</div>
                                <br/>
                                <div class="text-body-2">
                                    <p>Our website may link to external sites that are not operated by us. Please be aware that we have no control over the content and policies of those sites, and cannot accept responsibility or liability for their respective privacy practices.</p>
                                </div>
                                <br/>
                                <div class="text-h6">Changes to This Policy</div>
                                <br/>
                                <div class="text-body-2">
                                    <p>At our discretion, we may change our privacy policy to reflect updates to our business processes, current acceptable practices, or legislative or regulatory changes. If we decide to change this privacy policy, we will post the changes here at the same link by which you are accessing this privacy policy.</p>
                                    <p>If required by law, we will get your permission or give you the opportunity to opt in to or opt out of, as applicable, any new uses of your personal information.</p>
                                </div>
                                <br/>
                                <div class="text-h6">Contact Us</div>
                                <br/>
                                <div class="text-body-2">
                                    <p>For any questions or concerns regarding your privacy, you may contact us using the in app feedback form.</p>
                                </div>
                            </v-card-text>

                            <v-row justify="end" class="mx-2">
                                <v-card-actions>
                                    <v-btn text @click="step = 1" :loading="saving">Back</v-btn>
                                    <v-btn color="primary" @click="step = 3">Continue</v-btn>
                                </v-card-actions>
                            </v-row>
                        </v-card>
                    </v-stepper-content>

                    <v-stepper-content step="3">
                        <v-card class="mb-12" >
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
                                    <v-btn text @click="step = 1" :loading="saving">Back</v-btn>
                                    <v-btn color="primary" @click="savePersonClient" :loading="saving">Continue</v-btn>
                                </v-card-actions>
                            </v-row>
                        </v-card>

                    </v-stepper-content>

                    <v-stepper-content step="4">
                        <v-card class="mb-12" height="74vh">
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
                            <v-row justify="end" class="mx-2">
                                <v-card-actions>
                                    <v-btn text @click="step = 2" :loading="saving">Back</v-btn>
                                </v-card-actions>
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

const fetches = new Fetches();

export default {
    name: "Signup",
    components: {PaymentMethods},
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
                window.location = '/paymentmethod';
            } else if (resp.status === 422) {
                this.errors = json.errors;
            }
            this.saving = false;
        },
    },
    created() {
        if (window.location.pathname === '/paymentmethod') {
            this.step = 4;
        }

        if (this.user) {
            this.person = this.user;
            this.client = this.$store.state.clients.find(client => client.id === this.user.client_id);
        }
    },
}
</script>
