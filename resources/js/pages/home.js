import Vue from 'vue';
import Vuetify from "vuetify/lib";
import Home from 'components/Home';
import colors from 'vuetify/lib/util/colors';
import store from '../store';

const vuetify = new Vuetify({
    theme: {
        dark: true,
        themes: {
            dark: {
                primary: colors.indigo.lighten2,
                secondary: colors.pink.lighten2,
            },
        },
    },
});
Vue.use(Vuetify);

store.dispatch('getClients');

new Vue({
    el: '#body',
    store,
    vuetify,
    ...Home,
});
