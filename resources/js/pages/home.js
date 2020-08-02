import Vue from 'vue';
import Vuetify from "vuetify/lib";
import Home from 'components/Home';
import colors from 'vuetify/lib/util/colors';
import store from '../store';

const ignoreWarnMessage = 'The .native modifier for v-on is only valid on components but it was used on <div>.';
Vue.config.warnHandler = function (msg, vm, trace) {
    // `trace` is the component hierarchy trace
    if (msg === ignoreWarnMessage) {
        msg = null;
        vm = null;
        trace = null;
    }
}

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
store.dispatch('getPeople');

new Vue({
    el: '#body',
    store,
    vuetify,
    ...Home,
});
