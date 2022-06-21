<template>
    <v-lazy>
        <v-row justify="center" class="ma-lg-16">
            <v-card id="memberships-how-to">
                <v-img
                    class="white--text align-start"
                    height="726px"
                    src="https://flowrolled.nyc3.digitaloceanspaces.com/public/cash-register.jpeg"
                >
                    <v-card-title>How to manage products and sales</v-card-title>
                </v-img>

                <v-card-subtitle class="pb-0">
                    Create product:
                </v-card-subtitle>

                <v-card-text class="text--primary">

                    <v-timeline>

                        <v-timeline-item icon="mdi-numeric-1">
                            Hit the
                            <v-img
                                class="d-inline-block mx-2"
                                width="160px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/menuItemSales.png"
                                alt="Sales Menu Item"
                            />
                            menu item in the left side navigation menu to see your products.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-2">
                            Hit the <i>Add Product</i> button to create a new product to sell.
                            <v-img
                                class="d-inline-block mx-2"
                                width="161px"
                                height="53px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/buttonAddProduct.png"
                                alt="buttonAddProduct"
                            />
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-3">
                            Fill out the form with a name, optional unit (e.g. <i>12oz. bottle</i>) and price for the product, then hit save.
                            <v-img
                                class="ma-2"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/modalProductNew.png"
                                alt="New Product"
                            /><br/>
                            The newly created product will appear in the table. Hit the pencil icon to edit it.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-4">
                            Products and their prices can be set to inactive. Products can have more than one price. The price can be
                            chosen at the point of sale.
                            <v-img
                                class="ma-2"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/modalProductEdit.png"
                                alt="Edit Product"
                            />
                        </v-timeline-item>

                    </v-timeline>

                </v-card-text>

                <v-divider></v-divider>

                <v-card-subtitle class="pb-0">
                    To make a sale:
                </v-card-subtitle>

                <v-card-text class="text--primary">

                    <v-timeline>

                        <v-timeline-item icon="mdi-numeric-1">
                            Hit the
                            <v-img
                                class="d-inline-block mx-2"
                                width="160px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/menuItemSales.png"
                                alt="Sales"
                            />
                            menu item in the left side navigation menu to see your sales.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-2">
                            Hit the <i>Make Sale</i> button
                            <v-img
                                class="d-inline-block mx-2"
                                width="117px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/buttonAddSale.png"
                                alt="buttonAddMember"
                            />
                            to begin a new sale.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-3">
                            Fill out the form and hit <i>Make Sale</i>.
                            <v-img
                                class="ma-2"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/modalSaleNew.png"
                                alt="Membership Plan"
                            /><br/>
                            You can sell to a member or non-member. When a member is selected, their payment methods will
                            be listed. You can add payment methods for members as well. If selling to a non-member, the
                            card information must be input for each sale.
                        </v-timeline-item>

                    </v-timeline>

                </v-card-text>

                <v-divider></v-divider>

                <v-card-subtitle class="pb-0">
                    To refund a sale:
                </v-card-subtitle>

                <v-card-text class="text--primary">

                    <v-timeline>

                        <v-timeline-item icon="mdi-numeric-1">
                            Hit the
                            <v-img
                                class="d-inline-block mx-2"
                                width="160px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/menuItemSales.png"
                                alt="Sales"
                            />
                            menu item in the left side navigation menu to see your sales.
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-2">
                            Find the sale you want to refund in the Sales table and hit the <i>Refund</i> icon button
                            to begin refunding the sale. <br/>
                            <v-img
                                class="d-inline-block mx-2"
                                width="117px"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/buttonNewRefund.png"
                                alt="buttonAddMember"
                            />
                        </v-timeline-item>

                        <v-timeline-item icon="mdi-numeric-3">
                            Input the amount that you want to refund and hit the <i>Issue Refund</i> button.
                            <v-img
                                class="ma-2"
                                src="https://flowrolled.nyc3.digitaloceanspaces.com/public/modalAddRefund.png"
                                alt="Membership Plan"
                            /><br/>
                            The customer's card will be credited the refunded amount in 5 to 10 business days.
                        </v-timeline-item>

                    </v-timeline>

                </v-card-text>

            </v-card>
        </v-row>
    </v-lazy>
</template>

<script>
export default {
    name: 'HelpSales',
    components: {}

}
</script>
