<template>
    <v-app>
        <v-main>
            <v-container class="fill-height" fluid>
                <v-row align="center" justify="center">
                    <v-col cols="12" sm="8" md="4">
                        <v-form>
                            <v-card class="elevation-12">
                                <v-toolbar color="primary" dark flat>
                                    <v-toolbar-title>Welcome to FlowRolled</v-toolbar-title>
                                </v-toolbar>
                                <v-card-text>
                                    <v-text-field
                                        label="Email"
                                        name="email"
                                        prepend-icon="mdi-account-circle"
                                        type="text"
                                        v-model="email"
                                        :disabled="loading"
                                        :error-messages="errors.email"
                                    />

                                    <v-text-field
                                        id="password"
                                        label="Password"
                                        name="password"
                                        prepend-icon="mdi-lock"
                                        type="password"
                                        v-model="password"
                                        :disabled="loading"
                                        :error-messages="errors.password"
                                    />
                                </v-card-text>
                                <v-card-actions>
                                    <v-col cols="3">
                                        <a href="/signup">Sign up</a>
                                    </v-col>
                                    <v-col>
                                        <a href="/send-reset">Forgot Password</a>
                                    </v-col>
                                    <v-spacer />
                                    <v-btn
                                        :loading="loading"
                                        :disabled="loading"
                                        color="primary"
                                        @click="clickLogin"
                                        type="submit"
                                    >Login</v-btn>
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
            errors: {
                email: null,
                password: null
            },
            loading: false,
            password: null,
        }),
        methods: {
            async clickLogin() {
                this.loading = true;
                const resp = await fetch('/login', {
                    headers,
                    credentials: "same-origin",
                    method: 'POST',
                    body: JSON.stringify({
                        email: this.email,
                        password: this.password,
                    }),
                });
                if (resp.status >= 400) {
                    const json = await resp.json();
                    this.loading = false;
                    if (json && json.errors) {
                        this.errors.email = json.errors.email;
                        this.errors.password = json.errors.password;

                        if (json.errors.email && json.errors.email[0].match(new RegExp(/do not match/))) {
                            this.errors.password = json.errors.email;
                        }
                    }
                } else {
                    window.location = '/';
                }
            },
        },
    }
</script>
