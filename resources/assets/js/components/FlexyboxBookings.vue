<template>
    <div class="bookings">
        <pull-to :top-load-method="refresh" :top-config="pullToConfig">
            <v-layout justify-content-center align-items-center py-0 class="bookings__title">
                Tilmeldinger
            </v-layout>

            <loader :loading="loading" class="mt-3"></loader>
            <template v-if="!loading">
                <v-layout column v-for="(day, indexA) in bookings" :key="`day-${indexA}`">
                    <v-card v-if="day.bookings.length" class="mb-2 mt-3" width="100%">
                        <v-card-title v-if="day.date == 'I morgen'">
                            I morgen
                        </v-card-title>
                        <v-card-title v-else>
                            {{ [day.date, 'DD/MM/YYYY'] | moment('ddd [d.] Do MMM [-] YYYY') }}
                        </v-card-title>
                    </v-card>

                    <booking v-for="(booking, indexB) in day.bookings" :key="`booking-${indexB}`" :item="booking" @left="remove($event)"></booking>
                </v-layout>
                <v-layout v-if="this.bookings.length === 0" mx-3 mt-3>
                    <v-alert :value="true" type="info" class="w-100">
                        Du er ikke tilmeldt nogen hold.
                    </v-alert>
                </v-layout>
            </template>
        </pull-to>
    </div>
</template>

<script>
    import PullTo from 'vue-pull-to';
    import _ from 'lodash';

    export default {
        data() {
            return {
                loading: false,
                bookings: [],
                pullToConfig: {
                    pullText: 'Henter',
                    triggerText: 'Genindlæs',
                    loadingText: 'Henter...',
                    doneText: '',
                    failText: 'Fejl',
                    loadedStayTime: 200
                }
            }
        },

        components: {
            PullTo
        },

        mounted() {
            this.getBookings();
        },

        methods: {
            refresh(loaded) {
                this.getBookings(true)
                    .then(() => {
                        loaded('done');
                    })
                    .catch(() => {
                        loaded('fail');
                    });
            },

            getBookings(force = false) {
                const url = '/api/user/activities';

                this.loading = true;

                return new Promise((resolve, reject) => {
                    axios.post(url, { token: this.$store.state.token, force: force })
                        .then(response => {
                            const items = response.data.data;

                            this.bookings = items;

                            resolve();
                        })
                        .catch(error => {
                            this.$store.commit('notify', {
                                msg: 'Der skete en fejl',
                                color: 'error'
                            });

                            reject();
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                });
            },

            remove(bookingId) {
                const { dayIndex, bookingIndex } = this.find(bookingId);

                if (bookingIndex !== -1 && dayIndex !== -1) {
                    this.bookings[dayIndex]['bookings'].splice(bookingIndex, 1);

                    // Clean up the days
                    if (this.bookings[dayIndex]['bookings'].length === 0) {
                        this.bookings.splice(dayIndex, 1);
                    }
                }
            },

            find(bookingId) {
                for (let i = 0; i < this.bookings.length; i++) {
                    const bookingIndex = this.bookings[i]['bookings'].map((x) => {
                        return x.id;
                    }).indexOf(bookingId);

                    if (bookingIndex !== -1) {
                        return { bookingIndex, dayIndex: i };
                    }
                }

                return { bookingIndex: -1, dayIndex: -1 };
            }
        }
    }
</script>
