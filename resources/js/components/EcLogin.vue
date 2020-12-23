<template>
    <div>
        <v-card max-width="500" class="mx-auto mt-10">
            <v-card-text>
                <div class="text-h3 font-weight-black mb-10">Login</div>
                <v-form ref="form" lazy-validation @submit.prevent="login">
                    <v-text-field
                        v-model="auth.email"
                        type="email"
                        label="Email"
                        placeholder="email@example.com"
                        prepend-inner-icon="mdi-at"
                        :rules="email_validation_rules"
                        class="mb-1"
                    />
                    <v-text-field
                        v-model="auth.password"
                        label="password"
                        placeholder="Password"
                        prepend-inner-icon="mdi-key-variant"
                        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        :type="showPassword ? 'text' : 'password'"
                        :rules="required_field_rule"
                        @click:append="showPassword = !showPassword"
                    />
                    <div class="mt-10">
                        <v-btn dark color="orange" class="mr-3" type="submit">Login</v-btn>
                    </div>
                </v-form>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';

export default {
    computed: {
        ...mapGetters(["email_validation_rules", "required_field_rule"])
    },
    data() {
        return {
            auth: {
                email: null,
                password: null,
                password_client: 1
            },
            showPassword: false
        }
    },
    methods: {
        login() {
            if (this.$refs.form.validate()) {
                this.$http.post('/api/authentication', this.auth).then(response => {
                    this.$store.dispatch('after_login', response.data).then(() => {
                        window.location.href = '/';
                    })
                }).catch(err => {
                    console.log('Encountered error: ', err)
                    this.$eventBus.$emit('alert', {
                        status: 'error',
                        message: 'We were unable to authenticate you with the credentials given. Please try again.'
                    })
                })
            }
        },
    }
}
</script>
