<template>
    <div>
        <div v-if="view === 'week'" class="d-flex mb-4 flex-row flex-wrap p-0">
            <template column v-for="(day, indexA) in weekView">
                <span class="d-none" :key="`day-${indexA}`"></span>
                <template v-for="(activity, indexB) in day">
                    <div
                        :key="`day-${indexA}-activity-${indexB}`"
                        class="col-2-of-14 mb-2 flex-shrink-1"
                    >
                        <template v-if="activity !== undefined">
                            <activity :item="activity" :week="week"></activity>
                        </template>

                        <!-- Display an empty activity slot -->
                        <template v-else>
                            &nbsp;
                        </template>
                    </div>
                </template>
            </template>
        </div>

        <div v-else class="d-flex mb-4 flex-column p-0">
            <template column v-for="(activity, index) in dayView">
                <div
                    :key="`activity-${index}`"
                    class="col-12 mb-1"
                >
                    <template v-if="activity !== undefined">
                        <activity :item="activity" :week="week"></activity>
                    </template>

                    <!-- Display an empty activity slot -->
                    <template v-else>
                        &nbsp;
                    </template>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['view', 'week', 'date', 'items'],

        computed: {
            dayView() {
                for (const item of this.items) {
                    const week = item.items;

                    for (const day of week) {
                        if (this.$moment(this.date).isSame(day[0].date, 'day')) {
                            return day;
                        }
                    }
                }

                return [];
            },

            weekView() {
                for (const item of this.items) {
                    if (this.$moment(this.week).isSame(item.week, 'isoWeek')) {
                        return this.pivot(item.items);
                    }
                }

                return [];
            }
        },

        methods: {
            pivot(arr) {
                // Makes columns into rows
                return arr[0].map((_, i) => arr.map(row => row[i]));
            }
        }
    }
</script>
