import Vue from 'vue';
import vuetify from "./global";
import SendReset from 'components/SendReset';

new Vue({
    el: '#body',
    vuetify,
    ...SendReset,
});
