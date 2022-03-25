<template>
    <v-app>
        <v-main>
            <v-container class="fill-height" fluid>
                <v-row align="center" justify="center">
                    <v-col cols="12" sm="8" md="4">
                        <v-form>
                            <v-card class="elevation-12">
                                <v-toolbar color="primary" dark flat id="login-header">
                                    <v-img src="https://flowrolled.nyc3.digitaloceanspaces.com/public/Logo%20-%20No%20Background.png" />
                                </v-toolbar>
                                <v-card-text id="login-form">
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
                                    <v-col cols="4">
                                        <a href="/signup">Sign up for free</a>
                                    </v-col>

                                    <v-col>
                                        <a href="/send-reset">Forgot Password</a>
                                    </v-col>

                                    <v-btn
                                        :loading="loading"
                                        :disabled="loading"
                                        color="primary"
                                        @click="clickLogin"
                                        type="submit"
                                    >Login</v-btn>
                                </v-card-actions>

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

<style scoped>
 header#login-header {
     height: 158px !important;
     background: center no-repeat url('https://flowrolled.nyc3.digitaloceanspaces.com/public/scarfHold.jpeg');
     background-size: cover;
 }

 #login-form {
     padding: 0 85px !important;
 }
</style>
