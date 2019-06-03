<template>
    <v-card
        class="calendar__card rounded-0 d-inline-flex flex-column justify-content-between"
        :flat="item.special"
        :class="{
            'primary': item.special && isToday(),
            'success': item.isJoined,
            'grey': item.isFutureActivity,
            'text-white': item.isJoined || item.isFutureActivity || (item.special && isToday()),
            'faded': item.disallowBooking,
            'elevation-1': !item.special
        }"
        :title="item.disallowBooking ? 'Holdet kan ikke bookes' : ''"
    >
        <template v-if="item.special">
            <v-card-text class="px-2 py-1">
                <span>{{ item.heading.day }}</span>
                <p class="mb-0 h1 font-weight-light">{{ item.heading.date }}</p>
            </v-card-text>
        </template>
        <template v-else>
            <v-card-text class="px-2 py-1">
                <p class="text-wrap font-size-4 font-weight-normal">{{ item.name }}</p>
                <p v-if="(item.end - item.start) > 0">
                    {{ item.start | moment('HH:mm') }} - {{ item.end | moment('HH:mm') }}
                </p>
                <p v-if="(item.end - item.start) === 0">{{ item.start | moment('HH:mm') }}</p>
                <div class="font-size-3">
                    <span v-if="item.isCancelled" class="pb-1 d-block text-danger font-weight-bold font-italic">Holdet er aflyst</span>
                    <template v-if="!item.isFutureActivity">
                        <span v-if="item.room" class="pb-1 d-block">Rum: {{ item.room }}</span>
                        <span v-if="item.trainerName" class="pb-1 d-block">Instruktør: {{ item.trainerName }}</span>
                        <span v-if="item.duration" class="pb-1 d-block">Varighed: {{ [item.duration, 'minutes'] | duration('humanize') }}</span>

                        <span v-if="item.isBooked" class="d-block">
                            Tilmeldte: {{ item.numJoined }} påventeliste.
                        </span>
                        <span v-else class="d-block">Tilmeldte: {{ item.numJoined }}</span>

                        <span v-if="item.isJoined" class="pt-1 d-block">Du er tilmeldt holdet</span>
                    </template>
                    <template v-else>
                        <span class="d-block">{{ item.isFutureActivity }}</span>
                    </template>
                </div>
            </v-card-text>

            <v-card-actions class="pa-2 align-items-end" v-if="hasAction()">
                <v-btn
                    @click="join()"
                    :loading="isLoading"
                    :disabled="isLoading || item.isJoined"
                    small
                    :flat="!isLoading"
                >
                    <span>{{ item.isJoined ? 'Tilmeldt' : 'Tilmeld' }}</span>
                </v-btn>
            </v-card-actions>
        </template>
    </v-card>
</template>

<script>
    export default {
        props: ['item', 'week'],

        data() {
            return {
                isLoading: false
            }
        },

        methods: {
            isToday() {
                if (!this.item.special) {
                    return false;
                }

                return this.$moment().utc().isSame(this.item.date, 'day');
            },

            hasAction() {
                return this.week !== undefined &&
                    this.item.isFutureActivity === false &&
                    this.item.disallowBooking === false &&
                    this.item.id !== undefined;
            },

            joinAllowed() {
                return this.item.isJoined === false && this.hasAction();
            },

            join() {
                if (!this.joinAllowed()) {
                    return;
                }

                this.toggleLoading();

                axios.post('/api/user/activities/join', {
                        activityId: this.item.id,
                        token: this.$store.state.token
                    })
                    .then(response => {
                        this.toggleLoading();
                        this.item.isJoined = true;
                    })
                    .catch(error => {
                        this.toggleLoading();

                        // TODO snackbar display error
                        console.log(error);
                    });
            },

            toggleLoading() {
                this.isLoading = !this.isLoading;
            }
        }
    }
</script>
