<template>
  <div class="pgae">
    <div  v-if="show_episodes">
      <span class="pgae-div pgae-episodes" v-for="item in episodeslist"
          :key="'episodes' + item.epid" :class="{'pgae-episodes-active': playingepid === item.epid}"
          @click='handleSwitchEpisodes(item.epid)'>{{item.title}}</span>
    </div>
    <span class="pgae-div pgae-hr" v-if="show_episodes && show_playgate"></span>
    <div v-if="show_playgate">
      <span class="pgae-div pgae-playgate" v-for="(item, index) in playgatelist"
          :key="'playgate' + item._id.$oid" :class="{'pgae-playgate-active': playgateid === item._id.$oid}"
          @click='handleSwitchPlayGate(item._id.$oid)'>线路{{++index}}</span>
    </div>
    <span class="pgae-div pgae-remind" v-if="show_playgate"><i class="iconfont">&#xe64c;</i>若无法播放，请尝试更换其他线路。</span>
  </div>
</template>

<script>
export default {
  name: 'PlayGateAndEpisodes',
  props: {
    playingepid: String,
    episodeslist: Array,
    playgateid: String,
    playgatelist: Array
  },
  computed: {
    show_episodes () {
      return this.episodeslist && this.episodeslist.length > 1
    },
    show_playgate () {
      return this.playgatelist && this.playgatelist.length > 1
    }
  },
  methods: {
    handleSwitchPlayGate (gid) {
      this.$emit('switchplaygate', gid)
    },
    handleSwitchEpisodes (eid) {
      this.$emit('switchepisodes', eid)
    }
  }
}
</script>

<style lang="stylus" scoped>
  .pgae
    width: auto
    font-size: .15rem
    color: #becfdc
    overflow-x: auto
    margin: 0 .2rem
    padding: .15rem
    letter-spacing: .01rem
    .pgae-div
      cursor: pointer
      float: left
      min-width:.5 rem
      padding-left: .2rem
      padding-right: .2rem
      height: .7rem
      line-height: .7rem
      border: none
      margin: 0
      text-align: center
    .pgae-hr
      width: .3rem
    .pgae-playgate
      &:hover
        box-shadow: 0 0 0.05rem #14fd2378
    .pgae-playgate-active
      box-shadow: 0 0 0.05rem #14fd2378
    .pgae-episodes
      &:hover
        box-shadow: 0 0 0.05rem #96c4e278
    .pgae-episodes-active
      box-shadow: 0 0 0.05rem #96c4e278
    .pgae-remind
      display: inline
      width: auto
      height: auto
      vertical-align: bottom
      font-size: .1rem
      color: #14fd2378
      cursor: default
      margin-left: 0.2rem
      .iconfont
        font-size: .1rem
        margin-right: .03rem
</style>
