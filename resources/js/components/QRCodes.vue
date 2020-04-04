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
                    ></v-select>
                </v-card-title>
                <v-card-text>
                    <img :src="imgSrc" />
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
        user: {},
    }),
    computed: {
        imgSrc() { return this.user.id ? `/qrcode/${this.user.id}` : ''; },
        users() {
            if (isStudentOnly()) {
                return this.$store.state.people.filter(person => person.id === user().id);
            }
            return this.$store.state.people.filter(person => person.roles.includes(4));
        },
    },
    watch: {
        users(newUsers) {
            this.user = newUsers[0];
        }
    },
    created() {
        document.addEventListener('keydown', function(e) {
            const
                textInput = e.key || String.fromCharCode(e.keyCode),
                targetName = e.target.localName;

            if (textInput && textInput.length === 1 && targetName !== 'input'){

                console.log('barcode scanned:  ', textInput);
            }
        });
    }
}
</script>
