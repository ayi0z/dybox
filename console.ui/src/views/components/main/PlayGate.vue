<template>
  <div class="playgate">
    <el-container>
      <el-header>
        <el-input :disabled="isloading" placeholder="请输入内容播放接口链接" v-model="gateurl" class="input-with-select">
          <template slot="prepend">
            <i class="el-icon-caret-right"></i>{{hit}}<i class="el-icon-caret-left"></i></template>
          <el-checkbox :disabled="isloading" slot="append" v-model="isoff" true-label="0" :false-label="1">启用</el-checkbox>
          <el-button :loading="isloading" @click="doclear" slot="append" style="margin-left:1rem;border-left: solid .1rem #e4cbf5;" icon="el-icon-refresh"></el-button>
          <el-button :loading="isloading" @click="dosave" slot="append" style="margin-left:1rem;border-left: solid .1rem #e4cbf5;" icon="el-icon-success"></el-button>
          <el-button :loading="isloading" @click="doload" slot="append" style="margin-left:1rem;border-left: solid .1rem #e4cbf5;" icon="el-icon-search"></el-button>
        </el-input>
        <el-checkbox-group :disabled="isloading" style="margin-top: .2rem;" v-model="inscope" size="mini">
          <el-checkbox-button  v-for="so in scopes" :label="so" :key="so">{{so}}</el-checkbox-button>
        </el-checkbox-group>
      </el-header>
      <el-main>
        <el-table style="width: 100%" highlight-current-row
          :data="gatelist" size="mini" height="40rem"
          :row-class-name="rowStatus"
          @current-change="handleCurrentChange">
          <el-table-column type="index" width="50">
          </el-table-column>
          <el-table-column property="gateurl" label="接口链接"></el-table-column>
          <el-table-column property="inscope" label="应用范围" width="260">
            <template slot-scope="inscope">
              <el-tag size="mini" style="margin-right:.1rem"
                v-for="scope in inscope.row.inscope" :key="scope">{{scope}}</el-tag>
            </template>
          </el-table-column>
          <el-table-column property="hit" label="使用次数" width="50"></el-table-column>
          <el-table-column property="isoff" label="是否启用" width="50">
            <template slot-scope="isoff">
              <i :style="isoff.row.isoff ==='0'? 'color:green':'color:red'"
                :class="isoff.row.isoff ==='0' ? 'el-icon-circle-check':'el-icon-circle-close'"></i>
            </template>
          </el-table-column>
        </el-table>
      </el-main>
    </el-container>
  </div>
</template>
<script>
import qs from 'qs'
export default {
  name: 'PlayGate',
  data () {
    return {
      isloading: false,
      id: null,
      hit: 0,
      gateurl: '',
      isoff: 0,
      inscope: [],
      scopes: [ 'tencent', 'youku', 'iqiyi', 'dybox' ],
      gatelist: []
    }
  },
  computed: {
    playgateapi () {
      return this.$apis['playgate']
    }
  },
  mounted () {
    this.doload()
  },
  methods: {
    dosave () {
      if (!this.gateurl) { return }
      this.isloading = true
      if (this.playgateapi) {
        this.$axios.post(this.playgateapi, qs.stringify({
          action: 'save',
          paygate: {
            id: this.id,
            gateurl: this.gateurl,
            isoff: this.isoff,
            inscope: this.inscope
          }
        })).then((res) => {
          let redata = res.data
          if (redata) {
            if (redata.code === 1) {
              this.doload()
              this.doclear()
            }
          }
          this.isloading = false
        }).catch(() => {
          this.isloading = false
          console.log('error')
        })
      }
    },
    doload () {
      this.isloading = true
      if (this.playgateapi) {
        this.$axios.post(this.playgateapi, qs.stringify({
          action: 'query',
          offset: 0,
          pagesize: 1000
        })).then((res) => {
          this.gatelist = res.data.data
          this.isloading = false
        }).catch(() => {
          this.isloading = false
          console.log('error')
        })
      }
    },
    doclear () {
      this.id = null
      this.gateurl = ''
      this.isoff = 0
      this.hit = 0
      this.inscope = []
    },
    rowStatus ({row, rowIndex}) {
      return row.isoff === '0' ? 'on-row' : 'off-row'
    },
    handleCurrentChange (row) {
      if (row) {
        this.id = row._id.$oid
        this.gateurl = row.gateurl
        this.isoff = row.isoff
        this.hit = row.hit
        this.inscope = row.inscope
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
  .playgate >>> .el-input-group__prepend
  .playgate >>> .el-input__inner
  .playgate >>> .el-button
  .playgate >>> .el-input-group__append
  .playgate >>> .el-checkbox__inner
  .playgate >>> .el-checkbox-button__inner
    background-color #666d74
    border-color #666d74
    color #cbdae4
  .playgate >>> .is-checked .el-checkbox-button__inner
    background-color #424488
    border-color #424488
    box-shadow none
  .playgate
    .el-header
      width 100%
      height 8rem !important
      padding 1rem
      color #e4e8ec
      border-bottom solid .1rem #4f95ff
      margin-bottom .5rem
      .input-with-select
        box-shadow: 0 0 1rem #01040a
    .el-main
      width 100%
      background-color #203a5447
  .playgate >>> .el-table
    .on-row
      background #dcfbcb
      font-weight 500
    .off-row
      color #e4e4e4
</style>
