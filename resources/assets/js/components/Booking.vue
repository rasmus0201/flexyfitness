<template>
    <v-card class="ml-4">
        <v-card-title>
            <v-flex xs8 sm9 md10>
                {{ item.time }}:&nbsp;{{ item.name }}
                <span v-if="item.waitList">&nbsp;(Venteliste: {{ item.waitList }})</span>
            </v-flex>
            <v-flex xs4 sm3 md2 class="text-right">
                <v-btn
                    :disabled="loading"
                    :loading="loading"
                    @click="leave()"
                    class="my-0"
                    small
                >
                    Frameld
                </v-btn>
            </v-flex>
        </v-card-title>
    </v-card>
</template>

<script>
    export default {
        props: {
            item: { required: true }
        },

        data() {
            return {
                loading: false
            }
        },

        methods: {
            leave() {
                const url = '/api/user/activities/leave';

                this.toggleLoading();

                axios.post(url, { token: this.$store.state.token, bookingId: this.item.id })
                    .finally(() => {
                        this.toggleLoading();

                        this.$emit('left', this.item.id);
                    });
            },

            toggleLoading() {
                this.loading = !this.loading;
            }
        }
    }
</script>
