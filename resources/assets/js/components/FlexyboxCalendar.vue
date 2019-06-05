<template>
    <div class="calendar" v-resize="onResize">
        <pull-to :top-load-method="refresh" :top-config="pullToConfig">
            <v-layout justify-content-between align-items-center py-0 class="position-relative">
                <v-flex  p-0 xs12 sm3 md2 class="calendar__buttons">
                    <div>
                        <v-btn
                            class="mr-1 primary"
                            @click="prev()"
                            :loading="buttons.prev.loading"
                            :disabled="buttons.prev.disabled || buttons.prev.loading"
                            icon
                        >
                            <v-icon>chevron_left</v-icon>
                        </v-btn>
                        <v-btn
                            class="mr-1 primary"
                            @click="next()"
                            :loading="buttons.next.loading"
                            :disabled="buttons.next.disabled || buttons.next.loading"
                            icon
                        >
                            <v-icon>chevron_right</v-icon>
                        </v-btn>
                    </div>
                </v-flex>
                <v-flex p-0 xs12>
                    <div class="calendar__title" v-if="calendar.week">
                        {{ calendar.week | moment('[Uge] w, MMMM - Y') }}
                    </div>
                </v-flex>
                <v-flex p-0 class="calendar__options">
                    <v-menu bottom left>
                        <template v-slot:activator="{ on }">
                            <v-btn
                                v-on="on"
                                :color="'primary'"
                                icon
                            >
                            <v-icon>more_vert</v-icon>
                        </v-btn>
                        </template>

                        <v-list>
                            <v-list-tile
                                v-for="(option, index) in options"
                                :key="index"
                                @click="option.action()"
                            >
                                <v-list-tile-title>{{ option.title }}</v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>
                </v-flex>
            </v-layout>

            <loader :loading="loading"></loader>
            <flexybox-calendar-view
                v-if="!loading"
                :view="calendar.view"
                :week="calendar.week"
                :date="calendar.date"
                :items="calendar.items"
                v-hammer:swipe="swipe"
            ></flexybox-calendar-view>
        </pull-to>
    </div>
</template>

