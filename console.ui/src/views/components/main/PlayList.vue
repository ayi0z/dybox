<template>
  <div class="playlist">
   <el-dialog :visible.sync="dialog_visible" @close="close_dialog" :fullscreen="false" width="80%" top="0">
      <manual-entry v-if="dialog_visible" :pre_mid='dialog_pre_mid'></manual-entry>
    </el-dialog>
    <el-table size="mini" height="600" style="width: 100%;" ref="mediatable" :row-class-name="set_tb_row_class"
      :data="medialist" @sort-change='dosort' @filter-change='dofilter' v-loading="isloading">
      <el-table-column type="expand">
        <template slot="header" slot-scope="scope">
          <el-button-group style="padding:0;">
            <el-button size="mini" circle icon="iconfont icon-clear" @click="doclearfilter"></el-button>
          </el-button-group>
        </template>
        <template slot-scope="expand">
          <ul class="expand-ul">
            <li style="font-weight:500;">导演：</li>
            <li v-for="item in expand.row.director" :key="item">{{item}}</li>
          </ul>
          <ul class="expand-ul">
            <li style="font-weight:500;">主演：</li>
            <li v-for="item in expand.row.actor" :key="item">{{item}}</li>
          </ul>
          <ul class="expand-ul">
            <li style="font-weight:500;">风格：</li>
            <li v-for="item in expand.row.subtype" :key="item">{{item}}</li>
          </ul>
          <ul class="expand-ul">
            <li style="font-weight:500;">评分：</li>
            <li v-for="(item, index) in expand.row.score" :key="index">{{index}}: {{item}}</li>
          </ul>
          <ul class="expand-ul">
            <li style="font-weight:500; min-width:3rem;">上架时间：</li>
            <li style="line-height:1.4rem;">{{timestampToTime(expand.row.pubdate/1000)}}</li>
          </ul>
          <ul class="expand-ul" v-if="expand.row.epsprog">
            <li style="font-weight:500; min-width:3rem;">更新进度：</li>
            <li style="line-height:1.4rem;">{{expand.row.epsprog.epsc}}</li>
          </ul>
          <ul class="expand-ul" v-if="expand.row.langue">
            <li style="font-weight:500; min-width:3rem;">语言：</li>
            <li style="line-height:1.4rem;">{{expand.row.langue}}</li>
          </ul>
          <ul class="expand-ul">
            <li style="font-weight:500; min-width:3rem;">简介：</li>
            <li style="line-height:1.4rem;">{{expand.row.desc}}</li>
          </ul>
          <el-table style="width: 100%" highlight-current-row stripe
            :data="expand.row.eps"  max-height="400" size="mini">
            <el-table-column type="index" width="50"></el-table-column>
            <el-table-column property="url" label="播放链接"></el-table-column>
            <el-table-column property="title" label="标题" width="120"></el-table-column>
            <el-table-column property="playgate" label="来源" width="120"></el-table-column>
            <el-table-column property="isoff" label="可用" width="50">
              <template slot-scope="isoff">
                <i :style="isoff.row.isoff === '0' ? 'color:green':'color:red'"
                  :class="isoff.row.isoff === '0' ? 'el-icon-circle-check':'el-icon-circle-close'"></i>
              </template>
            </el-table-column>
          </el-table>
        </template>
      </el-table-column>
      <el-table-column width="50">
        <template slot="header" slot-scope="scope">
          <el-button size="mini" circle icon="iconfont icon-pingjia" @click="open_dialog()"></el-button>
        </template>
        <template slot-scope="scope">
          <img :src="scope.row.pic" :alt="scope.row.title" height="50" />
        </template>
      </el-table-column>
      <el-table-column>
        <template slot="header" slot-scope="scope">
          <el-input v-model="query.title" size="mini" placeholder="输入 标题 搜索" @keyup.enter.native="doreload"/>
        </template>
        <template slot-scope="scope">
          <h3 style="cursor:pointer;" @click="open_dialog(scope.row._id)">{{scope.row.title}}</h3>
          <h3 style="cursor:pointer;" @click="open_dialog(scope.row._id)">{{scope.row.title_en}}</h3>
        </template>
      </el-table-column>
      <el-table-column label="年度" prop="year" width="90" sortable='custom' columnKey='year' :filters="ft_year"></el-table-column>
      <el-table-column label="地区" prop="area" width="80" columnKey='area' :filters="ft_area"></el-table-column>
      <!-- <el-table-column label="语言" prop="langue" width="80" columnKey='langue' :filters="ft_langue"></el-table-column> -->
      <el-table-column label="eps" prop="epsprog.epsc" width="60" columnKey='epsprog.isall' :filters="ft_isall"></el-table-column>
      <el-table-column label="类型" prop="type" width="60" columnKey='type' :filters="ft_type"></el-table-column>
      <el-table-column label="最后更新" prop="latime" width="100" sortable='custom'>
        <template slot-scope="scope">
          <h4>{{timestampToTime(scope.row.latime/1000).split(' ')[0]}}</h4>
          <h4>{{timestampToTime(scope.row.latime/1000).split(' ')[1]}}</h4>
        </template>
      </el-table-column>
      <el-table-column label="观影密令" prop="mocode" width="80">
        <template slot-scope="scope">
          <a style="color:#05101b;text-decoration:none;" :href="'http://www.dybox.top/h/'+scope.row.mocode+'.html'" target="_blank">{{scope.row.mocode}}</a>
        </template>
      </el-table-column>
      <el-table-column label="热度" prop="hot" width="70" sortable='custom'>
        <template slot-scope="scope">
          <i style="color:red;font-size:1rem;" class="iconfont icon-huo">{{scope.row.hot}}</i>
        </template>
      </el-table-column>
      <el-table-column label="" width="35">
        <template slot-scope="isoff">
          <i :style="isoff.row.isoff === '0' ? 'color:green':'color:red'"
            :class="isoff.row.isoff === '0' ? 'el-icon-circle-check':'el-icon-circle-close'"></i>
        </template>
      </el-table-column>
      <infinite-loading spinner="waveDots" slot="append" force-use-infinite-wrapper=".el-table__body-wrapper"
        @infinite="load" :identifier="identifiercount">
      </infinite-loading>
    </el-table>
  </div>
