<template>
    <StripeOnboarding v-if=" ! client.charges_enabled" />

    <Memberships v-else />

    <v-row v-else justify="center">
        <v-card>
            <v-card-title>Memberships</v-card-title>
            <v-card-subtitle>Manage your students' membership plans and payments</v-card-subtitle>
        </v-card>
    </v-row>
</template>

<script>
import Memberships from "components/Memberships";
import StripeOnboarding from "components/StripeOnboarding";

export default {
    name: "MembershipsPage",
    components: {StripeOnboarding, Memberships},
    computed: {
        client() {
            return this.$store.state.clients.find(client => client.id === this.user.client_id);
        },
        user() {
            return this.$store.state.user;
        },
    },
}
</script>

