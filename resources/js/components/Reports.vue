<template>
    <v-container fluid>
        <v-row justify="center">
            <v-card style="width: 70vw">
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
                    <v-container>
                        <v-row :class="{
                            white_border: selUser.rank.belt_id === 1,
                            blue_border: selUser.rank.belt_id === 2,
                            purple_border: selUser.rank.belt_id === 3,
                            brown_border: selUser.rank.belt_id === 4,
                            black_border: selUser.rank.belt_id === 5,
                        }">
                            <div
                                class="td"
                                v-for="(_, index) in checkins"
                                :class="{
                                    nextrank: true,
                                    stripe1: showStripe(index, 1),
                                    stripe2: showStripe(index, 2),
                                    stripe3: showStripe(index, 3),
                                    stripe4: showStripe(index, 4),
                                    bluebelt: showBelt(index, 2),
                                    purplebelt: showBelt(index, 3),
                                    brownbelt: showBelt(index, 4),
                                    blackbelt: showBelt(index, 5),
                                }"
                            >
                                <span v-html="renderCheckin(index)"></span>
                            </div>
                        </v-row>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-row>
    </v-container>
</template>

<script>
import {isStudentOnly} from "../authorization";
import {utcDateTimeToLocal} from "../datetime_converters";

export default {
    name: "Reports",
    data: () => ({
        selUser: {
            id: null,
            rank: {
                belt_id: 1,
                stripes: 0,
                last_ranked_up: null,
            },
        },
    }),
    computed: {
        combineSameDayCheckins() {
            return this.settings[this.selUser.rank.belt_id].combine_same_day_checkins;
        },
        checkins() {
            const selUsersCheckins = this.$store.state.checkins.filter(checkin =>
                checkin.user_id === this.selUser.id && checkin.checked_in_at.slice(0, 10) > this.selUser.rank.last_ranked_up
            );
            let
                result = [],
                aggrUsersCheckins = [];

            if (selUsersCheckins.length && this.combineSameDayCheckins) {
                for (let i = 0; i < selUsersCheckins.length; i++) {
                    if (selUsersCheckins[i]) {
                        let
                            j = 1,
                            combinedCheckins = [selUsersCheckins[i]];
                        // if there more checkins with same date,
                        // combine into array at index of first of them & delete the subsequent same-date checkins
                        const dateStr = this.utcToLocal(selUsersCheckins[i].checked_in_at).slice(0, 10);
                        while (
                            selUsersCheckins[i + j]
                            && this.utcToLocal(selUsersCheckins[i + j].checked_in_at).slice(0, 10) === dateStr
                        ) {
                            combinedCheckins.push(selUsersCheckins[i + j]);
                            j++;
                        }
                        if (combinedCheckins.length > 1) {
                            aggrUsersCheckins.push(combinedCheckins);
                            i = i + j - 1;
                        } else {
                            aggrUsersCheckins.push(selUsersCheckins[i]);
                        }
                    }
                }
            }
            result = aggrUsersCheckins.length ? aggrUsersCheckins : selUsersCheckins;
            // add empty array elements to selUsersCheckins so that enough boxes render til next rank up
            if (result.length <= this.classesTilStripe) {
                const diff = this.classesTilStripe - result.length;
                for (let i = 0; i < diff; i++) {
                    result.push(null);
                }
            }
            return result;
        },
        users() {
            if (isStudentOnly(this.user)) {
                return this.$store.state.people.filter(person => person.id === this.user.id);
            }
            return this.$store.state.people.filter(person => person.roles.includes(4) && person.rank.belt_id !== 5);
        },
        classesTilStripe() {
            return Number(this.settings[this.selUser.rank.belt_id].sessions_til_stripe);
        },
        settings() {
            return this.$store.state.settings;
        },
        user() {
            return this.$store.state.user;
        },
    },
    methods: {
        renderCheckin(index) {
            if (this.checkins[index]) {
                if (Array.isArray(this.checkins[index])) {
                    const dateStr = new Date(this.checkins[index][0].checked_in_at).toLocaleDateString();
                    return `${dateStr}<br/>${this.checkins[index].length}X checkin`;
                } else {
                    return this.utcToLocal(this.checkins[index].checked_in_at);
                }
            }
        },
        showBelt(index, beltId) {
            return (
                index + 1 === Number(this.classesTilStripe)
                && this.selUser.rank.stripes === 4
                && this.selUser.rank.belt_id === beltId - 1
            );
        },
        showStripe(index, stripeNum) {
            return (
                index + 1 === Number(this.classesTilStripe)
                && this.selUser.rank.stripes + 1 === stripeNum
            );
        },
        utcToLocal: utcDateTimeToLocal,
    },
    watch: {
        users(newUsers) {
            this.selUser = newUsers[0];
        },
    },
    created() {
        this.selUser = this.user;
    },
}
</script>

<style scoped>
    table {
        border-collapse: collapse;
    }

    .td {
        height: 61.4px;
        width: 61.4px;
        padding-left: 2px;
        border: 1px solid;
        font-size: 10px;
        font-weight: bold;
    }

    .nextrank::after {
        content: "";
        opacity: .35;
        height: 60px;
        width: 60px;
        position: absolute;
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

    .white_border {
        border: 3px solid white;
    }

    .blue_border {
        border: 3px solid blue;
    }

    .purple_border {
        border: 3px solid rebeccapurple;
    }

    .brown_border {
        border: 3px solid saddlebrown;
    }

    .black_border {
        border: 3px solid black;
    }
</style>
