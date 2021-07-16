<template>
    <v-app>
        <v-main>
            <v-container class="fill-height" fluid>
                <v-row align="center" justify="center">
                    <v-col cols="12" sm="8" md="4">
                        <v-form>
                            <v-card class="elevation-12">
                                <v-toolbar color="primary" dark flat>
                                    <v-toolbar-title>Get a link to reset your password</v-toolbar-title>
                                </v-toolbar>
                                <v-card-text>
                                    <v-text-field
                                        label="Email"
                                        name="email"
                                        prepend-icon="mdi-account-circle"
                                        type="text"
                                        v-model="email"
                                        :disabled="loading"
                                        :error-messages="error"
                                    />

                                </v-card-text>
                                <v-card-actions>
                                    <v-row justify="end" class="pr-5">
                                        <v-btn
                                            :loading="loading"
                                            :disabled="loading"
                                            color="primary"
                                            @click="clickSend"
                                            type="submit"
                                        >Send link</v-btn>
                                    </v-row>
                                </v-card-actions>
                            </v-card>
                        </v-form>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    import {headers} from '../authorization';

    export default {
        data: () => ({
            email: null,
            error: null,
            loading: false,
        }),
        methods: {
            async clickSend() {
                this.loading = true;
                const resp = await fetch('/forgot-password', {
                    headers,
                    credentials: "same-origin",
                    method: 'POST',
                    body: JSON.stringify({email: this.email}),
                });
                if (resp.status >= 400) {
                    const json = await resp.json();
                    this.loading = false;
                    if (json && json.errors) {
                        this.error = json.errors.email;
                    }
                } else {
                    window.location = '/';
                }
            },
        },
    }
</script>
