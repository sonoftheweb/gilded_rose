<template>
    <div>
        <v-app-bar color="orange" dark tile>
            <v-toolbar-title>Gilded Rose</v-toolbar-title>
            <v-spacer/>
            <v-menu v-if="fully_authenticated" close-delay="30" open-on-hover offset-y bottom right>
                <template v-slot:activator="{ on }">
                    <v-avatar color="white" size="36" v-on="on">
                        <span class="orange--text">{{ user_name_initials }}</span>
                    </v-avatar>
                </template>
                <v-list class="my-0 py-0">
                    <v-list-item class="pointer-cursor" @click.stop="logout">
                        <v-icon class="pr-2">mdi-account</v-icon>
                        <v-list-item-title>Logout</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-app-bar>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';
export default {
    computed: {
        ...mapGetters(["user_name_initials", "fully_authenticated"])
    },
    methods: {
        logout() {
            this.$store.dispatch('logout')
        }
    }
}
</script>
