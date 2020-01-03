<template>
    <v-app>
        <v-content>
            <v-container
                class="fill-height"
                fluid
            >
                <v-row
                    align="center"
                    justify="center"
                >
                    <v-col
                        cols="12"
                        sm="8"
                        md="4"
                    >
                        <v-card class="elevation-12">
                            <v-toolbar
                                color="primary"
                                dark
                                flat
                            >
                                <v-toolbar-title>Welcome to BjjTrackr</v-toolbar-title>
                            </v-toolbar>
                            <v-card-text>
                                <v-form>
                                    <v-text-field
                                        label="Email"
                                        name="email"
                                        prepend-icon="mdi-account-circle"
                                        type="text"
                                        v-model="email"
                                        :error-messages="errors.email"
                                    />

                                    <v-text-field
                                        id="password"
                                        label="Password"
                                        name="password"
                                        prepend-icon="mdi-lock"
                                        type="password"
                                        v-model="password"
                                        :error-messages="errors.password"
                                    />
                                </v-form>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer />
                                <v-btn
                                    :loading="loading"
                                    :disabled="loading"
                                    color="primary"
                                    @click="clickLogin"
                                >Login</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
    export default {
        props: {
            source: String,
        },
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
            clickLogin() {
                this.loading = true;
                fetch('/login', {
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": CSRFToken,
                    },
                    credentials: "same-origin",
                    method: 'POST',
                    body: JSON.stringify({
                        email: this.email,
                        password: this.password,
                    }),
                })
                .then( resp => {
                    if (resp.status === 200) {
                        window.location = '/';
                    } else {
                        return resp.json();
                    }
                })
                .then( json => {
                    this.loading = false;
                    if (json && json.errors) {
                        this.errors.email = json.errors.email;
                        this.errors.password = json.errors.password;

                        if (json.errors.email && json.errors.email[0].match(new RegExp(/do not match/))) {
                            this.errors.password = json.errors.email;
                        }
                    }
                });
            },
        },
    }
</script>
