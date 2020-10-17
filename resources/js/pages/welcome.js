import Vue from 'vue';
import vuetify from "./global";
import Welcome from 'components/Welcome';

new Vue({
    el: '#body',
    vuetify,
    ...Welcome,
});
