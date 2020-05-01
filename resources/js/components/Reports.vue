<template>
    <v-container fluid>
        <v-row justify="center">
            <v-card>
                <v-card-title>
                    Reports
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
                    <table>
                        <tr v-for="row in rows">
                            <td
                                v-for="field in fieldsPerRow"
                                :class="{
                                    stripe1: field * row === Number(classesTilStripe) && user.rank.stripes === 0,
                                    stripe2: field * row === Number(classesTilStripe) && user.rank.stripes === 1,
                                    stripe3: field * row === Number(classesTilStripe) && user.rank.stripes === 2,
                                    stripe4: field * row === Number(classesTilStripe) && user.rank.stripes === 3,
                                }"
                            >
                                <span
                                    v-if="checkins.length && checkins[checkinIndex(row, field)]">
                                    {{checkins[checkinIndex(row, field)].checked_in_at}}
                                </span>
                            </td>
                        </tr>
                    </table>
                </v-card-text>
            </v-card>
        </v-row>
    </v-container>
</template>

<script>
import {isStudentOnly} from "../authorization";

export default {
    name: "Reports",
    data: () => ({
        user: {
            id: null,
            rank: {
                belt: 1,
                stripes: 0,
                last_ranked_up: null,
            },
        },
    }),
    computed: {
        checkins() {
            return this.$store.state.checkins.filter(checkin =>
                checkin.user_id === this.user.id && checkin.checked_in_at.slice(0, 10) > this.user.rank.last_ranked_up
            );
        },
        users() {
            if (isStudentOnly()) {
                return this.$store.state.people.filter(person => person.id === user().id);
            }
            return this.$store.state.people.filter(person => person.roles.includes(4));
        },
        classesTilStripe() {
            return settings[this.user.rank.belt].classes_til_stripe;
        },
        fieldsPerRow() {
            return this.classesTilStripe / (this.user.rank.belt === 1 ? 3 : 5);
        },
        rows() {
            let rows = this.user.rank.belt === 1 ? 3 : 5;
            if (this.checkins.length) {
                const classesTilEligible = this.classesTilStripe - this.checkins.length;

                if (Math.sign(classesTilEligible) === -1) {

                    rows += Math.ceil(Math.abs(classesTilEligible) / (this.classesTilStripe / rows));
                }
            }
            return rows;
        },
    },
    methods: {
        checkinIndex(row, field) {
            if (this.checkins[field - 1]) {
                if (row === 1) {
                    return row * field - 1;
                } else {
                    return ((this.fieldsPerRow * (row - 1) ) + field) - 1;
                }
            }
        },
    },
    watch: {
        users(newUsers) {
            this.user = newUsers[0];
        }
    },
}
</script>

<style scoped>
    table {
        border-collapse: collapse;
    }

    td {
        position: relative;
        height: 50px;
        width: 50px;
        padding-left: 2px;
        border: 1px solid;
        font-size: 10px;
        font-weight: bold;
    }

    .stripe1::after {
        content: "";
        background-image: url("/storage/stripe1.png");
        opacity: .35;
        height: 50px;
        width: 50px;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
    }

    .stripe2::after {
        content: "";
        background-image: url("/storage/stripe2.png");
        opacity: .35;
        height: 50px;
        width: 50px;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
    }

    .stripe3::after {
        content: "";
        background-image: url("/storage/stripe3.png");
        opacity: .35;
        height: 50px;
        width: 50px;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
    }

    .stripe4::after {
        display: block;
        content: "";
        background-image: url("/storage/stripe4.png");
        opacity: .35;
        height: 50px;
        width: 50px;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
    }
</style>
