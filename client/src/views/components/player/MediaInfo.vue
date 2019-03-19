<template>
  <div class="playinginfo">
    <div class="playinginfo-title">
      <div class="playinginfo-maintitle" v-show="mediainfo.title">{{mediainfo.title}}</div>
      <div class="playinginfo-subtitle" v-show="mediainfo.title_en">{{mediainfo.title_en}}</div>
    </div>
    <div class="playinginfo-tag">
      <span class="pit-year" v-show="mediainfo.year">{{mediainfo.year}}</span>
      <span v-show="mediainfo.area">{{mediainfo.area}}</span>
      <span v-show="mediainfo.langue">{{mediainfo.langue}}</span>
      <span v-show="mediainfo.subtype" v-for="subt in mediainfo.subtype" :key="subt">{{subt}}</span>
      <span class="pit-hot" v-show="mediainfo.hot"><i class="iconfont">&#xe6a0;</i>{{mediainfo.hot}}</span>
    </div>
    <div class="playinginfo-worker" @click.stop="handleToogleShow()">
      <div v-show="mediainfo.director">导演：{{mediainfo.director.join()}}</div>
      <div v-show="mediainfo.actor"
        :class="{'playinginfo-worker-shortshow':isshortstaring}">主演：{{mediainfo.actor.join()}}</div>
      <div v-show="mediainfo.desc"
        :class="{'playinginfo-worker-shortshow':isshortstaring}">简介：{{mediainfo.desc}}</div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MediaInfo',
  data () {
    return {
      isshortstaring: true
    }
  },
  props: {
    mediainfo: Object
  },
  methods: {
    handleToogleShow () {
      this.isshortstaring = !this.isshortstaring
    }
  }
}
</script>

<style lang="stylus" scoped>
  .playinginfo
    position: relative
    font-size: 20%
    color: #becfdc
    padding: .2rem
    .playinginfo-title
      letter-spacing: .03rem
      .playinginfo-maintitle
        padding: .1rem
        font-size: .3rem
        text-shadow: 0 0.05rem 0.23rem #0000009e
      .playinginfo-subtitle
        padding: 0 .1rem
        font-size: .2rem
        vertical-align: sub
    .playinginfo-tag
      position: relative
      padding: .1rem
      span
        color: #a9baca
        font-size: .1rem
        padding: .02rem .05rem
        border: solid .01rem #364d63
        border-radius: 0.03rem
        font-family: "Times New Roman", Times, serif
        font-weight: lighter;
        margin-right: .03rem
        border-collapse: collapse
        display: inline-block
      .pit-year
        color: #ce6a71
        border-color: #ce6a71
      .pit-hot
        border: none
        font-size: .2rem
        color: #bb1f1f
    .playinginfo-worker
      letter-spacing: .01rem
      line-height: .3rem
      padding: .1rem
      font-size: .2rem
      div
        margin: .1rem auto
        cursor: pointer
      .playinginfo-worker-shortshow
        text-align: justify
        overflow: hidden
        width: auto
        white-space: nowrap
        text-overflow: ellipsis
</style>