<script>
    import PullTo from 'vue-pull-to';
    import _ from 'lodash';

    export default {
        data() {
            return {
                title: '',
                isFetching: false, // If doing request
                loading: false, // Display loader
                calendar: {
                    view: 'day',
                    week: null,
                    date: null,
                    items: []
                },
                buttons: {
                    prev: {
                        disabled: false,
                        loading: false
                    },
                    next: {
                        disabled: false,
                        loading: false
                    }
                },
                options: [
                    {
                        title: 'I dag',
                        action: () => {
                            this.goToday();
                        }
                    }
                ],
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
            this.calendar.date = this.getToday();
            this.calendar.week = this.getMonday();

            this.getCalendar(this.calendar.week);

            this.onResize();
        },

        methods: {
            onResize: _.throttle(function () {
                this.calendar.view = window.innerWidth > 994 ? 'week' : 'day';
            }, 300),

            refresh(loaded) {
                this.loading = false;
                this.setButtonsState(true, true);

                this.getCalendar(this.calendar.week, true)
                    .then(() => {
                        loaded('done');
                    })
                    .catch(() => {
                        loaded('fail');
                    })
                    .finally(() => {
                        this.setButtonsState(false, false);
                    });
            },

            swipe(event) {
                switch(event.direction) {
                    case Hammer.DIRECTION_LEFT:
                        this.next();
                        break;
                    case Hammer.DIRECTION_RIGHT:
                        this.prev();
                        break;
                }
            },

            goToday() {
                this.loading = false;
                this.setButtonsState(false, false);

                this.calendar.date = this.getToday();

                this.syncWeek(0);
            },

            prev() {
                this.loading = false;
                this.setButtonsState(false, false);

                if (this.calendar.view === 'day') {
                    this.calendar.date = this.toMoment(this.calendar.date).add(-1, 'day');
                } else {
                    this.calendar.date = this.toMoment(this.calendar.date).add(-7, 'days');
                }

                this.syncWeek(-1);
            },

            next() {
                this.loading = false;
                this.setButtonsState(false, false);

                if (this.calendar.view === 'day') {
                    this.calendar.date = this.toMoment(this.calendar.date).add(1, 'day');
                } else {
                    this.calendar.date = this.toMoment(this.calendar.date).add(7, 'days');
                }

                this.syncWeek(1);
            },

            syncWeek(direction) {
                const sameWeek = this.$moment(this.calendar.week).isSame(this.calendar.date, 'isoWeek');

                if (sameWeek) {
                    return;
                }

                const newWeek = this.getMonday(this.calendar.date);
                this.calendar.week = newWeek;

                if (this.hasWeek(newWeek)) {
                    return;
                }

                if (direction <= 0) {
                    this.setButtonsState(true, null);
                }

                if (direction >= 0) {
                    this.setButtonsState(null, true);
                }

                this.getCalendar(newWeek)
                    .finally(() => {
                        this.setButtonsState(false, false);
                    });
            },

            getCalendar(week, force = false) {
                return new Promise((resolve, reject) => {
                    const url = '/api/user/calendar/' + (week / 1000);

                    // Check if an request is already happening, if so cancel
                    if (this.isFetching) {
                        this.loading = true; // Set loading spinner again

                        return reject('No concurrent requests please!');
                    }

                    this.loading = true;
                    this.isFetching = true;

                    axios.post(url, { token: this.$store.state.token, force: force })
                        .then(response => {
                            const items = this.toActivities(week, response.data.data);

                            this.calendar.items.push({week, items});

                            resolve({week, items});
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
                            this.isFetching = false;
                        });
                });
            },

            toActivities(week, items) {
                // Add properties to each activity
                for (let i = 0; i < items.length; i++) {
                    const timestamp = this.$moment(week).utc().add(i, 'days');

                    items[i].map(activity => {
                        activity.time = parseInt(activity.time);
                        activity.duration = parseInt(activity.duration);

                        activity.date = this.$moment(timestamp).utc().startOf('day');
                        activity.start = this.$moment(timestamp).utc().add(activity.time, 'minutes');
                        activity.end = this.$moment(timestamp).utc().add(activity.duration + activity.time, 'minutes');
                    });
                }

                // Add date headers
                return this.addCalendarHeaders(items);
            },

            addCalendarHeaders(week) {
                for (let i = 0; i < week.length; i++) {
                    let firstActivity = week[i][0];

                    const dayStr = this.$moment(firstActivity.date).format('ddd[.]');
                    const dateStr = this.$moment(firstActivity.date).format('D');

                    const element = {
                        special: true,
                        day: i,
                        date: firstActivity.date,
                        heading: {
                            day: dayStr[0].toUpperCase() + dayStr.substr(1),
                            date: dateStr
                        },
                    };

                    week[i] = [element, ...week[i]];
                }

                return week;
            },

            getToday() {
                return this.$moment().utc().startOf('day').unix() * 1000;
            },

            getMonday(date) {
                let monday = this.$moment();

                if (date) {
                    monday = this.$moment(date);
                }

                return monday.utc().startOf('isoWeek').unix() * 1000;
            },

            hasWeek(timestamp) {
                return !!this.calendar.items.filter(({week}) => {
                    return timestamp === week;
                }).length;
            },

            toMoment(timestamp) {
                return this.$moment(timestamp).utc();
            },

            setButtonsState(prev, next) {
                if (prev === true) {
                    this.buttons.prev.loading = true;
                } else if (prev === false) {
                    this.buttons.prev.loading = false;
                }

                if (next === true) {
                    this.buttons.next.loading = true;
                } else if (next === false) {
                    this.buttons.next.loading = false;
                }
            }
        }
    }
</script>
