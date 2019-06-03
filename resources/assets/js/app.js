import Vue from 'vue';
import Vuetify from 'vuetify';
import VueMoment from 'vue-moment';
import VeeValidate from 'vee-validate';
import moment from 'moment';
import da from 'moment/locale/da';
import axios from 'axios';
import router from './router';
import store from './store';

// Vuetify css
import 'vuetify/dist/vuetify.min.css';
import '@mdi/font/css/materialdesignicons.css';

// Polyfills
import 'es6-promise/auto';
import 'babel-polyfill';

Vue.use(VueMoment, {
    moment
});
Vue.use(VeeValidate);
Vue.use(Vuetify, {
    iconfont: 'mdi',
    theme: {
        primary: '#1976D2',
        secondary: '#424242',
        accent: '#82B1FF',
        error: '#FF5252',
        info: '#2196F3',
        success: '#4CAF50',
        warning: '#FFC107'
    }
});

// Autoload components
const files = require.context('./components', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

new Vue({
    el: '#app',
    store,
    router
});

// Need a way to globally show this
// <notification :text="notification.text" :show="notification.show" @close="notification.show = false"></notification>
// notification: {
//     text: null,
//     show: false
// }
//
// notify(e) {
//     this.notification.text = e.text;
//     this.notification.show = true;
// },
