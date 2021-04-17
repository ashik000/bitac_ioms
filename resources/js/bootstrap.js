window._ = require('lodash');
window.moment = require('moment');
require("moment-duration-format");

window.jquery = require('jquery');
window.popper = require('popper.js');
require('bootstrap');
import {store} from './store/index';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.baseURL = process.env.MIX_API_BASE_URL;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found!!');
}

axios.interceptors.request.use(request => {
    request.headers.common['Accept'] = 'application/json';
    const access_token = store.state.auth.token;
    // const access_token = window.localStorage.getItem('access_token');
    if (access_token) {
        request.headers.common['Authorization'] = `Bearer ${access_token}`;
    }

    // request.headers['X-Socket-Id'] = Echo.socketId()

    return request;
});

axios.interceptors.response.use((response) => (response), function (error) {
    if (error.response.status === 401) {
        store.commit('addAuthProperties', {
           token: null,
           refreshToken: null,
           tokenExpiration: null,
           role: null
        });
        // window.localStorage.setItem('access_token', '');
        // window.localStorage.setItem('refresh_token', '');
        // window.localStorage.setItem('token_expiration', '');
        // window.localStorage.setItem('role', '');
        window.location = '/login';
    }
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });
