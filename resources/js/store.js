import Vue from 'vue';
import Vuex from 'vuex';
import {headers} from "./authorization";

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        clients: [],
        people: [],
    },
    mutations: {
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
                    return user;
                });
                commit('setPeople', json);
            }
        },
    },
});

export default store;
