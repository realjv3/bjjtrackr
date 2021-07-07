import Vue from 'vue';
import vuetify from "./global";
import PrivacyPolicy from 'components/PrivacyPolicy';

new Vue({
    el: '#body',
    vuetify,
    ...PrivacyPolicy,
});
