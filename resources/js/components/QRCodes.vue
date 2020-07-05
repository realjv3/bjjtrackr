<template>
    <v-container fluid>
        <v-row justify="center">
            <v-card>
                <v-card-title>
                    QR Codes
                    <v-spacer></v-spacer>
                    <v-select
                        v-model="user"
                        :items="users"
                        item-value="id"
                        item-text="name"
                        :return-object="true"
                        style="width: 100px"
                        id="person-select"
                        @change="change"
                    ></v-select>
                </v-card-title>
                <v-card-text>
                    <img :src="imgSrc" />
                </v-card-text>
            </v-card>
        </v-row>
        <v-snackbar v-model="snackbar.show" :bottom="true" :multi-line="true">{{snackbar.text}}</v-snackbar>
    </v-container>
</template>

<script>
import {headers, isStudentOnly} from "../authorization";

export default {
    name: "QRCodes",
    data: () => ({
        scannedId: '',
        snackbar: {
            show: false,
            text: '',
        },
        timeStamp: 0,
        user: {},
    }),
    computed: {
        imgSrc() { return this.user && this.user.id ? `/qrcode/${this.user.id}` : ''; },
        users() {
            if (isStudentOnly()) {
                return this.$store.state.people.filter(person => person.id === user().id);
            }
            return this.$store.state.people.filter(person => person.roles.includes(4));
        },
    },
    methods: {
        change(e) {
            this.reset();
            document.getElementById('person-select').blur();
        },
        checkin() {
            const
                person = this.$store.state.people.find(person => person.id === Number(this.scannedId)),
                isoStr = new Date().toISOString(),
                checked_in_at = isoStr.substr(0, 10) + ' ' + isoStr.substr(11, 8);

            fetch(`/checkin`, {
                method: 'POST',
                headers,
                credentials: "same-origin",
                body: JSON.stringify({
                    client_id: person.client_id,
                    user_id: person.id,
                    checked_in_at,
                }),
            })
                .then(resp => resp.json())
                .then(json => {
                    if (json.errors) {
                        console.log(json.errors);
                    } else if (json.id) {
                        this.$emit('save-checkin');
                        const datetime = new Date(json.checked_in_at + ' UTC');
                        this.snackbar.text = `Checked in at ${datetime.toLocaleString()}`;
                        this.snackbar.show = true;
                    }
                    this.reset();
                });
        },
        onKeypress(e) {
            // if there is more than a few milliseconds bw current key press and and last one, start from scratch
            if (e.timeStamp > this.timeStamp + 15) {
                this.reset();
                if (e.code.match(new RegExp(/Digit\d+/))) {
                    this.scannedId += e.key;
                }
            }
            // only a few milliseconds bw current key press and and last one indicates scan, concat or checkin
            if (e.timeStamp <= this.timeStamp + 15) {

                if (e.code.match(new RegExp(/Digit\d+/))) {
                    this.scannedId += e.key;
                }
                if (e.keyCode === 13) {
                    this.checkin();
                }
            }
            this.timeStamp = e.timeStamp;
        },
        reset() {
            this.scannedId = '';
            this.timeStamp = 0;
        },
    },
    watch: {
        users(newUsers) {
            this.user = newUsers[0];
        }
    },
    created() {
        document.addEventListener('keypress', this.onKeypress);
    }
}
</script>
