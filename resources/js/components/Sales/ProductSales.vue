<template>
    <v-container fluid>
        <v-row class="mb-10" justify="center">
            <Products :products="products" />
        </v-row>

        <v-row class="mb-10" justify="center">
            <Sales :products="products" />
        </v-row>
    </v-container>
</template>

<script>
import Products from "components/Sales/Products";
import Sales from "components/Sales/Sales";

export default {
    name: "ProductSales",
    components: {Sales, Products},
    computed: {
        products() {
            return this.$store.state.products.filter(product => product.active && !product.prices[0].recurring);
        },
    },
}
</script>
