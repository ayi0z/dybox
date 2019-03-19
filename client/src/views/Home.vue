<template>
    <div class="home">
        <search class="search-normal" :class="searchClass"></search>
        <notice-bar class="noticebar" v-show="shownotice"></notice-bar>
        <welcome class="welcome" v-show="showwelcome"></welcome>
        <player class="player" v-if="isplaying"></player>
        <play-list class="playlist" v-if="hasplaylist"></play-list>
        <footer-mark class="footer"></footer-mark>
    </div>
</template>

<script>
import Search from './components/header/Search'
import NoticeBar from './components/header/NoticeBar'
import Welcome from './components/Welcome'
import Player from './components/player/Player'
import PlayList from './components/player/PlayList'
import FooterMark from './components/footer/Footer'
export default {
  name: 'Home',
  computed: {
    searchClass () {
      return (this.isplaying || this.hasplaylist) ? 'search-top' : 'search-middle'
    },
    isplaying () {
      return this.$store.state.playing
    },
    hasplaylist () {
      return this.$store.state.playlist
    },
    showwelcome () {
      return !(this.isplaying || this.hasplaylist)
    },
    shownotice () {
      return this.$store.state.noticename
    }
  },
  components: {
    Search,
    NoticeBar,
    Welcome,
    Player,
    PlayList,
    FooterMark
  }
}
</script>

<style lang="stylus" scoped>
  .home
    position: relative
    min-height: 100%
    margin-top: 0
    padding-top: 1.2rem
    .search-normal, .noticebar
      position: fixed
      margin: auto
      margin-top: 0rem
      right: 0
      left: 0
      top: 0
      z-index: 2
      background-color: #1f205057
      box-shadow: 0 0 8.1rem 0.6rem rgba(122, 116, 202, 0.32)
    .noticebar
      height: 1rem
      line-height: 1rem
      box-shadow: 0 0 1.6rem 6px rgb(106, 144, 220) inset
      background-color: #3d4a5c
    .player
      margin-bottom: .2rem
    .playlist, .welcome
      padding-bottom: 1.5rem
    .footer
      position: absolute
      bottom:0
      width: 100%
      margin-bottom: 0
</style>
