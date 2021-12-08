<template>
    <v-container fluid>
        <v-row justify="center">
            <v-card class="col-lg-6">
                <v-card-title>Feedback</v-card-title>

                <v-card-subtitle>Drop us a line and we'll respond as soon as possbile.</v-card-subtitle>

                <v-card-text>
                    <v-container fluid>
                        <v-row class="mx-lg-2">
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

                        <v-spacer/>

                        <v-row justify="center">
                            <v-btn
                                icon
                                width="30"
                                class="my-6 mx-2"
                                href="https://www.facebook.com/flowrolled"
                                target="_blank"
                            >
                                <v-img src="https://flowrolled.nyc3.digitaloceanspaces.com/public/fb.png" />
                            </v-btn>
                            <v-btn
                                icon
                                width="30"
                                class="my-6 mx-2"
                                href="https://www.instagram.com/flowr0lled/"
                                target="_blank"
                            >
                                <v-img src="https://flowrolled.nyc3.digitaloceanspaces.com/public/insta.png" />
                            </v-btn>
                            <v-btn
                                icon
                                width="30"
                                class="my-6 mx-2"
                                href="https://twitter.com/flowrolled"
                                target="_blank"
                            >
                                <v-img src="https://flowrolled.nyc3.digitaloceanspaces.com/public/tw.png" />
                            </v-btn>
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
