<template>
    <v-container>
        <v-layout>
            <v-flex xs12>
                Du bliver logget ud nu...
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    export default {
        created() {
            this.$store.commit('logout');
            this.$router.push({name: 'login'});
        }
    }
</script>
