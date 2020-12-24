<template>
    <v-app>
        <ec-alerts :alert-data="alertData"/>
        <ec-header/>
        <v-main>
            <v-container class="fill-height pa-0">
                <transition name="fade" mode="out-in">
                    <v-layout>
                        <v-flex xs12>
                            <router-view></router-view>
                        </v-flex>
                    </v-layout>
                </transition>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
import {mapGetters} from 'vuex'
import EcHeader from "./components/Shared/EcHeader";
import EcAlerts from "./components/Shared/EcAlerts";
export default {
    components: {EcAlerts, EcHeader},
    data() {
        return {
            alertData: {},
            appName: 'Ecommerce Store'
        }
    },
    computed: {
        ...mapGetters(['fully_authenticated', 'user_data_ready', 'user_id'])
    },
    methods: {
        changeTitle(route) {
            let prettyRouteName = route.name;
            document.title = this.appName + " - " + prettyRouteName;
        },
    },
    mounted() {
        this.$eventBus.$on('alert', alertData => {
            this.alertData = alertData
        })

        setTimeout(() => {
            this.changeTitle(this.$route)
        }, 1000)

        this.$router.beforeEach((to, from, next) => {
            this.changeTitle(to);
            next();
        });
    }
}
</script>
