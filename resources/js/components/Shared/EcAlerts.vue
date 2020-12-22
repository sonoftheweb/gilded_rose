<template>
    <v-alert
        elevation="0"
        @click="show = !show"
        v-if="alertData.status"
        class="ma-0 elevation-4 float-right"
        :type="alertData.status"
        :icon="alertIcon"
        width="40%"
        v-model="show"
        transition="scale-transition"
        style="cursor:pointer; position: fixed; top: 70px; z-index: 1000; right: 20px;"
        dismissible
    >{{ alertData.message }}</v-alert>
</template>

<script>
export default {
    props: ["alertData"],
    data() {
        return {
            alertIcon: "mdi-checkbox-marked-circle-outline",
            timeout: 5000,
            show: false
        }
    },
    watch: {
        alertData: function() {
            if (this.alertData.status === "error") {
                this.alertIcon = "mdi-alert-circle-outline"
            } else if (this.alertData.status === "success") {
                this.alertIcon = "mdi-checkbox-marked-circle-outline"
            } else if (this.alertData.status === "info") {
                this.alertIcon = "mdi-information-outline"
            } else if (this.alertData.status === "warning") {
                this.alertIcon = "mdi-alert-outline"
            }
            this.show = true;

            setTimeout(() => {
                this.show = false
            }, this.timeout)
        }
    },
}
</script>
