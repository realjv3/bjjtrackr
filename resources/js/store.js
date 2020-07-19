import Vue from 'vue';
import Vuex from 'vuex';
import {headers, isSuperAdmin} from "./authorization";

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        checkins: [],
        clients: [],
        people: [],
        user: user(),
    },
    mutations: {
        setCheckins(state, checkins) {
            state.checkins = checkins;
        },
        setClients(state, clients) {
            state.clients = clients;
        },
        setPeople(state, people) {
            state.people = people;
        },
    },
    actions: {
        async getClients({commit}) {
            const resp = await fetch('/clients', {
                headers,
                credentials: "same-origin",
            });
            if (resp.ok) {
                commit('setClients', await resp.json());
            }
        },
        async getPeople({commit}) {
            const resp = await fetch('/users', {headers, credentials: "same-origin"});
            if (resp.ok) {
                let json = await resp.json();
                json = json.map( user => {
                    user.client = user.client ? user.client.name : null;
                    user.roles = user.roles.map( role => role.id);
                    if (user.last_checkin) {
                        user.last_checkin = user.last_checkin.checked_in_at;
                    }
                    return user;
                });
                commit('setPeople', json);
            }
        },
        async getCheckins({commit}) {
            const
                clientId = isSuperAdmin() ? '' : user().client_id,
                resp = await fetch(`/checkins/${clientId}`, {headers, credentials: "same-origin"}),
                json = await resp.json();
            commit('setCheckins', json);
        },
    },
});

export default store;
