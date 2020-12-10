<template>
    <v-app>
        <v-main>
            <v-container class="fill-height" fluid>
                <v-row align="center" justify="center">
                    <v-col cols="12" sm="8" md="4">
                        <v-form>
                            <v-card class="elevation-12">
                                <v-toolbar color="primary" dark flat>
                                    <v-toolbar-title>Reset your password</v-toolbar-title>
                                </v-toolbar>
                                <v-card-text>
                                    <v-text-field
                                        label="Email"
                                        prepend-icon="mdi-account-circle"
                                        type="text"
                                        :value="email"
                                        :disabled="true"
                                        :error-messages="errors.email"
                                    />
                                    <v-text-field
                                        type="password"
                                        label="New Password"
                                        prepend-icon="mdi-lock"
                                        v-model="password"
                                        :disabled="loading"
                                        :error-messages="errors.password"
                                    />
                                    <v-text-field
                                        label="Confirm Password"
                                        type="password"
                                        prepend-icon="mdi-lock"
                                        v-model="password_confirmation"
                                        :disabled="loading"
                                        :error-messages="errors.password"
                                    />
                                </v-card-text>
                                <v-card-actions>
                                    <v-row justify="end" class="pr-5">
                                        <v-btn
                                            :loading="loading"
                                            :disabled="loading"
                                            color="primary"
                                            @click="clickReset"
                                            type="submit"
                                        >Reset Password</v-btn>
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
        props: {
            email: {type: String, required: true},
            token: {type: String, required: true},
        },
        data: () => ({
            password: null,
            password_confirmation: null,
            errors: {
                email: null,
                password: null,
            },
            loading: false,
        }),
        methods: {
            async clickReset() {
                this.loading = true;
                this.resetErrors();
                const resp = await fetch('/reset-password', {
                    headers,
                    credentials: "same-origin",
                    method: 'POST',
                    body: JSON.stringify({
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation,
                        token: this.token,
                    }),
                });
                if (resp.status >= 400) {
                    const json = await resp.json();
                    this.loading = false;
                    if (json && json.errors) {
                        this.errors = json.errors;
                    }
                } else {
                    window.location = '/';
                }
            },
            resetErrors() {
                this.errors = {
                    email: null,
                    password: null,
                };
            },
         },
    }
</script>
