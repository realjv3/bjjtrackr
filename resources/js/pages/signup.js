import Vue from 'vue';
import vuetify from "./global";
import Signup from 'components/Signup';

new Vue({
    el: '#body',
    vuetify,
    ...Signup,
});
