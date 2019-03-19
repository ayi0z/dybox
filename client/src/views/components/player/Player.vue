<template>
  <div class="player-container">
    <div class="player">
      <screen v-if="readyplay"
        :videourl="playingepisode ? playingepisode.url : ''"
        :gateurl="playgate ? playgate.gateurl : ''">
      </screen>
    </div>
    <media-info class="playing-info" v-if="mediainfo" :mediainfo="mediainfo">info</media-info>
    <play-gate-and-episodes
      v-if="showepisodes || showplaygate"
      :playingepid="playingepisode ? playingepisode.epid : ''"
      :episodeslist="episodeslist ? episodeslist : []"
      @switchepisodes="handleswitcheplayepisodes"
      :playgateid="playgate ? playgate._id.$oid : ''"
      :playgatelist="playgatelist ? playgatelist : []"
      @switchplaygate="handleswitchplaygate"
    ></play-gate-and-episodes>
  </div>
</template>

<script>
import qs from 'qs'
import Screen from './Screen'
import MediaInfo from './MediaInfo'
import PlayGateAndEpisodes from './PlayGateAndEpisodes'
export default {
  name: 'Player',
  data () {
    return {
      playingepisode: null,
      episodeslist: [],
      playgate: null,
      playgatelist: [],
      lasthotmid: null
    }
  },
  components: {
    MediaInfo,
    Screen,
    PlayGateAndEpisodes
  },
  computed: {
    readyplay () {
      return this.playingepisode && this.playingepisode.url && this.playgate && this.playgate.gateurl
    },
    mediainfo () {
      return this.$store.state.playing
    },
    showepisodes () {
      return this.episodeslist && this.episodeslist.length > 1
    },
    showplaygate () {
      return this.playgatelist && this.playgatelist.length > 1
    }
  },
  mounted () {
    this.queryEpisodesList()
    this.hot()
  },
  watch: {
    playingepisode () {
      this.playgatelist = this.playingepisode ? this.playingepisode.gate : []
      this.playgate = this.playgatelist[0]
    },
    mediainfo () {
      this.queryEpisodesList()
      this.hot()
    }
  },
  methods: {
    hot () {
      if (this.mediainfo._id.$oid && this.lasthotmid !== this.mediainfo._id.$oid) {
        this.lasthotmid = this.mediainfo._id.$oid
        this.$axios.post('./api/client/hot.php', qs.stringify({
          mid: this.mediainfo._id.$oid
        }))
      }
    },
    queryEpisodesList () {
      this.episodeslist = []
      this.playingepisode = null
      if (this.mediainfo && this.mediainfo._id && this.mediainfo._id.$oid) {
        this.$store.commit('loadNotice', 'loadepisodes')
        this.$axios.post('./api/client/medias.php', qs.stringify({
          mid: this.mediainfo._id.$oid
        })).then(this.handleEpisodesList).catch(() => {
          this.$store.commit('loadNotice', 'reqfail')
        })
      }
    },
    handleEpisodesList (res) {
      let resdata = res.data
      if (resdata.code === 1 && resdata.data) {
        this.episodeslist = resdata.data
        if (this.episodeslist.length > 0) {
          // 自动播放 第一分集
          this.episodeslist.sort((ep1, ep2) => {
            return Number(ep1.title) - Number(ep2.title)
          })
          this.playingepisode = this.episodeslist[0]
          this.$store.commit('unloadNotice')
          return
        }
      }
      this.$store.commit('loadNotice', 'noepisodes')
    },
    handleswitcheplayepisodes (eid) {
      if (!this.playingepisode || this.playingepisode.epid !== eid) {
        this.playingepisode = this.episodeslist ? this.episodeslist.filter((ei) => {
          return ei.epid === eid
        })[0] : null
      }
    },
    handleswitchplaygate (apiid) {
      if (!this.playgate || this.playgate._id.$oid !== apiid) {
        this.playgate = this.playgatelist ? this.playgatelist.filter((ei) => {
          return ei._id.$oid === apiid
        })[0] : null
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
  .player-container
    .player
      overflow: hidden
      width: auto
      height: 100%
      margin: 0 .2rem
      background-color: #20222B
      border-radius: .05rem .05rem 0 0
      box-shadow: 0 .05rem 1.1rem #00000085
    .playing-info
      width: auto
      margin: auto 0.2rem
      background-color: #46465057
      box-shadow: 0 0.03rem 0.4rem rgba(0,0,0,.4)
</style>
