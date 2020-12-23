import Vue from 'vue';
import Vuex from 'vuex';
import modules from './modules';
import axios from 'axios';
import Cookies from "js-cookie";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        authenticated: Cookies.get('access_token') !== undefined && Cookies.get('access_token') !== null && Cookies.get('access_token') !== '',
        access_token: Cookies.get('access_token') || '',
        data_ready: false
    },
    modules,
    mutations: {
        put_settings_in_store(state, data) {
            for (let config in data) {
                if (data.hasOwnProperty(config)) {
                    let configValue = data[config];

                    if (!configValue)
                        continue;

                    if (Object.prototype.hasOwnProperty.call(state, config)) {
                        Object.keys(configValue).forEach(conf => {
                            state[config][conf] = configValue[conf];
                        })
                    } else {
                        state[config] = configValue && configValue.data ? configValue.data : configValue;
                    }
                }
            }
        },
        update_state(state) {
            // Force a state refresh to put it to an empty object {} and then send it back to the previous state.
            let current_state = state;
            this.replaceState({});
            this.replaceState(current_state);
        },
        set_token(state, token) {
            state.access_token = token;
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
    },
    actions: {
        bootstrap_app(context) {
            // We could use this bit to get data from the DB to further alter the experience of the store.
            // Things like translations and default color are part of those data.
            if (context.getters.authenticated && !context.getters.user_data_ready) {
                context.dispatch('fetch_user_data').then(() => console.info('User info ready...'));
            }
        },

        after_login(context, data) {
            Cookies.set('access_token', data.access_token, {expires: data.expires_in});
            context.commit('set_token', data.access_token);
            context.commit('set_authenticated', true);
            context.dispatch('fetch_user_data');
        },

        async fetch_user_data(context) {
            await axios.get('/api/users/me').then(response => {
                let user_object = {
                    user: {
                        id: response.data.data.data.id,
                        name: response.data.data.data.name,
                        email: response.data.data.data.email
                    }
                }
                context.commit('put_settings_in_store', user_object);
                context.commit('set_data_ready', true);
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
            Cookies.remove('access_token');
            context.commit('delete_user');
            window.location.href = '/';
        }
    },
    getters: {
        authenticated: (state) => state.authenticated,
        user_data_ready: (state) => state.data_ready,
        user_id: (state) => state.id,
        user_name: state => state.name,
        fully_authenticated: (state, getters) => getters.authenticated && getters.user_data_ready,
        required_field_rule: (state, getters) => {
            return [
                (v) => !!v || 'Field required.',
            ]
        },
        password_field_rule: (state, getters) => {
            return [
                (v) => !!(v || '').match(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/) || 'Enter a valid password.',
            ]
        },
        password_field_length_rule: (state, getters) => {
            return [
                len => v => (v || '').length >= len || `Invalid character length, required ${len}`,
            ]
        },
        email_validation_rules: (state, getters) => {
            return [
                (v) => !!v || 'Field required.',
                (v) => /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@(([[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(v) || 'Enter a valid email.',
            ]
        },
        format_number: () => {
            return (number) => {
                const formatter = new Intl.NumberFormat('en-CA');
                return formatter.format(number);
            }
        },
        priceFormatted: () => {
            return price => {
                return new Intl.NumberFormat('en-CA', {style: 'currency', currency: 'CAD'}).format(price);
            }
        }
    }
})
