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
                                :error="error"
                                :error-messages="errorMsg"
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
        error: false,
        errorMsg: null,
        message: '',
        submitting: false,
        snackbar: {
            show: false,
            text: 'Thanks for your feedback!',
        }
    }),
    methods: {
        async clickSubmit() {

            this.submitting = true;
            this.error = false;
            this.errorMsg = null;

            const resp = await fetch(`/feedback`, {
                method: 'POST',
                headers,
                credentials: "same-origin",
                body: JSON.stringify({message: this.message}),
            });

            if (resp.status < 400) {
                this.submitting = false;
                this.message = '';
                this.snackbar.show = true;
                this.error = null;
            } else {
                const json = await resp.json();
                this.error = true;
                this.errorMsg = json.message;
                this.submitting = false;
                this.message = '';
            }
        },
    },
}
</script>
