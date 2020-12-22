<template>
    <div>
        <v-app-bar color="orange" dark tile>
            <v-container class="pa-0">
                <v-toolbar-title>Gilded Rose</v-toolbar-title>
                <v-spacer/>
            </v-container>
        </v-app-bar>
    </div>
</template>

<script>
export default {
    props: {
        isAuthenticated: false
    }
}
</script>
