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
       }
   },
   actions: {
       addAuthProperties(context, auth) {
           context.commit('addAuthProperties', auth);
       }
   }
});
