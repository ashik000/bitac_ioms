import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        auth: {
            token: null,
            refreshToken: null,
            tokenExpiration: null,
            role: null
        },
        reportPageFilters: {
            selectedStationId: null,
            selectedStationProductId: null,
            selectedStationShiftId: null,
            selectedStationOperatorId: null,
            selectedStationTeamId: null,
        }
    },
    getters: {
        isLoggedIn(state) {
            return state.auth.token !== null;
        },
        isManager(state) {
            return state.auth.role === 'manager';
        },
        isAdmin(state) {
            return state.auth.role === 'admin';
        }
    },
    mutations: {
        addAuthProperties(state, auth) {
            state.auth.token = auth.token;
            state.auth.refreshToken = auth.refreshToken;
            state.auth.tokenExpiration = auth.tokenExpiration;
            state.auth.role = auth.role;
            if(!auth.token) {
                window.localStorage.removeItem('user_info');
            }else{
                window.localStorage.setItem('user_info', JSON.stringify(auth));
            }
        },
        UPDATE_STATION_ID(state, stationId) {
            state.reportPageFilters.selectedStationId = stationId;
        },
        UPDATE_STATION_PRODUCT_ID(state, stationProductId) {
            state.reportPageFilters.selectedStationProductId = stationProductId;
        },
        UPDATE_STATION_SHIFT_ID(state, stationShiftId) {
            state.reportPageFilters.selectedStationShiftId = stationShiftId;
        },
        UPDATE_STATION_OPERATOR_ID(state, stationOperatorId) {
            state.reportPageFilters.selectedStationOperatorId = stationOperatorId;
        },
        UPDATE_STATION_TEAM_ID(state, stationTeamId) {
            state.reportPageFilters.selectedStationTeamId = stationTeamId;
        }
    },
    actions: {
        addAuthProperties(context, auth) {
            context.commit('addAuthProperties', auth);
        },
        selectedStationId({ commit }, stationId) {
            stationId = stationId === '0' ? null : stationId;
            commit('UPDATE_STATION_ID', stationId);
        },
        selectedStationProductId({ commit }, stationProductId) {
            stationProductId = stationProductId === '0' ? null : stationProductId;
            commit('UPDATE_STATION_PRODUCT_ID', stationProductId);
        },
        selectedStationShiftId({ commit }, stationShiftId) {
            stationShiftId = stationShiftId === '0' ? null : stationShiftId;
            commit('UPDATE_STATION_SHIFT_ID', stationShiftId);
        },
        selectedStationOperatorId({ commit }, stationOperatorId) {
            stationOperatorId = stationOperatorId === '0' ? null : stationOperatorId;
            commit('UPDATE_STATION_OPERATOR_ID', stationOperatorId);
        },
        selectedStationTeamId({ commit }, stationTeamId) {
            stationTeamId = stationTeamId === '0' ? null : stationTeamId;
            commit('UPDATE_STATION_TEAM_ID', stationTeamId);
        }
    }
});
