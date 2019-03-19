<template>
  <div class="crawling">
    <el-container>
      <el-header>
        <img style="height:2.5rem; vertical-align:middle; margin-right:1rem;" :src="logo" />
        <el-button-group>
          <el-button type="primary" icon="el-icon-search"
            :loading="loading === 'crawling'" :disabled="isloading" @click="docrawling">爬取数据</el-button>
          <el-button type="primary" icon="el-icon-check"
            :loading="loading === 'collate'" :disabled="isloading" @click="docollate">清洗数据</el-button>
          <el-button type="primary" icon="el-icon-upload2"
            :loading="loading === 'upload'" :disabled="isloading" @click="doupload">上传数据</el-button>
          <el-button type="primary" icon="el-icon-download"
            :loading="loading === 'download'" :disabled="isloading" @click="dodownload">导出数据</el-button>
        </el-button-group>
        <w-progress class="progress" text_height="3rem"
          :progress_width="35" :stoke_width="3" :token="token"
          @progressfinished="progressfinished"></w-progress>
      </el-header>
      <el-main>Main</el-main>
    </el-container>
  </div>
</template>

<script>
import WProgress from '../widget/Progress'
import qs from 'qs'
export default {
  name: 'CrawlingData',
  components: {
    WProgress
  },
  data () {
    return {
      loading: '',
      token: null
    }
  },
  computed: {
    logo () {
      return require('@/assets/imgs/' + this.spider + '.ico')
    },
    spider () {
      return this.$store.state.spider
    },
    isloading () {
      return this.loading !== ''
    },
    crawlingapi () {
      return this.$apis ? this.$apis['crawling'] : ''
    },
    collateapi () {
      return this.$apis ? this.$apis['collate'] : ''
    }
  },
  methods: {
    docrawling () {
      this.loading = 'crawling'
      this.token = Date.now()
      if (this.crawlingapi) {
        this.$axios.post(this.crawlingapi, qs.stringify({
          token: this.token,
          spider: this.spider
        })).catch(() => {
          console.log('error')
        })
      }
    },
    docollate () {
      this.loading = 'collate'
      this.token = Date.now()
      if (this.collateapi) {
        this.$axios.post(this.collateapi, qs.stringify({
          token: this.token,
          spider: this.spider
        })).catch(() => {
          console.log('error')
        })
      }
    },
    doupload () {
      console.log('upload')
      this.loading = 'upload'
    },
    dodownload () {
      console.log('download')
      this.loading = 'download'
    },
    progressfinished () {
      this.loading = ''
    }
  }
}
</script>

<style lang="stylus" scoped>
  .crawling
    .el-header
      width 100%
      height 6rem
      padding 1rem
      color #e4e8ec
      border-bottom solid .1rem #4f95ff
      margin-bottom .5rem
      .el-button--primary
        background-color #315a8440
      .is-disabled
        background-color #244765
        color #b9c5da
      .progress
        width auto
        height 100%
        display inline-block
        vertical-align middle
    .el-main
      width 100%
      background-color #203a5447
</style>
