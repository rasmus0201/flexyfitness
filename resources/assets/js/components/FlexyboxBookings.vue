<template>
    <div class="bookings">
        <v-layout justify-content-center align-items-center py-0 class="bookings__title">
            Tilmeldinger
        </v-layout>

        <loader :loading="loading" class="mt-3"></loader>
        <template v-if="!loading">
            <v-layout column v-for="(day, indexA) in bookings" :key="`day-${indexA}`">
                <v-card v-if="day.bookings.length" class="mb-2 mt-3" width="100%">
                    <v-card-title>
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
    </div>
</template>

<script>
    import _ from 'lodash';

    export default {
        data() {
            return {
                loading: false,
                bookings: []
            }
        },

        mounted() {
            this.getBookings();
        },

        methods: {

            getBookings() {
                const url = '/api/user/activities';

                this.loading = true;

                axios.post(url, { token: this.$store.state.token })
                    .then(response => {
                        const items = response.data.data;

                        this.bookings = items;
                    })
                    .catch(error => {

                        // TODO Display snackbar error
                        console.log(error);
                    })
                    .finally(() => {
                        this.loading = false;
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
