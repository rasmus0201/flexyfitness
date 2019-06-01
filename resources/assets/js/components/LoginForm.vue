<template>
    <v-card>
        <v-card-title primary-title>
            <div class="w-100">
                <h3 class="headline mb-0">Log ind på holdtilmeldningen</h3>
                <div>
                    <form>
                        <v-text-field
                            v-model="user"
                            v-validate="'required'"
                            name="user"
                            data-vv-name="user"
                            :error-messages="errors.collect('user')"
                            :label="dictionary.attributes.user"
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
            user: '',
            password: '',
            loading: false,
            dictionary: {
                attributes: {
                    user: 'Email/Telefon/Medlemsnr',
                    password: 'Password'
                },
                custom: {
                    user: {
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

        computed: {
            token() {
                return btoa(this.user + ':' + this.password);
            },
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

            notify(msg) {
                this.$emit('auth', {
                    text: msg
                });
            },

            auth() {
                this.loading = true;

                axios.post('/api/user/auth', { token: this.token })
                    .then(response => {
                        this.notify(response.data.msg);
                        this.$emit('login', response.data.data.token);
                    })
                    .catch(error => {
                        this.notify(error.response.data.msg);
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            }
        }
    }
</script>