</template>

<script>
import qs from 'qs'
import InfiniteLoading from 'vue-infinite-loading'
import ManualEntry from './ManualEntry'
export default {
  name: 'PlayList',
  components: {
    InfiniteLoading,
    ManualEntry
  },
  data () {
    return {
      dialog_visible: false,
      dialog_pre_mid: null,
      identifiercount: +new Date(),
      offset: 0,
      pagesize: 30,
      isloading: false,
      medialist: [],
      sort: null,
      query: {
        title: null
      },
      ft: require('@/assets/js/sugestions.data.js')
    }
  },
  computed: {
    ft_area () {
      return this.ft['area']
    },
    ft_langue () {
      return this.ft['langue']
    },
    ft_type () {
      return this.ft['type']
    },
    ft_year () {
      return this.ft['year']
    },
    ft_isall () {
      return this.ft['isall']
    }
  },
  methods: {
    set_tb_row_class ({row, rowIndex}) {
      if (!row.epsprog) {
        let epsoncount = row.eps ? row.eps.filter((ep) => { return parseInt(ep.isoff) === 0 }).length : 0
        let pepscount = parseInt(row.epscount)
        row.epsprog = { isall: 0, epsc: '0/0' }
        row.epsprog.isall = pepscount ? epsoncount / pepscount : 1
        row.epsprog.epsc = pepscount ? epsoncount + '/' + row.epscount : epsoncount
      }

      return parseInt(row.epsprog.isall) ? '' : 'row_not_update_complated'
    },
    open_dialog (mid) {
      this.dialog_visible = true
      if (mid) {
        this.dialog_pre_mid = { '_id': mid }
      }
    },
    close_dialog () {
      this.dialog_pre_mid = null
    },
    timestampToTime (timestamp) {
      let date = new Date(timestamp * 1000) // 时间戳为10位需*1000，时间戳为13位的话不需乘1000
      let Y = date.getFullYear() + '-'
      let M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-'
      let D = date.getDate() + ' '
      let h = date.getHours() + ':'
      let m = date.getMinutes() + ':'
      let s = date.getSeconds()
      return Y + M + D + h + m + s
    },
    dosort (column) {
      if (column.prop) {
        this.sort = {
          'prop': column.prop,
          'order': column.order
        }
      } else {
        this.sort = null
      }
      this.medialist = []
      this.offset = 0
      this.identifiercount += 1
    },
    doclearfilter () {
      this.$refs.mediatable.clearFilter()
      this.query = { title: null }
      this.doreload()
    },
    doreload () {
      this.medialist = []
      this.offset = 0
      this.identifiercount += 1
    },
    dofilter (filter) {
      for (let key in filter) {
        this.query[key] = filter[key]
      }
      this.doreload()
    },
    load ($state) {
      this.isloading = true
      this.$axios.post(this.$apis['medias'], qs.stringify({
        query: this.query,
        sort: this.sort,
        offset: this.offset,
        pagesize: this.pagesize
      }), { headers: { 'Content-type': 'application/x-www-form-urlencoded' } }).then((res) => {
        let resdata = res.data
        if (resdata.code === 1 && resdata.data) {
          this.medialist.push(...resdata.data)
          this.offset = this.offset + resdata.data.length
          if (resdata.data.length < this.pagesize) {
            $state.complete()
          } else {
            $state.loaded()
          }
        } else {
          $state.complete()
        }
        this.isloading = false
      }).catch((err) => {
        this.isloading = false
        $state.error()
        this.$message({
          showClose: true,
          message: '加载媒体信息失败：' + err,
          type: 'error'
        })
      })
    }
  }
}
</script>

<style lang="stylus" scoped>
  .playlist >>> .el-dialog
    margin-bottom 0
  .playlist >>> .el-dialog__header
    display none
  .playlist >>> .el-dialog__body
    padding 0
  .playlist >>> .row_not_update_complated
    box-shadow inset 0 0 10rem #ff000075
  .expand-ul
    display inline-flex
    background-color #f5faff
    margin-top: .3rem
    margin-right 1rem
    li
      padding .3rem
</style>
