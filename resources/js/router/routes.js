import EcStore from '../components/EcStore';
import EcLogin from '../components/EcLogin';
import EcRegister from '../components/EcRegister';

export default [
    {
        path: '/',
        name: 'store',
        component: EcStore,
        meta: {} // for stuff like defining role access and other meta data
    },
    {
        path: '/login',
        name: 'login',
        component: EcLogin,
        meta: {}
    },
    {
        path: '/register',
        name: 'register',
        component: EcRegister,
        meta: {}
    },
]
