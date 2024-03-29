import {loadStripe} from "@stripe/stripe-js";
import Vue from 'vue';
import Vuex from 'vuex';
import {headers, isSuperAdmin} from "./authorization";

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        checkins: [],
        clients: [],
        documents: [],
        events: [],
        people: [],
        products: [],
        settings: initSettings,
        stripe: null,
        user: {},
    },
    mutations: {
        setCheckins(state, checkins) {
            state.checkins = checkins;
        },
        setClients(state, clients) {
            state.clients = clients;
        },
        setDocuments(state, documents) {
            state.documents = documents;
        },
        setEvents(state, events) {
            state.events = events;
        },
        setPeople(state, people) {
            state.people = people;
        },
        setProducts(state, products) {
            state.products = products;
        },
        setUser(state, user) {
            state.user = user;
        },
        setSettings(state, settings) {
            state.settings = settings;
        },
        setStripe(state, stripe) {
            state.stripe = stripe;
        },
    },
    actions: {
        async getCheckins({commit, state}) {
            const
                clientId = isSuperAdmin(state.user) ? '' : state.user.client_id,
                resp = await fetch(`/checkins/${clientId}`, {headers, credentials: "same-origin"}),
                json = await resp.json();
            commit('setCheckins', json);
        },
        async getClients({commit}) {
            const resp = await fetch('/clients', {
                headers,
                credentials: "same-origin",
            });
            if (resp.ok) {
                commit('setClients', await resp.json());
            }
        },
        async getDocuments({commit, state}) {
            const resp = await fetch(`/document/${state.user.client_id}`, {
                headers,
                credentials: "same-origin",
            });
            if (resp.ok) {
                commit('setDocuments', await resp.json());
            }
        },
        async getEvents({commit, state}) {
            const
                resp = await fetch(`/events/${state.user.client_id}`, {headers, credentials: "same-origin"}),
                events = await resp.json();
            commit('setEvents', events.map(event => {
                const
                    now = new Date(),
                    today = `${now.getFullYear()}-${now.getMonth() + 1}-${now.getDate()}`,
                    curDay = now.getDay(),
                    diffDaysToEventDay = curDay - event.day_id;
                let dateStr = today;

                if (diffDaysToEventDay < 0) {
                    // event is on a weekday after today
                    now.setDate(now.getDate() + Math.abs(diffDaysToEventDay));
                    dateStr = `${now.getFullYear()}-${now.getMonth() + 1}-${now.getDate()}`;
                } else if (diffDaysToEventDay > 0) {
                    // event is on a weekday prior to today
                    now.setDate(now.getDate() - diffDaysToEventDay);
                    dateStr = `${now.getFullYear()}-${now.getMonth() + 1}-${now.getDate()}`;
                }
                return {
                    id: event.id,
                    day_id: event.day_id,
                    name: event.name,
                    start: `${dateStr} ${event.start}`,
                    end: `${dateStr} ${event.end}`,
                };
            }));
        },
        async getPeople({commit}) {
            const resp = await fetch('/users', {headers, credentials: "same-origin"});
            if (resp.ok) {
                let json = await resp.json();
                json = json.map( user => {
                    user.roles = user.roles.map( role => role.id);
                    if (user.last_checkin) {
                        user.last_checkin = user.last_checkin.checked_in_at;
                    }
                    return user;
                });
                commit('setPeople', json);
            }
        },
        async getProducts({commit, state}) {
            const resp = await fetch(`/product/${state.user.client_id}`, {headers});
            if (resp.ok) {
                commit('setProducts', await resp.json());
            }
        },
        async getUser({commit}) {
            const resp = await fetch('/user', {headers, credentials: "same-origin"});
            if (resp.ok) {
                let user = await resp.json();
                commit('setUser', user[0]);
            }
        },
        async getSettings({commit, state}) {
            const
                clientId = state.user.client_id,
                resp = await fetch(`/settings/${clientId}`, {headers, credentials: "same-origin"}),
                json = await resp.json();
            commit('setSettings', json);
        },
        async setStripe({commit, state}) {
            if (state.user.client.stripe_account) {
                const stripe = await loadStripe(STRIPE_PUB_KEY, {stripeAccount: state.user.client.stripe_account});
                commit('setStripe', stripe);
            }
        },
    },
});

export default store;
