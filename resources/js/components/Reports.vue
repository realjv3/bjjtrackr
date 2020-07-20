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
                                    nextrank: true,
                                    stripe1: field * row === Number(classesTilStripe) && user.rank.stripes === 0,
                                    stripe2: field * row === Number(classesTilStripe) && user.rank.stripes === 1,
                                    stripe3: field * row === Number(classesTilStripe) && user.rank.stripes === 2,
                                    stripe4: field * row === Number(classesTilStripe) && user.rank.stripes === 3,
                                    bluebelt: field * row === Number(classesTilStripe)
                                            && user.rank.stripes === 4
                                            && user.rank.belt === 1,
                                    purplebelt: field * row === Number(classesTilStripe)
                                            && user.rank.stripes === 4
                                            && user.rank.belt === 2,
                                    brownbelt: field * row === Number(classesTilStripe)
                                            && user.rank.stripes === 4
                                            && user.rank.belt === 3,
                                    blackbelt: field * row === Number(classesTilStripe)
                                            && user.rank.stripes === 4
                                            && user.rank.belt === 4,
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