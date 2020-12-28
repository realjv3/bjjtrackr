import Vue from 'vue';
import vuetify from "./global";
import Home from 'components/Home';
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

new Vue({
    el: '#body',
    store,
    vuetify,
    ...Home,
});
