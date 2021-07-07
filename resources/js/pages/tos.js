import Vue from 'vue';
import vuetify from "./global";
import ToS from 'components/ToS';

new Vue({
    el: '#body',
    vuetify,
    ...ToS,
});
