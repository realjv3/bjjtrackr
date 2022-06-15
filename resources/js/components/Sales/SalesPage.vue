<template>
    <StripeOnboarding v-if=" ! client.charges_enabled" />

    <ProductSales v-else />

    <v-row v-else justify="center">
        <v-card>
            <v-card-title>Sales</v-card-title>
            <v-card-subtitle>Sell products</v-card-subtitle>
        </v-card>
    </v-row>
</template>

<script>
import ProductSales from "components/Sales/ProductSales";
import StripeOnboarding from "components/StripeOnboarding";

export default {
    name: "SalesPage",
    components: {StripeOnboarding, ProductSales},
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

