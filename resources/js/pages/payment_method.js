import Vue from 'vue';
import vuetify from "./global";
import Signup from 'components/Signup';
import store from '../store';

Promise.all([
    store.dispatch('getClients'),
    store.dispatch('getPeople'),
    store.dispatch('getUser'),
])
    .then(() =>
        new Vue({
            el: '#body',
            store,
            vuetify,
            ...Signup,
        })
    );
