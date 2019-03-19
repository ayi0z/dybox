import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    motoken: '',
    playlist: null,
    playing: null,
    noticename: null
  },
  mutations: {
    changeMoToken (state, motoken) {
      state.motoken = motoken
    },
    updatePlaylist (state, playlist) {
      state.playlist = playlist
    },
    pushPlaylist (state, playlist) {
      state.playlist.push(...playlist)
    },
    updatePlaying (state, playing) {
      state.playing = playing
    },
    loadNotice (state, nname) {
      state.noticename = nname
    },
    unloadNotice (state) {
      state.noticename = null
    }
  }
})
