import Cookies from 'js-cookie';
import axios from 'axios';

const state = {
    authenticated: Cookies.get('token') !== undefined && Cookies.get('token') !== null && Cookies.get('token') !== '',
    token: Cookies.get('token') || '',
    data_ready: false,
    subscribed: false
}

const mutations = {
    set_token(state, token) {
        state.token = token;
    },

    set_authenticated(state, boolean_value) {
        state.authenticated = boolean_value;
    },

    set_data_ready(state) {
        state.data_ready = true;
    },

    delete_user(state) {
        this.replaceState({});
        let current_state = state;
        current_state.user = {};
        this.replaceState(current_state);
    }
}

const actions = {
    after_login(context, data) {
        Cookies.set('token', data.access_token, {expires: data.expires_in});
        context.commit('set_token', data.access_token);
        context.commit('set_authenticated', true);
        context.dispatch('fetch_user_data');
    },

    async fetch_user_data(context) {
        await axios.get('/api/v1/users/me').then(response => {
            let user_object = {
                user: {
                    id: response.data.id,
                    name: response.data.name,
                    email: response.data.email
                }
            }
            context.commit('put_settings_in_store', user_object);
            context.commit('set_data_ready', true);
            context.commit('update_state');
        }).catch((e) => {
            console.log(e);
            context.dispatch('after_logout');
        })
    },

    async logout(context) {
        if (context.getters.authenticated) {
            try {
                await axios.post('/api/auth/logout').then(() => {
                    context.dispatch('after_logout');
                });
            } catch (e) {
                await context.dispatch('after_logout');
            }
        }
    },

    after_logout(context) {
        // force user out by removing cookie and user data
        Cookies.remove('token');
        context.commit('delete_user');
        window.location.href = '/';
    }
}

const getters = {
    authenticated: (state) => state.authenticated,
    user_data_ready: (state) => state.data_ready,
    user_id: (state) => state.id,
    user_name: state => state.name,
    fully_authenticated: (state, getters) => getters.authenticated && getters.user_data_ready
}

export default {
    state: state,
    mutations,
    actions,
    getters
}
