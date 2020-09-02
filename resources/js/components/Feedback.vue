<template>
    <v-container fluid>
        <v-row justify="center">
            <v-card>
                <v-card-title>Feedback</v-card-title>
                <v-card-text>
                    <v-container style="width: 50vw">
                        <v-row class="mx-2">
                            <v-textarea
                                :full-width="true"
                                v-model="message"
                                :loading="submitting"
                                :readonly="submitting"
                                :outlined="true"
                            />
                        </v-row>
                        <v-row justify="end">
                            <v-card-actions>
                                <v-btn text @click="clickSubmit" :loading="submitting">Submit</v-btn>
                            </v-card-actions>
                        </v-row>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-row>
        <v-snackbar v-model="snackbar.show" :bottom="true">{{snackbar.text}}</v-snackbar>
    </v-container>
</template>

<script>
import {headers} from "../authorization";

export default {
    name: "Feedback",
    data: () => ({
        message: '',
        submitting: false,
        snackbar: {
            show: false,
            text: 'Thanks for your feedback!',
        }
    }),
    methods: {
        clickSubmit() {

            this.submitting = true;

            fetch(`/feedback`, {
                method: 'POST',
                headers,
                credentials: "same-origin",
                body: JSON.stringify({message: this.message}),
            })
                .then(() => {
                    this.submitting = false;
                    this.message = '';
                    this.snackbar.show = true;
                });
        },
    },
}
</script>
