import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './routes';
import store from '../store/index';

Vue.use(VueRouter);

const router = new VueRouter({
    routes: routes,
    mode: 'history'
});

router.initialize = async () => {
    router.beforeEach(async (to, from, next) => {
        // typically I'd use this to protect pages that require authentication
        if (to.name === 'login' && store.getters.authenticated) {
            return router.push('/dashboard');
        }

        return next();
    });
};

export default router;
