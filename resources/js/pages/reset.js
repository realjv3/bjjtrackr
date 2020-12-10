import Vue from 'vue';
import vuetify from "./global";
import Reset from 'components/Reset';

new Vue({
    el: '#body',
    components: {Reset},
    props: {
        email: {default: email, required: true},
        token: {default: token, required: true},
    },
    vuetify,
    template: '<Reset :email="email" :token="token"/>',
});
