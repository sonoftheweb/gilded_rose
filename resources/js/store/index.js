import Vue from 'vue';
import Vuex from 'vuex';
import modules from './modules';
import axios from 'axios';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        locale: 'en'
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
        }
    },
    actions: {
        bootstrap_app(context) {
            // We could use this bit to get data from the DB to further alter the experience of the store.
            // Things like translations and default color are part of those data.
            if (context.getters.needs_user_data) {
                context.dispatch('fetch_user_data').then(() => console.info('User info ready...'));
            }
        },
    },
    getters: {
        required_field_rule: (state, getters) => {
            return [
                (v) => !!v || getters.__('messages.ui_required_field_validation_message'),
            ]
        },
        password_field_rule: (state, getters) => {
            return [
                (v) => !!(v || '').match(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/) || getters.__('messages.ui_password_rule'),
            ]
        },
        password_field_length_rule: (state, getters) => {
            return [
                len => v => (v || '').length >= len || `Invalid character length, required ${len}`,
            ]
        },
        email_validation_rules: (state, getters) => {
            return [
                (v) => !!v || getters.__('messages.ui_required_field_validation_message'),
                (v) => /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@(([[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(v) || getters.__('messages.ui_email_field_validation_message'),
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
