<template>
    <v-container fluid>
        <v-row justify="center">
            <v-card>
                <v-card-title>
                    QR Codes
                    <v-spacer></v-spacer>
                    <v-select
                        v-model="selUser"
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
                    <img id="qr-code" :src="imgSrc" />
                </v-card-text>
            </v-card>
        </v-row>
    </v-container>
</template>

<script>
import {isStudentOnly} from "../authorization";

export default {
    name: "QRCodes",
    data: () => ({
        scannedId: '',
        timeStamp: 0,
        selUser: {},
    }),
    computed: {
        imgSrc() { return this.selUser && this.selUser.id ? `/qrcode/${this.selUser.id}` : ''; },
        user() {
            return this.$store.state.user;
        },
        users() {
            if (isStudentOnly(this.user)) {
                return this.$store.state.people.filter(person => person.id === this.user.id);
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

            this.$emit('save-checkin', {client_id: person.client_id, user_id: person.id, checked_in_at});
            this.reset();
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
                    console.log(`Scanned ${this.scannedId}`);
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
            this.selUser = newUsers[0];
        }
    },
    mounted() {
        document.addEventListener('keypress', this.onKeypress);
        this.selUser = this.user;
    }
}
</script>

<style scoped>
@media screen and (max-width: 400px) {
    #qr-code {
        width: 300px;
    }
}
</style>
