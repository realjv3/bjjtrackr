<template>
    <v-container fluid>
        <v-row class="mb-10" justify="center">

            <v-card>
                <v-card-title>

                    <div class="d-flex flex-column">
                        <div class="mb-3">Membership Plans</div>
                        <v-btn x-small @click="show.membershipModal = true">+Add membership plan</v-btn>
                    </div>

                    <v-spacer></v-spacer>

                    <v-text-field
                        v-model="searchMemberships"
                        append-icon="search"
                        label="Search"
                        single-line
                        hide-details
                    ></v-text-field>

                </v-card-title>

                <v-data-table
                    :headers="membershipHeaders"
                    :items="memberships"
                    :items-per-page="10"
                    class="elevation-1"
                    :loading="loading"
                    :search="searchMemberships"
                >
                    <template v-slot:item.prices="{ item }">
                        <div class="d-flex flex-column">
                            <span v-for="price in item.prices" v-if="price.active">
                                ${{ price.amount }} every {{price.period_count}} {{price.period}}{{price.period_count > 1 ? 's': ''}}
                            </span>
                        </div>
                    </template>

                    <template v-slot:item.active="{ item }">
                        <span v-if="item.active">active</span>
                        <span v-else>archived</span>
                    </template>

                    <template v-slot:item.action="{ item }">
                        <v-icon small class="mr-2" @click="editMembership(item)">edit</v-icon>
                    </template>

                </v-data-table>
            </v-card>

            <v-dialog v-model="show.membershipModal" :persistent="true" max-width="400">
                <v-card>
                    <v-card-title class="grey darken-2" v-text="`${editing ? 'Edit' : 'Add'} Membership Plan`"/>

                    <v-container fluid>
                        <v-form v-model="valid" ref="membershipForm">

                            <v-row class="mx-2">
                                <v-col>
                                    <v-text-field v-model="membership.name" label="Membership Name" required :rules="rules.required"/>
                                </v-col>
                            </v-row>

                            <v-row class="mx-2">
                                <v-col cols="6">
                                    <v-text-field v-model="membership.unit" label="Unit"/>
                                </v-col>
                            </v-row>

                            <v-col cols="2">
                                <v-checkbox
                                    v-if="membership.hasOwnProperty('active')"
                                    v-model="membership.active"
                                    label="Active"
                                />
                            </v-col>

                            <v-divider class="mx-3"/>

                            <v-btn v-if="editing" x-small @click="addPrice" class="ma-2">+Add price</v-btn>

                            <v-expansion-panels v-if="membership.prices.length > 1">
                                <v-expansion-panel v-for="(price,i) in membership.prices" :key="i">

                                    <v-expansion-panel-header v-if="!price.amount">New Price</v-expansion-panel-header>
                                    <v-expansion-panel-header v-else>
                                        ${{price.amount}} per {{membership.unit}} every {{price.period_count}} {{price.period}}s
                                    </v-expansion-panel-header>

                                    <v-expansion-panel-content>

                                        <v-row>
                                            <v-col cols="4">
                                                <v-text-field
                                                    v-model="price.amount"
                                                    label="Amount"
                                                    required
                                                    :rules="rules.price"
                                                    :disabled="price.id && editing"
                                                />
                                            </v-col>

                                            <v-col cols="2">
                                                <v-checkbox
                                                    v-if="price.hasOwnProperty('active')"
                                                    v-model="price.active"
                                                    label="Active"
                                                />
                                            </v-col>
                                        </v-row>

                                        <v-row>
                                            <v-col cols="3" class="pt-8">Bill every</v-col>
                                            <v-col cols="4">
                                                <v-text-field
                                                    v-if="price.period === 'month'"
                                                    v-model="price.period_count"
                                                    label="Interval"
                                                    type="number"
                                                    min="1"
                                                    max="12"
                                                    required
                                                    :rules="rules.month"
                                                    :error="errors[`prices.0.period_count`]"
                                                    :error-messages="errors[`prices.0.period_count`]"
                                                    :disabled="price.id && editing"
                                                />
                                                <v-text-field
                                                    v-else-if="price.period === 'week'"
                                                    v-model="price.period_count"
                                                    label="Interval"
                                                    type="number"
                                                    min="1"
                                                    max="52"
                                                    required
                                                    :rules="rules.week"
                                                    :error="errors[`prices.0.period_count`]"
                                                    :error-messages="errors[`prices.0.period_count`]"
                                                    :disabled="price.id && editing"
                                                />
                                                <v-text-field
                                                    v-else-if="price.period === 'day'"
                                                    v-model="price.period_count"
                                                    label="Interval"
                                                    type="number"
                                                    min="1"
                                                    max="365"
                                                    required
                                                    :rules="rules.day"
                                                    :error="errors[`prices.0.period_count`]"
                                                    :error-messages="errors[`prices.0.period_count`]"
                                                    :disabled="price.id && editing"
                                                />
                                            </v-col>
                                            <v-col cols="4">
                                                <v-select
                                                    :items="periods"
                                                    v-model="price.period"
                                                    label="Billing Period"
                                                    required
                                                    :rules="rules.required"
                                                    @change="onBillingPeriodSelect"
                                                    :disabled="price.id && editing"
                                                />
                                            </v-col>
                                        </v-row>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>

                            <v-row v-else v-for="(price, i) in membership.prices" class="mx-2 mt-2" :key="i">

                                <v-row class="mx-1">
                                    <v-col cols="4">
                                        <v-text-field
                                            v-model="price.amount"
                                            label="Amount"
                                            required
                                            :rules="rules.price"
                                            :disabled="price.id && editing"
                                        />
                                    </v-col>

                                    <v-col cols="2">
                                        <v-checkbox
                                            v-if="price.hasOwnProperty('active')"
                                            v-model="price.active"
                                            label="Active"
                                        />
                                    </v-col>
                                </v-row>

                                <v-row class="ml-1">
                                    <v-col cols="3" class="pt-8">Bill every</v-col>
                                    <v-col cols="4">
                                        <v-text-field
                                            v-if="price.period === 'month'"
                                            v-model="price.period_count"
                                            label="Interval"
                                            type="number"
                                            min="1"
                                            max="12"
                                            required
                                            :rules="rules.month"
                                            :error="errors[`prices.${i}.period_count`]"
                                            :error-messages="errors[`prices.${i}.period_count`]"
                                            :disabled="price.id && editing"
                                        />
                                        <v-text-field
                                            v-else-if="price.period === 'week'"
                                            v-model="price.period_count"
                                            label="Interval"
                                            type="number"
                                            min="1"
                                            max="52"
                                            required
                                            :rules="rules.week"
                                            :error="errors[`prices.${i}.period_count`]"
                                            :error-messages="errors[`prices.${i}.period_count`]"
                                            :disabled="price.id && editing"
                                        />
                                        <v-text-field
                                            v-else-if="price.period === 'day'"
                                            v-model="price.period_count"
                                            label="Interval"
                                            type="number"
                                            min="1"
                                            max="365"
                                            required
                                            :rules="rules.day"
                                            :error="errors[`prices.${i}.period_count`]"
                                            :error-messages="errors[`prices.${i}.period_count`]"
                                            :disabled="price.id && editing"
                                        />
                                    </v-col>
                                    <v-col cols="4">
                                        <v-select
                                            :items="periods"
                                            v-model="price.period"
                                            label="Billing Period"
                                            required
                                            :rules="rules.required"
                                            @change="onBillingPeriodSelect"
                                            :disabled="price.id && editing"
                                        />
                                    </v-col>
                                </v-row>
                            </v-row>

                            <v-row justify="end">
                                <v-card-actions>
                                    <v-btn text @click="handleSaveMembership" :loading="loading" :disabled="!valid">Save</v-btn>
                                    <v-btn text color="primary" @click="reset">Cancel</v-btn>
                                </v-card-actions>
                            </v-row>
                        </v-form>
                    </v-container>
                </v-card>
            </v-dialog>
        </v-row>

        <v-row class="mb-10" justify="center">
            <v-card>
                <v-card-title>
                    <div class="d-flex flex-column">
                        <div class="mb-3">Members</div>
                        <v-btn x-small @click="show.memberModal = true">+Add member</v-btn>
                    </div>

                    <v-spacer></v-spacer>

                    <v-text-field
                        v-model="searchMembers"
                        append-icon="search"
                        label="Search"
                        single-line
                        hide-details
                    />
                </v-card-title>

                <v-data-table
                    :headers="memberHeaders"
                    :items="members"
                    :items-per-page="10"
                    class="elevation-1"
                    :loading="loading"
                    :search="searchMembers"
                >
                    <template v-slot:item.price="{ item }">
                        <span v-if="item.price">
                            ${{ item.price.amount }} every {{item.price.period_count}} {{item.price.period}}{{item.price.period_count > 1 ? 's': ''}}
                        </span>
                    </template>

                    <template v-slot:item.current_period_end="{ item }">
                        {{ utcToYMD(item.current_period_end) }}
                    </template>

                    <template v-slot:item.action="{ item }">
                        <v-icon small class="mr-2" @click="editMember(item)" :disabled="item.status === 'canceled'">
                            edit
                        </v-icon>

                        <v-icon
                            v-if="item.pause_collection"
                            small
                            class="mr-2"
                            @click="handleResumeMember(item)"
                            :disabled="item.status === 'canceled'"
                        >
                            mdi-play
                        </v-icon>

                        <v-icon
                            v-else
                            small
                            class="mr-2"
                            @click="handlePauseMember(item)"
                            :disabled="item.status === 'canceled' || ! item.subscription_id"
                        >
                            pause
                        </v-icon>

                        <v-icon
                            v-if="item.cancel_at_period_end"
                            small
                            class="mr-2"
                            @click="handleResumeMember(item)"
                            :disabled="item.status === 'canceled' || ! item.subscription_id"
                        >
                            mdi-play
                        </v-icon>

                        <v-icon
                            v-else
                            small
                            @click="handleCancelMember(item)"
                            :disabled="item.status === 'canceled' || ! item.subscription_id"
                        >
                            stop
                        </v-icon>
                    </template>
                </v-data-table>
            </v-card>

            <v-dialog v-model="show.memberModal" :persistent="true" max-width="400">
                <v-card>
                    <v-card-title class="grey darken-2" v-text="`${editing ? 'Edit' : 'Add'} Membership`"/>

                    <v-container fluid>
                        <v-form v-model="valid" ref="memberForm">

                            <v-row class="mx-2">
                                <v-col cols="8">
                                    <v-select
                                        label="Member"
                                        v-model="member.user_id"
                                        :items="students"
                                        required
                                        :rules="rules.required"
                                        :disabled="editing"
                                    />
                                </v-col>
                            </v-row>

                            <v-row v-if="member.user_id" class="mx-2">

                                <PaymentMethodsMembers :client="client" :member="member" @cust-id="setCustID"/>
                            </v-row>

                            <v-row class="mx-2">
                                <v-col cols="8">
                                    <v-select
                                        label="Membership"
                                        v-model="member.membership_id"
                                        :items="membershipOptions"
                                        required
                                        :rules="rules.required"
                                        :disabled="member.pause_collection"
                                    />
                                </v-col>
                            </v-row>

                            <div v-if="member.membership_id">
                                <v-row class="mx-2">
                                    <v-col cols="8">
                                        <v-select
                                            label="Price"
                                            v-model="member.price_id"
                                            :items="priceOptions"
                                            required
                                            :rules="rules.required"
                                            :disabled="member.pause_collection"
                                        />
                                    </v-col>

                                    <v-col v-if="! editing">
                                        <v-text-field
                                            label="Trial Period Days"
                                            v-model="member.trial_period_days"
                                            type="number"
                                            min="0"
                                            step="1"
                                            required
                                            :rules="rules.number"
                                        />
                                    </v-col>
                                </v-row>
                            </div>


                            <v-row justify="end">
                                <v-card-actions>
                                    <v-btn text @click="handleSaveMember" :loading="loading" :disabled="!valid">Save</v-btn>
                                    <v-btn text color="primary" @click="reset">Cancel</v-btn>
                                </v-card-actions>
                            </v-row>

                        </v-form>
                    </v-container>
                </v-card>
            </v-dialog>

            <v-dialog v-model="show.pauseModal" :persistent="true" max-width="400">
                <v-card>
                    <v-card-title class="grey darken-2" v-text="`Pause Membership`"/>
                    <v-container fluid>
                        <v-form>

                            <v-row class="mx-2">
                                <v-col>
                                    <p>
                                        This member's subscription payments will be paused until the chosen date or until
                                        they're manually unpaused.
                                    </p>
                                </v-col>
                            </v-row>

                            <v-row class="mx-2">
                                <v-col cols="6">
                                    <v-text-field
                                        label="Resume on"
                                        :value="member.resume_collection"
                                        @click="show.pickResumeDate = true"
                                        prepend-inner-icon="mdi-calendar-month-outline"
                                    />
                                </v-col>
                            </v-row>

                            <v-dialog v-model="show.pickResumeDate" class="mx-auto" width="290px">
                                <v-date-picker v-model="member.resume_collection" />
                            </v-dialog>

                            <v-row justify="end">
                                <v-card-actions>
                                    <v-btn text @click="pauseMember" :loading="loading">Pause Membership</v-btn>
                                    <v-btn text color="primary" @click="reset">Cancel</v-btn>
                                </v-card-actions>
                            </v-row>

                        </v-form>
                    </v-container>
                </v-card>
            </v-dialog>

            <v-dialog v-model="show.cancelModal" :persistent="true" max-width="400">
                <v-card>
                    <v-card-title class="grey darken-2" v-text="`Cancel Membership`"/>
                    <v-container fluid>
                        <v-form>

                            <v-row class="mx-2">
                                <v-col>
                                    <p>
                                        This member's subscription payments will be canceled
                                        {{member.cancel_at_period_end ? 'at the end of the current billing period' : 'immediately'}}.
                                    </p>
                                </v-col>
                            </v-row>

                            <v-row class="mx-2">
                                <v-col>
                                    <v-switch
                                        v-model="member.cancel_at_period_end"
                                        :label="`Cancel ${member.cancel_at_period_end ? 'at the end of the current billing period' : 'immediately'}`"
                                        :loading="loading"
                                        :disabled="loading"
                                    />
                                </v-col>
                            </v-row>

                            <v-row justify="end">
                                <v-card-actions>
                                    <v-btn text @click="cancelMember" :loading="loading">Cancel Membership</v-btn>
                                    <v-btn text color="primary" @click="reset">Close</v-btn>
                                </v-card-actions>
                            </v-row>

                        </v-form>
                    </v-container>
                </v-card>
            </v-dialog>

        </v-row>
    </v-container>
