<template>
  <div class="search">
    <div class="search-input">
      <input type="text"
        placeholder="输入6位观影密令即可观看影片"
        maxlength="6"
        v-model="motoken"
        @keyup.enter="handlePlayMoToken"/>
    </div>
    <div class="search-icon-play iconfont"
      @click="handlePlayMoToken"
      @keyup.enter="handlePlayMoToken">&#xe625;</div>
  </div>
</template>

<script>
import qs from 'qs'
export default {
  name: 'Search',
  data: function () {
    return {
      motoken: ''
    }
  },
  methods: {
    handlePlayMoToken () {
      this.$store.commit('changeMoToken', '')
      this.$store.commit('updatePlaylist', null)
      this.$store.commit('updatePlaying', null)
      if (this.motoken && this.motoken.split(' ').join('').length === 6) {
        this.$store.commit('loadNotice', 'loadmedias')
        this.$axios.post('./api/client/medias.php', qs.stringify({
          mo: this.motoken, offset: 0, pagesize: 30
        })).then(this.handleMediaList).catch(() => {
          this.$store.commit('loadNotice', 'reqfail')
        })
      } else {
        this.$store.commit('loadNotice', 'invalidmotoken')
      }
    },
    handleMediaList (res) {
      let resdata = res.data
      if (resdata.code === 1 && resdata.data) {
        let playlist = resdata.data
        let len = playlist.length
        if (len > 0) {
          playlist.sort((a, b) => {
            return b.hot - a.hot
          })
          playlist.sort((a, b) => {
            return b.year - a.year
          })
          this.$store.commit('changeMoToken', this.motoken)
          this.$store.commit('updatePlaylist', playlist)
          if (len === 1) {
            // 只有一个影片，直接播放
            this.$store.commit('updatePlaying', playlist[0])
          }
          this.$store.commit('unloadNotice')
          return
        }
      }
      this.$store.commit('loadNotice', 'invalidmotoken')
    }
  }
}
</script>

<style lang="stylus" scoped>
.search
  position fixed
  display: flex
  justify-content: center
  height: 1rem
  line-height: 1rem
  font-size: .1rem
  color: #F2F2F2
  background-color: #373C50
  box-shadow: 0 0 .05rem 0.01rem #4ea7f185
  .search-input
    flex: 0 1 auto
    vertical-align: middle
    input
      width: 3.3rem
      padding: .1rem .3rem
      background-color: #1723386b
      border: none
      color: #a2b2d4
      text-transform: uppercase
      text-align: center
      outline: none
      border-radius: 1rem
      box-shadow: 0 0 0.1rem #ffffff1c
      &:focus
        box-shadow: inset 0 0 0.1rem rgb(132, 203, 255)
  .search-icon-play
    width: 1rem
    text-align: center
    display: inline-block
    cursor: pointer
    border-radius: 1rem
    &:hover
      background-color: #2f446999
      text-shadow: 0 0 0.2rem #0be7fd
</style>
