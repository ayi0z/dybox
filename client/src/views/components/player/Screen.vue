<template>
  <div id="screen" class="screen">
    <iframe id="screenfrm" name="screenfrm" scrolling="no" allowtransparency="true" frameborder="0"
        width="100%" height="100%" align="middle" hspace="0" vspace="0"
        allowfullscreen="true" marginheight="0" marginwidth="0"
        :src="fullurl" class="screen-frame">
    </iframe>
  </div>
</template>

<script>
export default {
  name: 'Screen',
  props: {
    gateurl: String,
    videourl: String
  },
  computed: {
    fullurl () {
      let gateurl = this.gateurl.split(' ').join('')
      // gateurl = gateurl === 'self:dplayer' ? '../../../static/dplayerscreen.html?url=' : gateurl
      return gateurl + this.videourl
    }
  },
  mounted () {
    this.$store.commit('loadNotice', 'loadmedias')
    var screenfrm = document.getElementById('screenfrm')
    if (screenfrm.attachEvent) {
      screenfrm.attachEvent('onload', () => { this.showback() })
    } else {
      screenfrm.onload = () => { this.showback() }
    }
  },
  methods: {
    showback () {
      if (document.getElementById('app').scrollTop >= document.getElementById('screenfrm').offsetHeight) {
        this.$store.commit('loadNotice', 'gotoscreen')
      } else {
        this.$store.commit('unloadNotice')
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
  .screen
    position: relative
    width: auto
    height: 0
    margin: 0
    padding-bottom: 45%
    .screen-frame
      position: absolute
      left: 0
</style>
