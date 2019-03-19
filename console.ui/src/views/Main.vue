<template>
  <div class="main">
    <el-container class="container">
      <el-aside class="aside" width="auto">
        <side-menu></side-menu>
      </el-aside>
      <el-container>
        <el-main class="main">
          <component :is="activemain"></component>
        </el-main>
      </el-container>
    </el-container>
 </div>
</template>

<script>
import SideMenu from './components/aside/SideMenu'
import PlayGate from './components/main/PlayGate'
import CrawlingData from './components/main/CrawlingData'
import ManualEntry from './components/main/ManualEntry'
import PlayList from './components/main/PlayList'
export default {
  name: 'Main',
  components: {
    SideMenu,
    PlayGate,
    CrawlingData,
    ManualEntry,
    PlayList
  },
  data () {
    return {
      isCollapse: false
    }
  },
  computed: {
    activemain () {
      return this.$store.state.activemain
    }
  }
}
</script>

<style lang="stylus" scoped>
  .main
    height 100%
    .container, .aside
      height 100%
    .el-header
      height 12rem
      margin-top 1rem
    .header
      height 12rem
    .footer
      background-color #060e131a
      color #393952
      line-height 2rem
      text-align center
</style>
