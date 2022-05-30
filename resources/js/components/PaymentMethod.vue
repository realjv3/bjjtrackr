<template>
    <v-container fluid>
        <v-row align="center">
            <v-col style="min-width: 255px">
                <div id="card"></div>
                <div id="card-errors" role="alert">{{errors.card}}</div>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>

/**
 * Logic for payment method input for sales
 *
 * @emits payment-method - boolean
 */
export default {
    name: "PaymentMethod",
    data: function () {
        return {
            card: null,
            errors: {card: null},
            loading: false,
        }
    },
    methods: {
        changeCard({error, empty, complete}) {
            this.errors.card = error ? error.message : '';
            if ( ! empty && complete) {
                this.card.update({ disabled: true });
                this.$emit('payment-method', true);
            }
        },
    },
    async mounted() {
        // Mount the card inputs and listen for change
        const
            elements = this.$store.state.stripe.elements(),
            style = {
                base: {
                    color: "#FFF",
                    fontFamily: 'Roboto, sans-serif',
                    fontSmoothing: "antialiased",
                    fontSize: "16px",
                    "::placeholder": {
                        color: "#aab7c4"
                    }
                },
                invalid: {
                    color: "#ff5252",
                    iconColor: "#ff5252"
                },
            };
        this.card = elements.create('card', {style});
        this.card.mount('#card');
        this.card.on('change', this.changeCard);
    },
}
</script>

<style scoped>

#card-errors {
    color: #ff5252;
    margin-left: 31px;
}

</style>
