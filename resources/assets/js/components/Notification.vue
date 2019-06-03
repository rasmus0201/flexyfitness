<template>
    <v-snackbar
        v-model="snackbar"
        :multi-line="mode === 'multi-line'"
        :color="color"
        :timeout="0"
        right
        top
        absolute
    >
        {{ text }}
        <v-btn
            color="pink"
            flat
            @click="closeNotification()"
            >
            Luk
        </v-btn>
    </v-snackbar>
</template>

<script>
    export default {
        props: {
            show: { required: true },
            text: { required: true },
            color: { default: 'blue-grey darken-4' },
            timeout: { default: 6000 }
        },

        data () {
            return {
                mode: 'multi-line',
                snackbar: this.show,
                timer: null
            }
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
                this.$emit('close');
            }
        }
    }
</script>
