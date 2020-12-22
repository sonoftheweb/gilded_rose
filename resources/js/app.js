import './bootstrap';
import Vue from 'vue';
import App from './App.vue'
import vuetify from './plugins/vuetify';
import router from './router/router';
import store from './store/index';

Vue.config.productionTip = false;

store.dispatch('bootstrap_app').then(() => {
    router.initialize().then(() => console.info('Router ready'))
    new Vue({
        vuetify,
        router,
        store,
        render: h => h(App)
    }).$mount('#app')
});
