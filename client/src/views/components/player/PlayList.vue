<template>
  <div class="playlist">
    <play-list-item class="playlist-item" v-for="item in playlist" :playlistitem="item" :key="item.id"></play-list-item>
    <infinite-loading spinner="waveDots" @infinite="infiniteHandler">
      <div slot="no-more">(๑•̀ㅂ•́)و✧, 更多最新电影请关注公众号“ymlshow”</div>
      <div slot="no-results">(๑•̀ㅂ•́)و✧, 更多最新电影请关注公众号“ymlshow”</div>
      <div slot="error">(°ー°〃), 糟糕! GG啦!!!</div>
    </infinite-loading>
  </div>
</template>

<script>
import PlayListItem from './PlayListItem'
import InfiniteLoading from 'vue-infinite-loading'
import qs from 'qs'
export default {
  name: 'PlayList',
  data () {
    return {
      playlist: this.$store.state.playlist,
      offset: 0,
      pagesize: 10
    }
  },
  computed: {
    motoken () {
      return this.$store.state.motoken
    }
  },
  mounted () {
    this.offset = this.$store.state.playlist.length
  },
  methods: {
    infiniteHandler ($state) {
      if (this.motoken && this.motoken.split(' ').join('').length === 6) {
        this.$axios.post('./api/client/medias.php', qs.stringify({
          mo: this.motoken, offset: this.offset, pagesize: this.pagesize
        })).then(res => {
          if (res.data.code === 1 && res.data.data) {
            this.$store.commit('pushPlaylist', res.data.data)
            this.offset = this.offset + res.data.data.length

            if (res.data.data.length < this.pagesize) {
              $state.complete()
            } else {
              $state.loaded()
            }
          } else {
            $state.complete()
          }
        }).catch(() => {
          $state.error()
        })
      }
    }
  },
  components: {
    PlayListItem,
    InfiniteLoading
  }
}
</script>

<style lang="stylus" scoped>
  .playlist
    font-size: 20%
    color: #becfdc
    padding: .2rem
    overflow: hidden
    .playlist-item
      margin-bottom: .2rem
</style>
