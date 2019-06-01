<template>
    <v-container>
        <v-layout>
            <v-flex xs12 sm6 offset-sm3>
                <login-form @login="login($event)"></login-form>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    export default {
        methods: {
            login(token) {
                this.$store.commit('login', token);
                this.$router.push({name: 'calendar'});
            }
        }
    }
</script>
