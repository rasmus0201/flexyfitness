<template>
    <v-card>
        <v-card-title primary-title>
            <div class="w-100">
                <h3 class="headline mb-0">Log ind på holdtilmeldningen</h3>
                <div>
                    <form>
                        <v-text-field
                            v-model="username"
                            v-validate="'required'"
                            name="username"
                            data-vv-name="username"
                            :error-messages="errors.collect('username')"
                            :label="dictionary.attributes.username"
                            :disabled="loading"
                            required
                        ></v-text-field>

                        <v-text-field
                            v-model="password"
                            v-validate="'required'"
                            data-vv-name="password"
                            name="password"
                            type="password"
                            :error-messages="errors.collect('password')"
                            :label="dictionary.attributes.password"
                            :disabled="loading"
                            required
                        ></v-text-field>
                    </form>
                </div>
            </div>
        </v-card-title>
        <v-card-actions class="pa-3">
            <v-btn color="primary" @click="submit" :loading="loading" :disabled="loading">Log ind</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
    export default {
        $_veeValidate: {
            validator: 'new'
        },

        data: () => ({
            username: '',
            password: '',
            loading: false,
            dictionary: {
                attributes: {
                    username: 'Email/Telefon/Medlemsnr',
                    password: 'Password'
                },
                custom: {
                    username: {
                        required: () => 'Skal udfyldes'
                    },
                    password: {
                        required: () => 'Skal udfyldes'
                    },
                }
            }
        }),

        mounted () {
            this.$validator.localize('en', this.dictionary);
        },

        methods: {
            submit () {
                this.$validator.validateAll().then(result => {
                    if (!result) {
                        return;
                    }

                    this.auth();
                });
            },

            notify(msg, color) {
                this.$store.commit('notify', {
                    msg, color
                });
            },

            auth() {
                this.loading = true;

                axios.post('/api/user/auth', { username: this.username, password: this.password })
                    .then(response => {
                        this.$emit('login', response.data.data.token);
                    })
                    .catch(error => {
                        this.notify('Forkert E-mail/Telefon/Medlemsnr eller adgangskode', 'error');
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            }
        }
    }
</script>
