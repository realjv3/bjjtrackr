<template>
    <v-container fluid fill-height>
        <v-row style="height:95.5vh">

            <v-col cols="5" v-if="this.$vuetify.breakpoint.name !== 'xs'">
                <v-img src="https://flowrolled.nyc3.digitaloceanspaces.com/public/High%20Resolution%20Logo.png"/>
                <div class="column1 d-flex flex-column">
                    <div class="mt-15 text-h4">Flowrolled partners with Stripe for payments.</div>
                    <div class="d-flex align-center align-self-start mt-auto">
                        <span>Powered by</span>
                        <a href="https://stripe.com/" target="_blank">
                            <v-img class="d-inline-block"
                                   src="https://flowrolled.nyc3.digitaloceanspaces.com/public/stripe.svg" width="119"/>
                        </a>
                    </div>
                </div>
            </v-col>

            <v-col class="column2 d-flex flex-column justify-center pa-6">
                <div :class="{'text-h2': !extraSmall, 'text-h4': extraSmall}">Setup payment processing for memberships
                    and sales
                </div>
                <div class="text-body-1 mt-6 mb-2 ml-2">
                    Millions of companies of all sizes use Stripe to accept payments, send payouts, and manage their
                    businesses online. Get onboarded with <a href="https://stripe.com" target="_blank">Stripe</a> for
                    free to start managing your students' subscriptions and payments.
                </div>
                <v-form
                    ref="form"
                    v-model="valid"
                    class="text--accent-1 my-3 ml-2"
                    :class="{'mr-16': !extraSmall, 'pr-10': !extraSmall}"
                >
                    <v-text-field
                        v-model="email"
                        label="Email"
                        background-color="indigo lighten-5"
                        light filled
                        :rules="emailRules"
                        :disabled="needsFinishOnboarding"
                        required
                    />
                    <v-btn block color="primary" :disabled="!valid" @click="submit">
                        <span v-if="!needsFinishOnboarding">Next</span>
                        <span v-else>Finish Stripe Onboarding</span>
                        <v-icon>mdi-arrow-right</v-icon>
                    </v-btn>
                </v-form>
            </v-col>
        </v-row>
    </v-container>
</template>
<script>
import {headers} from "../authorization";

export default {
    name: 'StripeOnboarding',
    data: () => ({
        email: null,
        emailRules: [
            v => !!v || 'E-mail is required',
            v => /.+@.+/.test(v) || 'E-mail must be valid',
        ],
        valid: true,
    }),
    computed: {
        extraSmall() {
            return this.$vuetify.breakpoint.name === 'xs';
        },
        chargesEnabled() {
            return !!this.client.charges_enabled;
        },
        client() {
            return this.$store.state.clients.find(client => client.id === this.user.client_id);
        },
        hasStripeAccount() {
            return !!this.client.stripe_account;
        },
        needsFinishOnboarding() {
            return this.client.stripe_account && !this.chargesEnabled;
        },
        user() {
            return this.$store.state.user;
        },
    },
    methods: {
        getAccountStatus() {
            fetch(`/stripeconnect/${this.client.id}/account`)
                .then(resp => resp.json());
        },
        submit() {
            this.$refs.form.validate();
            this.valid && fetch(`/stripeconnect/${this.client.id}`, {
                method: 'POST',
                headers,
                credentials: "same-origin",
                body: JSON.stringify({email: this.email}),
            })
                .then(resp => resp.json())
                .then(json => window.location = json.url);
        },
    },
    created() {
        this.email = this.user.email;

        if (this.hasStripeAccount) {
            this.getAccountStatus();
        }
    },
}
</script>
<style scoped>
.column1 {
    height: 75%;
}

.column2 {
    color: #000000;
    background-color: #FFF;
    height: 100%;
}
</style>
