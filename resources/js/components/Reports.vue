<template>
    <v-container fluid>
        <v-row justify="center">
            <v-card>
                <v-card-title>
                    Reports
                    <v-spacer></v-spacer>
                    <v-select
                        v-model="selUser"
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
                                    nextrank: true,
                                    stripe1: showStripe(field, row, 1),
                                    stripe2: showStripe(field, row, 2),
                                    stripe3: showStripe(field, row, 3),
                                    stripe4: showStripe(field, row, 4),
                                    bluebelt: showBelt(field, row, 2),
                                    purplebelt: showBelt(field, row, 3),
                                    brownbelt: showBelt(field, row, 4),
                                    blackbelt: showBelt(field, row, 5),
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
        selUser: {
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
                checkin.user_id === this.selUser.id && checkin.checked_in_at.slice(0, 10) > this.selUser.rank.last_ranked_up
            );
        },
        users() {
            if (isStudentOnly()) {
                return this.$store.state.people.filter(person => person.id === this.user.id);
            }
            return this.$store.state.people.filter(person => person.roles.includes(4));
        },
        classesTilStripe() {
            return Number(this.settings[this.selUser.rank.belt].classes_til_stripe);
        },
        fieldsPerRow() {
            let fieldsPerRow = Math.round(this.classesTilStripe / (this.classesTilStripe <= 30 ? 3 : 5));
            fieldsPerRow = fieldsPerRow > 20 ? 20 : fieldsPerRow;
            if (fieldsPerRow < 10) {
                fieldsPerRow = 10;
            } else if (fieldsPerRow > 10 && window.innerWidth <= 752) {
                fieldsPerRow = 10;
            } else if (fieldsPerRow > 16 && window.innerWidth <= 845) {
                fieldsPerRow = 16;
            }
            return fieldsPerRow;
        },
        rows() {
            let rows = Math.ceil(this.classesTilStripe / this.fieldsPerRow);
            if (this.checkins.length) {
                const classesTilEligible = this.classesTilStripe - this.checkins.length;
                // if student is eligible for next stripe but has not received
                if (Math.sign(classesTilEligible) === -1) {

                    rows += Math.ceil(Math.abs(classesTilEligible) / (this.classesTilStripe / rows));
                }
            }
            return rows;
        },
        settings() {
            return this.$store.state.settings;
        },
        user() {
            return this.$store.state.user;
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
        showBelt(field, row, beltId) {
            return (
                this.fieldsPerRow * (row - 1) + field === Number(this.classesTilStripe)
                && this.selUser.rank.stripes === 4
                && this.selUser.rank.belt === beltId - 1
            );
        },
        showStripe(field, row, stripeNum) {
            return (
                this.fieldsPerRow * (row - 1) + field === Number(this.classesTilStripe)
                && this.selUser.rank.stripes + 1 === stripeNum
            );
        },
    },
    watch: {
        users(newUsers) {
            this.selUser = newUsers[0];
        },
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

    .nextrank::after {
        content: "";
        opacity: .35;
        height: 50px;
        width: 50px;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
    }

    .stripe1::after {
        background-image: url("/storage/ranks/stripe1.png");
    }

    .stripe2::after {
        background-image: url("/storage/ranks/stripe2.png");
    }

    .stripe3::after {
        background-image: url("/storage/ranks/stripe3.png");
    }

    .stripe4::after {
        display: block;
        background-image: url("/storage/ranks/stripe4.png");
    }

    .bluebelt::after {
        background-image: url("/storage/ranks/blue.png");
    }

    .purplebelt::after {
        background-image: url("/storage/ranks/purple.png");
    }

    .brownbelt::after {
        background-image: url("/storage/ranks/brown.png");
    }

    .blackbelt::after {
        background-image: url("/storage/ranks/black.png");
    }
</style>
