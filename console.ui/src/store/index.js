import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    activemain: '',
    spider: ''
  },
  mutations: {
    activemain (state, activemain) {
      state.activemain = activemain
    },
    spider (state, spider) {
      state.spider = spider
    }
  }
})
