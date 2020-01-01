import Vue from 'vue';
import Vuetify from "vuetify/lib";
import Welcome from 'components/Welcome';
import colors from 'vuetify/lib/util/colors';

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

new Vue({
    el: '#body',
    vuetify,
    ...Welcome,
});
