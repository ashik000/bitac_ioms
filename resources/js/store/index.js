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
       }
   },
   actions: {
       addAuthProperties(context, auth) {
           context.commit('addAuthProperties', auth);
       },
       selectedStationId({ commit }, stationId) {
           commit('UPDATE_STATION_ID', stationId);
       }
   }
});
