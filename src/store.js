import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    uToken: null,
    pToken: null
  },
  mutations: {
    loadUserData(state, data){
      state.uToken = data;
      state.pToken = data;
    }
  },
  actions: {

  }
})