</template>

<script>
import {headers} from '../authorization';
import {utcDateTimeToLocal, utcDateTimeToLocalYMD} from "../datetime_converters";
import Documents from "components/Documents";
import PaymentMethodsMembers from "components/PaymentMethodsMembers";
import Vue from "vue";
import Fetches from "../fetches";

const fetches = new Fetches();

function Membership() {
    this.name = null;
    this.unit = 'Student';
    this.prices = [new Price()];
}

function Price() {
    this.amount = null;
    this.period = "month";
    this.period_count = 1;
    this.recurring = 1;
}

function Member() {
    this.id = null;
    this.user_id = null;
    this.membership_id = null;
    this.price_id = null;
    this.trial_period_days = 0;
    this.pause_collection = null;
    this.resume_collection = null;
    this.cancel_at_period_end = false;
}

export default {
    name: "Memberships",
    components: {Documents, PaymentMethodsMembers},
    data: () => ({
        editing: false,
        errors: {},
        loading: false,
        memberHeaders: [
            { text: 'Name', align: 'left', value: 'user.name' },
            { text: 'Plan', align: 'left', value: 'price.product.name' },
            { text: 'Price', value: 'price'},
            { text: 'Status', value: 'status' },
            { text: 'Current Period End', value: 'current_period_end' },
            { text: 'Actions', value: 'action', sortable: false },
        ],
        members: [],
        member: new Member(),
        membership: new Membership(),
        membershipHeaders: [
            { text: 'Name', align: 'left', value: 'name' },
            { text: 'Price', value: 'prices'},
            { text: 'Active', value: 'active' },
            { text: 'Edit', value: 'action', sortable: false },
        ],
        memberships: [],
        periods: [
            {text: 'Days', value: 'day'},
            {text: 'Weeks', value: 'week'},
            {text: 'Months', value: 'month'},
        ],
        rules: {
            required: [v => !!v || 'Field is required'],
            month: [v => v > 0 && v <= 12 || 'Can not exceed a year.'],
            week: [v => v > 0 && v <= 52 || 'Can not exceed a year.'],
            day: [v => v > 0 && v <= 365 || 'Can not exceed a year.'],
            number: [v => /\d+/.test(v) || 'Number is required'],
            price: [v => /^\d+(\.\d{1,2})?$/.test(v) || 'Price must be valid'],
        },
        searchMembers: '',
        searchMemberships: '',
        show: {
            cencelModal: false,
            memberModal: false,
            membershipModal: false,
            pauseModal: false,
            pickResumeDate: false,
        },
        valid: false,
    }),
    computed: {
        client() {
            return this.$store.state.clients.find(client => client.id === this.user.client_id);
        },
        membershipOptions() {
            return this.memberships
                .filter(m => m.active)
                .map(m => ({text: m.name, value: m.id}));
        },
        priceOptions() {
            const membership = this.memberships.find(m => m.id === this.member.membership_id);
            return membership.prices
                .filter(p => p.active)
                .map(p => {
                    const text = `$${p.amount} every ${p.period_count} ${p.period}${p.period_count > 1 ? 's': ''}`;
                    return {text, value: p.id};
                });
        },
        user() {
            return this.$store.state.user;
        },
        students() {
            return this.$store.state.people.map(person => ({text: person.name, value: person.id}));
        },
    },
    created() {
        this.refresh();
    },
    methods: {
        addMember() {
            this.loading = true;
            this.errors = {};
            fetch(`/member/${this.client.id}/${this.member.user_id}/${this.member.membership_id}/${this.member.price_id}`, {
                method: 'POST',
                headers,
                body: JSON.stringify(this.member),
            })
                .then( resp => {
                    this.loading = false;
                    if (resp.ok) {
                        this.refresh();
                        this.reset();
                    }
                    return resp.json();
                })
                .then(json => {
                    if (json.errors) {
                        this.errors = json.errors;
                    }
                });
        },
        addMembership() {
            this.loading = true;
            this.errors = {};
            fetch(`/product/${this.client.id}`, {
                method: 'POST',
                headers,
                body: JSON.stringify(this.membership),
            })
                .then( resp => {
                    this.loading = false;
                    if (resp.ok) {
                        this.refresh();
                        this.reset();
                    }
                    return resp.json();
                })
                .then(json => {
                    if (json.errors) {
                        this.errors = json.errors;
                    }
                });
        },
        addPrice() {
            this.membership.prices.push(new Price());
        },
        cancelMember() {
            this.loading = true;
            this.errors = {};
            fetch(`member/${this.member.id}/product/${this.member.price.product.id}/cancel`, {
                method: 'PATCH',
                headers,
                body: JSON.stringify(this.member),
            })
                .then( resp => resp.json())
                .then( membership => {
                    this.loading = false;
                    this.refresh();
                    this.reset();
                });
        },
        editMember(member) {
            this.member = {...this.member, ...member};
            if (member.price) {
                this.member.membership_id = member.price.product.id;
            }
            this.editing = !!member.subscription_id;
            this.show.memberModal = true;
        },
        editMembership(membership) {
            this.membership = membership;
            this.editing = true;
            this.show.membershipModal = true;
        },
        handleCancelMember(member) {
            this.member = {...this.member, ...member};
            if (member.price) {
                this.member.membership_id = member.price.product.id;
            }
            this.show.cancelModal = true;
        },
        handlePauseMember(member) {
            this.member = {...this.member, ...member};
            if (member.price) {
                this.member.membership_id = member.price.product.id;
            }
            this.show.pauseModal = true;
        },
        handleResumeMember(member) {
            this.member = {...this.member, ...member};
            if (member.price) {
                this.member.membership_id = member.price.product.id;
            }
            confirm('Are you sure you want to resume collecting payments from this member?')
            && fetch(`member/${this.member.id}/product/${this.member.price.product.id}/resume`, {
                method: 'PATCH',
                headers,
                body: JSON.stringify(this.member),
            })
                .then( resp => resp.json())
                .then( () => {
                    this.refresh();
                    this.reset();
                });
        },
        handleSaveMember() {
            this.editing ? this.updateMember() : this.addMember()
        },
        handleSaveMembership() {
            this.editing ? this.updateMembership() : this.addMembership()
        },
        onBillingPeriodSelect() {
            Vue.nextTick()
                .then(() => this.$refs.membershipForm.validate());
        },
        pauseMember() {
            this.loading = true;
            this.errors = {};
            fetch(`member/${this.member.id}/product/${this.member.price.product.id}/pause`, {
                method: 'PATCH',
                headers,
                body: JSON.stringify(this.member),
            })
                .then( resp => resp.json())
                .then( () => {
                    this.loading = false;
                    this.refresh();
                    this.reset();
                });
        },
        refresh() {
            this.loading = true;

            Promise.all([
                fetch(`/product/${this.client.id}`)
                    .then(resp => resp.json())
                    .then(json => this.memberships = json),

                fetch(`/member/${this.client.id}`)
                    .then(resp => resp.json())
                    .then(members => this.members = members.map(member => {

                        if (member.cancel_at_period_end === 1 && member.status === 'active') {

                            member.status =  'cancels at period end';

                        } else if (member.pause_collection === 1 && member.status === 'active') {

                            member.status = 'paused';

                            if (member.resumes_at) {

                                const resumesAt = new Date(member.resumes_at * 1000);
                                member.status += ' until ' + resumesAt.toLocaleDateString();
                            }
                        }

                        return member;
                    })),

            ]).then(() => this.loading = false);
        },
        reset() {
            this.refresh();
            this.show = {
                memberModal: false,
                membershipModal: false,
                pauseModal: false,
            };
            this.editing = false;
            this.member = new Member();
            this.membership = new Membership();
            this.errors = {};
        },
        resumeMember() {
            this.loading = true;
            this.errors = {};
            fetch(`member/${this.member.id}/product/${this.member.price.product.id}/resume`, {method: 'PATCH', headers})
                .then( resp => resp.json())
                .then( membership => {
                    this.loading = false;
                    this.refresh();
                    this.reset();
                });
        },
        /**
         * @param custId {String} - Stripe customer id
         */
        setCustID(custId) {
            this.member.cust_id = custId;
        },
        updateMember() {
            this.loading = true;
            this.errors = {};
            const body = JSON.stringify(this.member);
            delete body.trial_period_days;
            fetch(`/member/${this.member.id}/${this.member.membership_id}/${this.member.price_id}`, {
                method: 'PATCH',
                headers,
                body,
            })
                .then( resp => {
                    this.loading = false;
                    if (resp.ok) {
                        this.refresh();
                        this.reset();
                    }
                    return resp.json();
                })
                .then(json => {
                    if (json.errors) {
                        this.errors = json.errors;
                    }
                });
        },
        updateMembership() {
            this.loading = true;
            this.errors = {};
            fetch(`/product/${this.client.id}/${this.membership.id}`, {
                method: 'PATCH',
                headers,
                credentials: "same-origin",
                body: JSON.stringify(this.membership),
            })
                .then( resp => {
                    this.loading = false;
                    if (resp.ok) {
                        this.refresh();
                        this.reset();
                    }
                    return resp.json();
                })
                .then(json => {
                    if (json.errors) {
                        this.errors = json.errors;
                    }
                });
        },
        utcToLocal: utcDateTimeToLocal,
        utcToYMD: utcDateTimeToLocalYMD,
    },
}
</script>
