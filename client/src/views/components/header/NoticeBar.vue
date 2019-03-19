<template>
  <div class="noticebar" @click="closenoticebar">
    <div class="loading" v-show="notice && notice.type === 'loading'">
      <loading></loading>
    </div>
    <div class="notice" v-show="notice && notice.type === 'info'">
      <i class="iconfont" v-html="noticename ? icons[noticename] : ''"></i>
      {{notice ? notice.msgcontent : ''}}</div>
    <div class="action" @click.stop="action" v-show="notice && notice.type === 'action'">
      <i class="iconfont" v-html="noticename ? icons[noticename] : ''"></i>
      {{notice ? notice.msgcontent : '试试点这里～？'}}</div>
  </div>
</template>

<script>
import Loading from '../widget/Loading'
export default {
  name: 'NoticeBar',
  components: {
    Loading
  },
  data () {
    return {
      icons: {
        invalidmotoken: '&#xe64c;',
        reqfail: '&#xe69d;',
        noepisodes: '&#xe64c;',
        loadepisodes: '&#xe6a0;',
        loadmedias: '&#xe6a0;',
        gotoscreen: '&#xe691;'
      },
      noticelist: require('@/assets/js/noticebar.data.js')
    }
  },
  computed: {
    notice () {
      return this.noticelist.find(n => {
        return n.name === this.noticename
      })
    },
    noticename () {
      return this.$store.state.noticename
    }
  },
  mounted () {
    this.autoclosenoticebar()
  },
  updated () {
    this.autoclosenoticebar()
  },
  methods: {
    autoclosenoticebar () {
      if (this.notice && this.notice.type === 'info') {
        setTimeout(() => { this.closenoticebar() }, 5000)
      }
    },
    closenoticebar () {
      this.$store.commit('unloadNotice')
    },
    action () {
      this.notice.action(this)
    }
  }
}
</script>

<style lang="stylus" scoped>
  .noticebar
    width: auto
    overflow: hidden
    color: #babacc
    text-shadow: 0 0.01rem 0.01rem #000000
    font-size: .2rem
    text-align: center
    cursor: pointer
    div
      position: relative
      max-height: 100%
    .iconfont
      font-size: .4rem
      vertical-align: bottom
      color: #c3ffc9
      text-shadow: 0 0 0.51rem #30bd32
      margin-right: .1rem
    .notice
      animation: moveing 5s
      animation-direction: alternate
      @keyframes moveing
        0%
          left:0
        50%
          left: 2rem
        100%
          left: -2rem
</style>
