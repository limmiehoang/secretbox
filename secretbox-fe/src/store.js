import Vue from "vue";
import Vuex from "vuex";
import auth from "./auth/authService";
import MainVue from "./main.js"

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    joinedGroups: [],
    newGroups: []
  },
  mutations: {
    SET_GROUPS (state, groups) {
      state.joinedGroups = groups.joinedGroups;
      state.newGroups = groups.newGroups;
    }
  },
  actions: {
    async loadGroups ({ commit }) {
      try {
        const accessToken = await auth.getAccessToken();

        MainVue.$http
          .get('api/groups', {
            headers: {
              Authorization: `Bearer ${accessToken}`
            }
          })
          .then(response => {
            if (response.body.success) {
              const groups = response.body.data;
              commit('SET_GROUPS', groups)
            }
          });
      }
      catch(e) {
        console.log(e);
      }
    }
  }
});
