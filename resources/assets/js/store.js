import Vue from 'vue';
import Vuex from 'vuex';
import VuexPersistence from 'vuex-persist';

Vue.use(Vuex);

export default new Vuex.Store({
    plugins: [new VuexPersistence().plugin],
    state: {
        auth: false,
        token: null,
        calendar: {
            week: null,
            date: null
        },
        appName: 'OBBC Holdtilmelding'
    },
    mutations: {
        login(state, token) {
            state.auth = true;
            state.token = token;
        },

        logout(state) {
            state.auth = false;
            state.token = null;
        },

        setWeek(state, week) {
            state.calendar.week = week;
        },

        setDate(state, date) {
            state.calendar.date = date;
        },
    }
});
