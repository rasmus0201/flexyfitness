<template>
    <v-container>
        <v-layout>
            <v-flex xs12>
                <v-layout justify-content-center align-items-center py-0 class="mydata__title">
                    Mine data
                </v-layout>

                <loader :loading="loading" class="mt-3"></loader>
                <template v-if="!loading">
                    <v-layout column>
                        <v-btn color="primary" class="mx-0 mt-4 mb-1" @click="download">Download alle data</v-btn>
                        <v-btn color="error" class="mx-0" @click="remove" :disabled="isDeleting">Slet alle data</v-btn>
                    </v-layout>
                    <p class="mb-1 font-size-5">Brugerdata:</p>
                    <pre class="mydata__json">{{ mydata.user }}</pre>
                    <small>
                        <v-icon small class="text-warning">warning</v-icon>
                        Passwordhashes vises ikke!
                    </small>
                    <p class="mt-3 mb-1 font-size-5">Holdtilmeldings-data:</p>

                    <pre class="mydata__json">{{ mydata.bookings }}</pre>
                    <p class="mt-3 mb-1 font-size-5">Kalender-data:</p>


<!-- Begin Weird indetion -->
<template v-if="mydata.calendars.length">
<pre class="mydata__json">[
  {
    &hellip;
  },
  &hellip;
]</pre>
    <small>
        <v-icon small class="text-warning">warning</v-icon>
        Der er for meget data til visning - download i stedet.
    </small>
</template>
<pre v-else>{{ mydata.calendars }}</pre>
<!-- Stop Weird indetion -->


                </template>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import FileSaver from 'file-saver';
    import EventBus from '../event-bus';

    export default {
        data() {
            return {
                loading: true,
                isDeleting: false,
                mydata: []
            }
        },

        mounted() {
            this.fetch();

            EventBus.$on('refresh', this.fetch);
        },

        methods: {
            fetch() {
                this.loading = true;
                const url = '/api/user/mydata';

                axios.post(url, { token: this.$store.state.token })
                    .then(response => {
                        this.mydata = response.data.data;
                    })
                    .catch(error => {
                        this.notify('Der skete en fejl', 'error');
                    })
                    .finally(() => {
                        this.loading = false;
                        EventBus.$emit('refresh-done');
                    });
            },

            download() {
                if (navigator.userAgent.match(/(FlexyfitnessApp)/g)) {
                    window.location.href = '/api/user/mydata?token=' + this.$store.state.token;
                    return;
                }

                const file = new Blob([JSON.stringify(this.mydata)], {
                    type: 'application/json;charset=utf-8'
                });

                FileSaver.saveAs(file, 'mydata.json');
            },

            remove() {
                this.isDeleting = true;
                const url = '/api/user/delete';

                axios.post(url, { token: this.$store.state.token })
                    .then(response => {
                        this.$router.push({name: 'logout'});
                    })
                    .catch(error => {
                        this.notify('Der skete en fejl', 'error');
                    })
                    .finally(() => {
                        this.isDeleting = false;
                    });
            },

            notify(msg, color) {
                this.$store.commit('notify', {
                    msg, color
                });
            }
        }
    }
</script>
