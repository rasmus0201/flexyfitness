<template>
    <v-snackbar
        v-model="snackbar"
        :multi-line="mode === 'multi-line'"
        :color="color"
        :timeout="0"
        class="darken-4"
        right
        top
        absolute
    >
        {{ message }}
        <v-btn
            :color="btnColor"
            flat
            @click="closeNotification()"
            >
            Luk
        </v-btn>
    </v-snackbar>
</template>

<script>
    export default {
        data () {
            return {
                snackbar: false,
                message: '',
                color: 'blue-grey',
                btnColor: 'white',
                timeout: 1750,
                mode: 'multi-line',
                timer: null
            }
        },

        created: function () {
            this.$store.watch(state => state.snack, () => {
                const {msg, color, btnColor} = this.$store.state.snack;

                if (msg !== '') {
                    this.setNotificationTimeout();

                    this.snackbar = true;
                    this.color = color;
                    this.message = msg;

                    if (btnColor) {
                        this.btnColor = btnColor;
                    }
                }
            });
        },

        mounted() {
            this.setNotificationTimeout();
        },

        watch: {
            show: function (newValue, oldValue) {
                this.setNotificationTimeout();
                this.snackbar = newValue;
            }
        },

        methods: {
            setNotificationTimeout() {
                this.clearNotificationTimeout();

                if (!this.timeout) {
                    return;
                }

                this.timer = setTimeout(() => {
                    this.closeNotification();
                }, this.timeout);
            },

            clearNotificationTimeout() {
                clearTimeout(this.timer);
            },

            closeNotification() {
                this.clearNotificationTimeout();
                this.snackbar = false;

                this.$store.commit('notify', {
                    msg: '',
                    color: ''
                });

                this.$emit('close');
            }
        }
    }
</script>
