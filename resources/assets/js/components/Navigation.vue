<template>
    <div>
        <v-toolbar clipped-left>
            <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
                <v-toolbar-title>
                    <router-link to="/" class="toolbar-title">
                        {{ appName }}
                    </router-link>
                </v-toolbar-title>

            <v-spacer></v-spacer>

            <v-btn v-if="auth" icon :disabled="refreshing" @click="refresh" title="Tving genindlæsning">
                <v-icon>refresh</v-icon>
            </v-btn>
            <v-btn
                v-for="item in navItems"
                v-if="(item.auth && auth) || (!item.auth && !auth)"
                :key="item.title"
                :to="{ name: item.route }"
                :title="item.title"
                exact
                replace
                icon
            >
                <v-icon>{{ item.icon }}</v-icon>
            </v-btn>
        </v-toolbar>

        <v-navigation-drawer
            v-model="drawer"
            temporary
            absolute
            :width="width"
            id="drawer"
        >
            <v-toolbar flat>
                <v-list>
                    <v-list-tile>
                        <v-list-tile-title class="title">
                            Menu
                        </v-list-tile-title>
                    </v-list-tile>
                </v-list>
            </v-toolbar>

            <v-divider></v-divider>

            <v-list dense class="pt-0">
                <v-list-tile
                    v-for="item in drawerItems"
                    v-if="(item.auth === null) || (item.auth && auth) || (!item.auth && !auth)"
                    :key="item.title"
                    :to="{ name: item.route }"
                    exact
                    replace
                >
                    <v-list-tile-action>
                        <v-icon>{{ item.icon }}</v-icon>
                    </v-list-tile-action>

                    <v-list-tile-content>
                        <v-list-tile-title>{{ item.title }}</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-navigation-drawer>
    </div>

</template>

<script>
    import EventBus from '../event-bus';

    export default {
        data() {
            return {
                drawer: null,
                width: 250,
                refreshing: false,
                appName: this.$store.state.appName
            }
        },

        mounted() {
            EventBus.$on('refresh-done', () => {
                this.refreshing = false;
            });
        },

        computed: {
            auth() {
                return this.$store.state.auth;
            },

            navItems() {
                return this.nav();
            },

            drawerItems() {
                return this.routes();
            }
        },

        methods: {
            refresh() {
                this.refreshing = true;

                EventBus.$emit('refresh', true);
            },

            nav() {
                return [
                    {
                        title: 'Log ind',
                        route: 'login',
                        icon: 'account_circle',
                        auth: false,
                        nav: true
                    },
                    {
                        title: 'Log ud',
                        route: 'logout',
                        icon: 'exit_to_app',
                        auth: true
                    }
                ]
            },

            routes() {
                return [
                    {
                        title: 'Log ind',
                        route: 'login',
                        icon: 'account_circle',
                        auth: false
                    },
                    {
                        title: 'Kalender',
                        route: 'calendar',
                        icon: 'calendar_today',
                        auth: true
                    },
                    {
                        title: 'Tilmeldninger',
                        route: 'bookings',
                        icon: 'event',
                        auth: true
                    },
                    {
                        title: 'Log ud',
                        route: 'logout',
                        icon: 'exit_to_app',
                        auth: true
                    },
                    {
                        title: 'Mine data',
                        route: 'mydata',
                        icon: 'account_circle',
                        auth: true
                    },
                    {
                        title: 'Privatliv',
                        route: 'privacy-policy',
                        icon: 'lock',
                        auth: null
                    }
                ];
            }
        }
    }
</script>
