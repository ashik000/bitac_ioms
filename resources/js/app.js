require('./bootstrap');
import VCalendar from 'v-calendar';
import VueCtkDateTimePicker from 'vue-ctk-date-time-picker';
import Multiselect from 'vue-multiselect';

import Vue from 'vue'
import router from './routes';
import {store} from './store/index';
import Toasted from 'vue-toasted';

import App from './pages/App'

import Modal from "./components/Modal";

moment.updateLocale("en", { week: {
        dow: 6, // First day of week is Saturday
    }});

Vue.component('Modal', Modal);
Vue.component('VueCtkDateTimePicker', VueCtkDateTimePicker);
Vue.component('multiselect', Multiselect);

Vue.use(VCalendar);
Vue.use(Toasted);

const app = new Vue({
    el: '#app',
    store,
    components: {App},
    router,
});
