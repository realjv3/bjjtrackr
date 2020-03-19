import Vue from 'vue';
import Vuex from 'vuex';
import {headers} from "./authorization";

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        clients: [],
    },
    mutations: {
        setClients(state, clients) {
            state.clients = clients;
        },
    },
    actions: {
        getClients({commit}) {
            fetch('/clients', {
                headers,
                credentials: "same-origin",
            })
                .then( resp => {
                    if (resp.ok) {
                        return resp.json();
                    }
                })
                .then( json => commit('setClients', json))
        },
    },
});

export default store;
