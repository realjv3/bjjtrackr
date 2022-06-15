<template>
    <div>
        <v-card>
            <v-card-title>

                <div class="d-flex flex-column">
                    <div class="mb-3">Products</div>
                    <v-btn x-small @click="show.productModal = true">+Add product</v-btn>
                </div>

                <v-spacer></v-spacer>

                <v-text-field
                    v-model="searchProducts"
                    append-icon="search"
                    label="Search"
                    single-line
                    hide-details
                ></v-text-field>

            </v-card-title>

            <v-data-table
                :headers="productHeaders"
                :items="products"
                :items-per-page="10"
                class="elevation-1"
                :loading="loading"
                :search="searchProducts"
            >
                <template v-slot:item.prices="{ item }">
                    <div class="d-flex flex-column">
                        <span v-for="price in item.prices" v-if="price.active">
                            {{ price.amount }}
                        </span>
                    </div>
                </template>

                <template v-slot:item.active="{ item }">
                    <span v-if="item.active">active</span>
                    <span v-else>archived</span>
                </template>

                <template v-slot:item.action="{ item }">
                    <v-icon small class="mr-2" @click="editProduct(item)">edit</v-icon>
                </template>

            </v-data-table>
        </v-card>

        <v-dialog v-model="show.productModal" :persistent="true" max-width="400">
            <v-card>
                <v-card-title class="grey darken-2" v-text="`${editing ? 'Edit' : 'Add'} Product`"/>

                <v-container fluid>
                    <v-form v-model="valid">

                        <v-row class="mx-2">
                            <v-col>
                                <v-text-field
                                    v-model="product.name"
                                    label="Product Name"
                                    required
                                    :rules="rules.required"
                                />
                            </v-col>
                        </v-row>

                        <v-row class="mx-2">
                            <v-col cols="6">
                                <v-text-field v-model="product.unit" label="Unit (optional)"/>
                            </v-col>
                        </v-row>

                        <v-col cols="2">
                            <v-checkbox
                                v-if="product.hasOwnProperty('active')"
                                v-model="product.active"
                                label="Active"
                            />
                        </v-col>

                        <v-divider class="mx-3"/>

                        <v-btn v-if="editing" x-small @click="addPrice" class="ma-2">+Add price</v-btn>

                        <v-expansion-panels v-if="product.prices.length > 1">
                            <v-expansion-panel v-for="(price,i) in product.prices" :key="i">

                                <v-expansion-panel-header v-if="!price.amount">New Price</v-expansion-panel-header>
                                <v-expansion-panel-header v-else>
                                    {{ price.amount }}{{product.unit ? ` per ${product.unit}` : ''}}
                                </v-expansion-panel-header>

                                <v-expansion-panel-content>

                                    <v-row>
                                        <v-col cols="4">
                                            <v-text-field
                                                v-model="price.amount"
                                                label="Amount"
                                                required
                                                :rules="rules.price"
                                                :disabled="price.id && editing"
                                            />
                                        </v-col>

                                        <v-col cols="2">
                                            <v-checkbox
                                                v-if="price.hasOwnProperty('active')"
                                                v-model="price.active"
                                                label="Active"
                                            />
                                        </v-col>
                                    </v-row>

                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>

                        <v-row v-else v-for="(price, i) in product.prices" class="mx-2 mt-2" :key="i">

                            <v-row class="mx-1">
                                <v-col cols="4">
                                    <v-text-field
                                        v-model="price.amount"
                                        label="Amount"
                                        required
                                        :rules="! editing && rules.price"
                                        :disabled="price.id && editing"
                                    />
                                </v-col>

                                <v-col cols="2">
                                    <v-checkbox
                                        v-if="price.hasOwnProperty('active')"
                                        v-model="price.active"
                                        label="Active"
                                    />
                                </v-col>
                            </v-row>

                        </v-row>

                        <v-row justify="end">
                            <v-card-actions>
                                <v-btn text @click="handleSaveProduct" :loading="loading" :disabled="!valid">
                                    Save
                                </v-btn>
                                <v-btn text color="primary" @click="reset">Cancel</v-btn>
                            </v-card-actions>
                        </v-row>
                    </v-form>
                </v-container>
            </v-card>
        </v-dialog>
    </div>
</template>
<script>

import {headers} from "../../authorization";

function Product() {
    this.name = null;
    this.unit = null;
    this.prices = [new Price()];
}

function Price() {
    this.amount = null;
}

export default {
    name: 'Products',
    props: {
        products: {type: Array, required: true},
    },
    data: () => ({
        editing: false,
        errors: {},
        loading: false,
        product: new Product(),
        productHeaders: [
            { text: 'Name', align: 'left', value: 'name' },
            { text: 'Price', value: 'prices'},
            { text: 'Active', value: 'active' },
            { text: 'Edit', value: 'action', sortable: false },
        ],
        rules: {
            required: [v => !!v || 'Field is required'],
            number: [v => /\d+/.test(v) || 'Number is required'],
            price: [v => /^\d+(\.\d{1,2})?$/.test(v) || 'Price must be valid'],
        },
        searchProducts: '',
        show: {
            productModal: false,
        },
        valid: false,
    }),
    computed: {
        client() {
            return this.$store.state.clients.find(client => client.id === this.user.client_id);
        },
        productOptions() {
            return this.products
                .filter(m => m.active)
                .map(m => ({text: m.name, value: m.id}));
        },
        priceOptions() {
            const product = this.products.find(m => m.id === this.sale.product_id);
            return product.prices
                .filter(p => p.active)
                .map(p => ({text: `${p.amount}`, value: p.id}));
        },
        user() {
            return this.$store.state.user;
        },
    },
    methods: {
        addProduct() {
            this.loading = true;
            this.errors = {};
            fetch(`/product/${this.client.id}`, {
                method: 'POST',
                headers,
                body: JSON.stringify(this.product),
            })
                .then( resp => {
                    if (resp.ok) {
                        this.refresh();
                        this.reset();
                    }
                    return resp.json();
                })
                .then(json => {
                    if (json.errors) {
                        this.errors = json.errors;
                    }
                })
                .finally(() => this.loading = false);
        },
        addPrice() {
            this.product.prices.push(new Price());
        },
        editProduct(product) {
            this.product = {...product};
            this.editing = true;
            this.show.productModal = true;
        },
        handleSaveProduct() {
            this.editing ? this.updateProduct() : this.addProduct()
        },
        async refresh() {
            this.loading = true;
            await this.$store.dispatch('getProducts');
            this.loading = false;
        },
        reset() {
            this.show = {productModal: false};
            this.editing = false;
            this.product = new Product();
            this.errors = {};
        },
        updateProduct() {
            this.loading = true;
            this.errors = {};
            fetch(`/product/${this.client.id}/${this.product.id}`, {
                method: 'PATCH',
                headers,
                body: JSON.stringify(this.product),
            })
                .then( resp => {
                    if (resp.ok) {
                        this.refresh();
                        this.reset();
                    }
                    return resp.json();
                })
                .then(json => {
                    if (json.errors) {
                        this.errors = json.errors;
                    }
                })
                .finally(() => this.loading = false);
        },
    },
    created() {
        this.refresh();
    },
}
</script>
