<template>
  <div class="playlist-item"
    :class="{'playlist-item-isplaying':isplaying, 'playlist-item-mousedown':ismousedown}"
    @click="handlePlay"
    @mousedown="handleMouseDown"
    @mouseup="handleMouseUp"
  >
    <div class="playlist-img" is-show="this.playlistitem.pic">
      <img :src="this.playlistitem.pic"
        referrer="no-referrer"
        :alt="this.playlistitem.title"
        :onerror="defaultImg"/>
    </div>
    <media-info class="playlist-item-mediainfo" :mediainfo="this.playlistitem"></media-info>
  </div>
</template>

<script>
import MediaInfo from './MediaInfo'
export default {
  name: 'PlayListItem',
  data () {
    return {
      defaultImg: 'this.src="' + require('@/assets/imgs/logo.png') + '"',
      ismousedown: false
    }
  },
  components: {
    MediaInfo
  },
  props: {
    playlistitem: null
  },
  computed: {
    isplaying () {
      return this.playlistitem && this.$store.state.playing && this.playlistitem._id.$oid === this.$store.state.playing._id.$oid
    }
  },
  methods: {
    handlePlay () {
      if (!this.isplaying) {
        this.$store.commit('updatePlaying', this.playlistitem)
      }
    },
    handleMouseDown () {
      this.ismousedown = true
    },
    handleMouseUp () {
      this.ismousedown = false
    }
  }
}
</script>

<style lang="stylus" scoped>
  .playlist-item
    position: relative
    display: flex
    font-size: 20%
    color: #becfdc
    overflow: hidden
    border-radius: .03rem
    .playlist-img
      img
        width: 100%
        max-width: 2rem
        vertical-align: text-top
        border-radius: .03rem
        box-shadow: 0 0.03rem 0.4rem rgba(0,0,0,.4)
  .playlist-item-isplaying
    box-shadow: 0 0 0.2rem #6db4e4
  .playlist-item-mediainfo
    flex: 1
    width: 1rem
  .playlist-item-mousedown
    box-shadow: 0 0rem .5rem #000000 inset
</style>
