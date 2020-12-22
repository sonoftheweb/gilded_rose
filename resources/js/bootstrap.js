import  Vue from 'vue';
import axios from 'axios';
import Cookies from 'js-cookie';
import store from './store/index';
import moment from 'moment';

const $eventBus = new Vue();

axios.defaults.baseURL = process.env.MIX_APP_URL;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;


axios.interceptors.request.use(config => {
    $eventBus.$emit('toggle-loading', true);

    if (Cookies.get('token')) {
        config.headers.common['Authorization'] = 'Bearer ' + Cookies.get('token');
    }

    return config;
}, error => {
    return Promise.reject(error);
})

axios.interceptors.response.use(response => {
    // When a response is received, trigger hide loading
    $eventBus.$emit('toggle-loading', false );
    return response;

}, error => {

    $eventBus.$emit('toggle-loading', false );

    let error_data = error.response.data;

    if (error_data.hasOwnProperty('action_required')) {
        // if the response is a 40* or an error and the response comes back with some data we can check for
    } else {
        if (error.response.status === 404) {
            // not found page
        }

        if (error.response.status === 401 && error_data.message === 'Unauthenticated.') {
            // 401 returned but no type defined, means user does not have access
            store.dispatch('after_logout');
            return;
        }
    }

    $eventBus.$emit({
        status: 'error',
        message: error_data.hasOwnProperty('message') ? error_data.message : 'Unexpected error encountered. This issue has been logged.'
    })

    return Promise.reject(error)
});

Vue.prototype.$http = axios
Vue.prototype.$eventBus = $eventBus
Vue.prototype.$moment = moment
