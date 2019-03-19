<template>
  <div class="progress">
    <el-popover placement="bottom" width="300" trigger="click">
      <ul>
        <li v-for="lo in log" :key="lo.timestamp">
          <i :class="lo.status ? 'el-icon-check' : 'el-icon-close'" ></i>
          <span>{{lo.text}}</span>
        </li>
      </ul>
      <el-progress slot="reference" :color="color" :status="status"
          :width="progress_width" :stroke-width="3"
          :type="type" :percentage="value">{{value}}%</el-progress>
    </el-popover>
      <div slot="reference" class="progress-dec" :style="{'line-height': text_height}">{{text}}</div>
  </div>
</template>

<script>
import qs from 'qs'
export default {
  name: 'Progress',
  data () {
    return {
      type: 'circle',
      status: 'text',
      value: 0,
      text: 'No Mission Is Running',
      log: []
    }
  },
  computed: {
    api () {
      return require('@/assets/js/apiurl.config.js')['progress']
    }
  },
  watch: {
    token () {
      if (this.token) {
        this.status = 'text'
        this.value = 0
        if (this.token) {
          this.text = 'Mission is ready'
        } else {
          this.text = 'No Mission Is Running'
        }
        this.loadprogress()
      }
    }
  },
  methods: {
    loadprogress () {
      if (this.token) {
        setTimeout(() => {
          this.$axios.post(this.api, qs.stringify({
            token: this.token
          })).then(this.loaddata).catch(() => {
            this.status = 'exception'
            this.log.push({ timestamp: Date.now(), status: 0, text: this.text })
          })
        }, 2000)
      } else {
        this.destory()
      }
    },
    loaddata (res) {
      this.value = Number(res.data.value)
      this.text = res.data.text
      let isover = Number(res.data.isover)

      if (this.value === 100) {
        this.log.push({ timestamp: Date.now(), status: 1, text: this.text })
      }

      if (isover === 0) {
        this.loadprogress()
      } else {
        this.status = 'success'
        this.$emit('progressfinished')
      }
    }
  },
  props: {
    token: Number,
    text_height: String,
    progress_width: {
      type: Number,
      default: 35
    },
    stoke_width: {
      type: Number,
      default: 1
    },
    color: String
  }
}
</script>

<style lang="stylus" scoped>
  .progress >>> .el-progress__text
    color #81bdf9
  .progress
    height 100%
    width 100%
    cursor pointer
    .el-progress
      margin-left 1rem
    .progress-dec
      display inline-block
      width auto
      vertical-align: top
  ul
    max-height 10rem
    overflow auto
  li
    i
      font-size 2rem
      vertical-align middle
    span
      font-size 1rem
    .el-icon-check
      margin-right .3rem
      color: rgb(19, 206, 102)
    .el-icon-close
      color: rgb(255, 73, 73)
</style>
