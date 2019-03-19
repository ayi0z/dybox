<template>
   <div class="manualentry">
    <el-form :model="form" width="400" size="mini" v-loading="isloading">
      <el-form-item>
        <el-card>
          <el-card style="position: absolute;bottom:1rem;right: 2rem;z-index: 0;height:13rem;width:22rem;">
            <el-input size="mini" class="infoshow" readonly placeholder="_id" v-model="this._id"><template slot="prepend">_id:</template></el-input>
            <el-input size="mini" class="infoshow" readonly placeholder="Cover ID" v-model="form.cid"><template slot="prepend">Cover ID:</template></el-input>
            <el-input size="mini" class="infoshow" readonly placeholder="热度" v-model="form.hot"><template slot="prepend">热度:</template></el-input>
            <el-input size="mini" class="infoshow" readonly placeholder="上架日期" v-model="form.pubdate"><template slot="prepend">上架日期:</template></el-input>
          </el-card>
          <el-row style="text-align:center;" :gutter="17">
            <el-col :span="5">
              <el-autocomplete clearable class="inline-input" style="width:100%;" placeholder="中文名"
                suffix-icon="iconfont icon-zhongwen1"
                v-model="form.title" popper-class="title-autocomplete" :trigger-on-focus="false"
                :fetch-suggestions="(query, cb)=>{dosuggest(query, cb, 'title')}"
                @select="(item)=>{dosuggest_select(item, 'title')}">
                <template slot-scope="{ item }">
                  <div style="position:relative;display:inline-block;">
                    <img :v-show="item.pic" :src="item.pic" :onerror="img" referrer="no-referrer" :alt="item.title"
                          style="z-index:2;max-width:6rem;margin-top:.3rem;vertical-align:text-bottom;" />
                    <i style="position:absolute;bottom:.8rem;right: 0px;color:rgb(27, 191, 66);font-size: 2rem;line-height: 1rem;"
                      v-if="item.from" class="iconfont"
                      :class="item.from =='douban' ? 'icon-doubanwang' : 'icon-hebing' "></i>
                  </div>
                  <div class="title-options">
                    <p class="title">{{ item.value }}</p>
                    <p class="title-en">{{ item.title_en }}</p>
                    <p class="title-en">{{ item.year }}</p>
                  </div>
                </template>
              </el-autocomplete>
            </el-col>
            <el-col :span="6"><el-input clearable v-model="form.title_en" suffix-icon="iconfont icon-yingwen2" placeholder="英文名"></el-input></el-col>
            <el-col :span="3"><el-input clearable v-model="form.dbid" suffix-icon="iconfont icon-doubanwang" placeholder="豆瓣 id"></el-input></el-col>
            <el-col :span="1"><el-switch v-model="form.isoff" active-value="0" inactive-value="1"></el-switch></el-col>
            <el-col :span="2">{{form.isoff==='0' ? 'online' : 'offline'}}</el-col>
            <el-col :span="3"><el-input clearable v-model="ft_dbid" suffix-icon="iconfont icon-doubanwang" placeholder="ft dbid"></el-input></el-col>
          </el-row>
          <el-row style="text-align:center;" :gutter="17">
            <el-col :span="14"><el-input clearable v-model="form.pic" suffix-icon="el-icon-picture" placeholder="图片链接"></el-input></el-col>
            <el-col :span="3"><el-input class="input-mocode" clearable v-model="form.mocode" maxlength="6" suffix-icon="iconfont icon-kouling" placeholder="观影密令"></el-input></el-col>
            <el-col :span="3">
              <div style="position:relative">
                  <img :v-show="form.pic" :src="form.pic" :onerror="img" referrer="no-referrer" :alt="this.form.title"
                style="position:absolute;left:0.2rem;top:.3rem;z-index:3;max-height:10.5rem;padding:.3rem;box-shadow: 0 0 0.8rem #100606;" />
              </div>
            </el-col>
          </el-row>
          <el-row style="text-align:left;" :gutter="17">
            <el-col :span="14">
              <el-tag :key="dir" v-for="dir in form.director" closable size="small" style="margin-right:.2rem"
                  :disable-transitions="false" @close="doclosetag(dir, 'director')">{{dir}}</el-tag>
              <el-input class="input-new-tag" size="mini" style="width:8rem;height:2rem;"
                v-if="edittags['director'].input" v-model="edittags['director'].newvalue" ref="inputdirector"
                @keyup.enter.native="()=>{doconfirmtag('director')}" @blur="()=>{doconfirmtag('director')}">
              </el-input>
              <el-button v-else class="button-new-tag" size="mini" style="height:2rem;padding-bottom:0;padding-top:0;"
                @click="(tag)=>{donewtag(tag, 'director')}">+ 导演</el-button>
            </el-col>
            <el-col :span="3">
              <el-autocomplete clearable class="inline-input" placeholder="地区" suffix-icon="el-icon-location"
                v-model="form.area"
                :fetch-suggestions="(query, cb)=>{dosuggest(query, cb, 'area')}"
                @select="(item)=>{dosuggest_select(item, 'area')}"></el-autocomplete>
            </el-col>
          </el-row>
          <el-row style="text-align:left;" :gutter="17">
            <el-col :span="14">
              <el-tag :key="dir" v-for="dir in form.actor" closable size="small" style="margin-right:.2rem"
                  :disable-transitions="false" @close="doclosetag(dir, 'actor')">{{dir}}</el-tag>
              <el-input class="input-new-tag" size="mini" style="width:8rem;height:2rem;"
                v-if="edittags['actor'].input" v-model="edittags['actor'].newvalue" ref="inputactor"
                @keyup.enter.native="()=>{doconfirmtag('actor')}" @blur="()=>{doconfirmtag('actor')}">
              </el-input>
              <el-button v-else class="button-new-tag" size="mini" style="height:2rem;padding-bottom:0;padding-top:0;"
                @click="(tag)=>{donewtag(tag, 'actor')}">+ 主演</el-button>
            </el-col>
            <el-col :span="3">
              <el-autocomplete clearable class="inline-input" placeholder="语言" suffix-icon="el-icon-service"
                v-model="form.langue"
                :fetch-suggestions="(query, cb)=>{dosuggest(query, cb, 'langue')}"
                @select="(item)=>{dosuggest_select(item, 'langue')}"></el-autocomplete>
            </el-col>
          </el-row>
          <el-row style="text-align:left;" :gutter="17">
            <el-col :span="14">
              <el-tag :key="dir" v-for="dir in form.subtype" closable size="small" style="margin-right:.2rem"
                  :disable-transitions="false" @close="doclosetag(dir, 'subtype')">{{dir}}</el-tag>
              <el-input class="input-new-tag" size="mini" style="width:8rem;height:2rem;"
                v-if="edittags['subtype'].input" v-model="edittags['subtype'].newvalue" ref="inputsubtype"
                @keyup.enter.native="()=>{doconfirmtag('subtype')}" @blur="()=>{doconfirmtag('subtype')}">
              </el-input>
              <el-button v-else class="button-new-tag" size="mini" style="height:2rem;padding-bottom:0;padding-top:0;"
                @click="(tag)=>{donewtag(tag, 'subtype')}">+ 类型</el-button>
            </el-col>
            <el-col :span="3"><el-input clearable v-model="form.year" maxlength="4" suffix-icon="el-icon-time" placeholder="年份"></el-input></el-col>
          </el-row>
          <el-row style="text-align:center;" class="score" :gutter="17">
            <el-col :span="3">
              <el-input clearable v-model="form.score.douban" style="height:2rem;" suffix-icon="iconfont icon-doubanwang" placeholder="豆瓣评分"></el-input>
            </el-col>
            <el-col :span="3">
              <el-input clearable v-model="form.score.tencent" class="score-tencent" suffix-icon="iconfont icon-cntencentvideo" placeholder="腾讯视频评分"></el-input>
            </el-col>
            <el-col :span="3">
              <el-input clearable v-model="form.score.IMDb1" suffix-icon="iconfont icon-IMDb1" placeholder="IMDb1评分"></el-input>
            </el-col>
            <el-col :span="3">
              <el-input clearable v-model="form.score.mtime" suffix-icon="iconfont icon-CN_mtimecom" placeholder="MTime评分"></el-input>
            </el-col>
            <el-col :span="2">&nbsp;</el-col>
            <el-col :span="3">
              <el-autocomplete clearable class="inline-input" placeholder="类别" suffix-icon="iconfont icon-leibie1"
                v-model="form.type" value-key="text"
                :fetch-suggestions="(query, cb)=>{dosuggest(query, cb, 'type')}"
                @select="(item)=>{dosuggest_select(item, 'type')}"></el-autocomplete>
            </el-col>
          </el-row>
          <el-row style="text-align:center; margin-top:.2rem;" :gutter="17">
            <el-col :span="17"><el-input type="textarea" v-model="form.desc" :rows="5" placeholder="剧情简介"></el-input></el-col>
          </el-row>
        </el-card>
      </el-form-item>
      <el-form-item>
        <el-card>
          <el-row style="text-align:center;" :gutter="17">
            <el-col :span="4"><el-input readonly v-model="editingEp.epid" suffix-icon="iconfont icon-tishishuoming" placeholder="epid"></el-input></el-col>
            <el-col :span="2"><el-input clearable v-model="form.epscount" suffix-icon="iconfont icon-leibie1" placeholder="集数"></el-input></el-col>
            <el-col :span="4"><el-input clearable v-model="editingEp.title" suffix-icon="iconfont icon-yingwen2" placeholder="标题"></el-input></el-col>
            <el-col :span="4">
              <el-autocomplete clearable class="inline-input" placeholder="播放接口" suffix-icon="iconfont icon-URLlianjie"
                v-model="editingEp.playgate"
                :fetch-suggestions="(query, cb)=>{dosuggest(query, cb, 'playgate')}"
                @select="(item)=>{dosuggest_select(item, 'playgate')}"></el-autocomplete>
            </el-col>
            <el-col :span="1"><el-switch v-model="editingEp.isoff" active-value="0" inactive-value="1"></el-switch></el-col>
            <el-col :span="2">{{editingEp.isoff === '0' ? 'online' : 'offline'}}</el-col>
            <el-col :span="2"><el-input v-model="editingEp.hot" readonly suffix-icon="iconfont icon-huo" placeholder="播放热度"></el-input></el-col>
          </el-row>
          <el-row style="text-align:center;" :gutter="17">
            <el-col :span="14"><el-input type="textarea" :rows="3" clearable v-model="editingEp.url" suffix-icon="iconfont icon-bofang" placeholder="播放链接"></el-input></el-col>
            <el-col :span="10">
              <el-input v-model="form.syncsource" suffix-icon="iconfont icon-mc-urllj" placeholder="源url"></el-input>
              <el-col style="z-index:2;width:26rem;padding:0rem;margin-top:.1rem;" :body-style="{ padding: '0px' }" shadow="never">
                <el-row style="text-align:center;" :gutter="26">
                  <el-col :span="3">
                    <el-button type="success" icon="iconfont icon-shujuliebiao1" circle @click="docommitep"></el-button>
                  </el-col>
                  <el-col :span="3">
                    <el-button type="success" icon="iconfont icon-leibie" circle @click="docleareps"></el-button>
                  </el-col>
                  <el-col :span="4">
                    <el-button type="warning" icon="iconfont icon-API1" circle @click="dosync"></el-button>
                  </el-col>
                  <el-col :span="6">
                    <el-button type="primary" :loading="isloading" round @click="dosave">上传影片</el-button>
                  </el-col>
                  <el-col :span="3">
                    <el-button type="primary" icon="iconfont icon-clear" circle @click="doclear"></el-button>
                  </el-col>
                  <el-col :span="3">
                    <el-button type="primary" icon="iconfont icon-pingjia" circle @click="docopy"></el-button>
                  </el-col>
                </el-row>
              </el-col>
            </el-col>
          </el-row>
          <el-row>
            <el-col style="background-color:#ddd;">
              <el-table style="width: 100%" highlight-current-row stripe
                :data="form.eps"  max-height="400" size="mini"
                @row-click="doselectrow">
                <el-table-column type="index" width="50"></el-table-column>
                <el-table-column property="url" label="播放链接"></el-table-column>
                <el-table-column property="title" label="标题" width="120"></el-table-column>
                <el-table-column property="playgate" label="播放接口" width="80"></el-table-column>
                <el-table-column property="isoff" label="可用" width="50">
                  <template slot-scope="isoff">
                    <i :style="isoff.row.isoff === '0' ? 'color:green':'color:red'"
                      :class="isoff.row.isoff === '0' ? 'el-icon-circle-check':'el-icon-circle-close'"></i>
                  </template>
                </el-table-column>
              </el-table>
            </el-col>
          </el-row>
        </el-card>
      </el-form-item>
    </el-form>
   </div>
</template>

<script>
import qs from 'qs'
export default {
  name: 'ManualEntry',
  methods: {
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
    doclosetag (tag, elname) {
      this.form[elname].splice(this.form[elname].indexOf(tag), 1)
    },
    donewtag (tag, elname) {
      this.edittags[elname].input = true
      this.$nextTick(_ => {
        this.$refs['input' + elname].$refs.input.focus()
      })
    },
    doconfirmtag (elname) {
      let newvalue = this.edittags[elname].newvalue
      if (newvalue) {
        this.form[elname] = this.form[elname] ? this.form[elname] : []
        this.form[elname].push(newvalue)
      }
      this.edittags[elname].input = false
      this.edittags[elname].newvalue = ''
    },
    dosuggest (query, cb, elname) {
      if (elname === 'title' && query) {
        let filter = { 'title': query }
        this.doloadautocompete(filter, cb)
      } else {
        cb(this.suggestions[elname])
      }
    },
    dosuggest_select (item, elname) {
      if (elname === 'title') {
        this.doloadmedia(item)
      } else if (elname === 'type') {
        this.form['type'] = item.value
      }
    },
    doloadautocompete (query, cb) {
      if (this.ft_dbid) {
        query['dbid'] = this.ft_dbid
      }
      this.$axios.post(this.$apis['autocomplete'], qs.stringify({
        query: query,
        offset: 0,
        pagesize: 30
      }), {
        headers: {
          'Content-type': 'application/x-www-form-urlencoded'
        }
      }).then((res) => {
        let resdata = res.data
        if (resdata.code === 1 && resdata.data) {
          resdata.data.forEach((data) => { data['value'] = data.title })
          cb(resdata.data)
        }
      }).catch(() => {
        this.$message({
          showClose: true,
          message: 'auto complete error',
          type: 'error'
        })
      })
    },
    doloadmedia (filter) {
      this.isloading = true
      this.$axios.post(this.$apis['medias'], qs.stringify({
        mfilter: filter
      }), { headers: { 'Content-type': 'application/x-www-form-urlencoded' } }).then((res) => {
        let resdata = res.data
        if (resdata.code === 1 && resdata.data) {
          this.form = Object.assign({}, this.template_form, resdata.data)
          if (!Date.parse(this.form.pubdate)) {
            if (this.form.pubdate.toString().length === 13) {
              this.form.pubdate = this.timestampToTime(parseInt(this.form.pubdate) / 1000)
            } else {
              this.form.pubdate = this.timestampToTime(Date.now())
            }
          }
        } else {
          this.$message({
            showClose: true,
            message: '未加载有效媒体信息',
            type: 'warning'
          })
        }
        this.isloading = false
      }).catch(() => {
        this.isloading = false
        this.$message({
          showClose: true,
          message: '加载媒体信息失败',
          type: 'error'
        })
      })
    },
    doselectrow (row) {
      if (row) {
        this.editingEp = row
      } else {
        this.editingEp = JSON.parse(JSON.stringify(this.template_ep))
      }
    },
    doclear () {
      this.editingEp = JSON.parse(JSON.stringify(this.template_ep))
      this.template_form.eps = []
      this.form = JSON.parse(JSON.stringify(this.template_form))
    },
    docopy () {
      this.form._id = null
      this.form.cid = null
      this.form.eps = []
    },
    dosync () {
      if (this.form.syncsource) {
        this.isloading = true
        this.$axios.post(this.$apis['synceps'], qs.stringify({
          syncsource: this.form.syncsource
        })).then((res) => {
          let redata = res.data
          if (redata) {
            if (redata.code === 1) {
              this.synceps = redata.data.map(c => {
                c = c.split('$')
                return { title: c[0].replace(/(大结局)?(字幕修正)?(画面)?(修正)?(修复)?(声音)?(\$97zyck)?/ig, ''), url: c[1] }
              })

              if (!this.form.eps || this.form.eps.length === 0) {
                this.editingEp.url = this.synceps.map(c => (c.title + '$' + c.url)).join('\n')
                this.docommitep()
              } else {
                for (let sc of this.synceps) {
                  let lep = this.form.eps.find(c => (c.url === sc.url))
                  sc.isnew = lep ? 0 : 1
                }
                this.editingEp.url = this.synceps.filter(c => (c.isnew === 1)).map(c => (c.title + '$' + c.url)).join('\n')
                console.log(this.editingEp.url)
              }
            } else {
              this.$message({
                showClose: true,
                message: redata.msg,
                type: 'warning'
              })
            }
          }
          this.isloading = false
        }).catch((res) => {
          this.isloading = false
          this.$message({
            showClose: true,
            message: '同步失败',
            type: 'error'
          })
        })
      }
    },
    docleareps () {
      this.form.eps = []
    },
    docommitep () {
      if (!this.editingEp.epid && this.editingEp.url && this.editingEp.url.trim()) {
        let urls = this.editingEp.url.split('\n')
        let goodurls = []
        for (let url of urls) {
          url = url.trim()
          if (!url) { continue } // 跳过空白行
          let th = url.split('$')
          let h = th[th.length - 1]
          if (!h || !h.trim()) { return } // 某一行格式错误，中断执行
          h = h.trim()
          if (!h.startsWith('http') && !h.startsWith('magnet:')) { return }
          goodurls.push([th[th.length - 2], h])
        }
        for (let gu of goodurls) {
          let tep = JSON.parse(JSON.stringify(this.template_ep))
          tep.title = gu[0] ? gu[0] : this.editingEp.title
          tep.url = gu[1]
          tep.playgate = this.editingEp.playgate
          tep.isoff = this.editingEp.isoff
          this.docommitep_single(tep)
        }
      }
      this.editingEp = JSON.parse(JSON.stringify(this.template_ep))
    },
    docommitep_single (eEp) {
      if (!eEp.epid && eEp.url && eEp.url.trim()) {
        eEp.url = eEp.url.trim().replace('\n', '')
        if (this.form.eps) {
          let index = this.form.eps.findIndex((ep) => { return ep.url.trim().toLowerCase() === eEp.url.trim().toLowerCase() })
          if (index > -1) {
            eEp.epid = this.form.eps[index].epid ? this.form.eps[index].epid : Date.now() + Math.round(Math.random(1) * 100000)
            eEp.epid = eEp.epid + eEp.title
            eEp = Object.assign({}, this.form.eps[index], eEp)
            eEp.title = eEp.title ? eEp.title : this.form.eps[index].title
            this.form.eps.splice(index, 1, eEp)
          } else {
            eEp.title = eEp.title ? eEp.title : this.form.eps.length + 1
            eEp.epid = Date.now() + Math.round(Math.random(1) * 100000)
            this.form.eps.push(eEp)
          }
        } else {
          eEp.title = eEp.title ? eEp.title : this.form.eps.length + 1
          eEp.epid = Date.now() + Math.round(Math.random(1) * 100000)
          this.form.eps = []
          this.form.eps.push(eEp)
        }
      }
    },
    dovalidate () {
      let err = ''
      if (!this.form.title || this.form.title.split(' ').join('').length === 0) {
        err = '* 标题必填！'
      }
      if (!this.form.mocode || this.form.mocode.split(' ').join('').length === 0) {
        err += '* 观影密令必填！'
      }

      if (err && err.length > 0) {
        this.$message({
          showClose: true,
          message: err,
          type: 'error'
        })
        return false
      }
      return true
    },
    dosave () {
      if (!this.dovalidate()) {
        return
      }
      this.isloading = true
      this.form.cid = this.form.cid ? this.form.cid : Date.now() + Math.round(Math.random(1) * 100000)

      let oneps = this.form.eps.filter((ep) => { return parseInt(ep.isoff) === 0 })
      let eosoncount = oneps.length
      if (parseInt(this.form.type) === 1) {
        if (oneps.length > 0) {
          this.form.epsprog.isall = 1
          let newstep = oneps[eosoncount - 1]
          this.form.epsprog.epsc = newstep.title
        } else {
          this.form.epsprog.isall = 0
          this.form.epsprog.epsc = '即将上线'
        }
      } else {
        let pepscount = parseInt(this.form.epscount)
        this.form.epsprog.isall = pepscount ? parseInt(eosoncount / pepscount) : 1
        this.form.epsprog.epsc = pepscount ? eosoncount + '/' + this.form.epscount : eosoncount
      }

      this.$axios.post(this.$apis['mediascrud'], qs.stringify({
        action: 'save',
        mediacover: this.form
      })).then((res) => {
        let redata = res.data
        if (redata) {
          if (redata.code === 1) {
            this.$message({
              showClose: true,
              message: '数据已保存。',
              type: 'success'
            })
            this.doclear()
          }
        }
        this.isloading = false
      }).catch(() => {
        this.isloading = false
        this.$message({
          showClose: true,
          message: '保存数据失败',
          type: 'error'
        })
      })
    }
  },
  data () {
    return {
      ft_dbid: '',
      isloading: false,
      img: require('@/assets/imgs/dybox.ico'),
      synceps: [],
      edittags: {
        director: {
          input: false,
          newvalue: ''
        },
        actor: {
          input: false,
          newvalue: ''
        },
        subtype: {
          input: false,
          newvalue: ''
        }
      },
      template_form: {
        _id: null,
        cid: null,
        dbid: null,
        type: '1',
        title: null,
        title_en: null,
        alias: [ ],
        year: null,
        area: null,
        langue: null,
        director: [],
        actor: [],
        subtype: [],
        desc: null,
        score: {
          douban: null,
          tencent: null,
          imdb: null,
          mtime: null
        },
        pic: null,
        pubdate: new Date().getFullYear() + '-' + (new Date().getMonth() + 1) + '-' + new Date().getDate() + ' ' + new Date().getHours() + ':' + new Date().getMinutes() + ':' + new Date().getSeconds(),
        istrailer: 0,
        epscount: null,
        epsprog: {
          isall: 0,
          epsc: '0/0'
        },
        eps: [],
        hot: 0,
        mocode: null,
        isoff: '0',
        syncsource: ''
      },
      template_ep: {
        epid: null,
        title: null,
        playgate: 'dybox',
        url: null,
        hot: 0,
        isoff: '0'
      },
      suggestions: require('@/assets/js/sugestions.data.js'),
      editingEp: null,
      form: null
    }
  },
  props: {
    pre_mid: Object
  },
  mounted () {
    if (this.pre_mid) {
      this.doloadmedia(this.pre_mid)
    }
  },
  watch: {
    pre_mid () {
      if (this.pre_mid) {
        this.doloadmedia(this.pre_mid)
      }
    }
  },
  computed: {
    _id () {
      return this.form ? (this.form._id ? this.form._id.$oid : '') : ''
    }
  },
  beforeMount () {
    this.form = this.form ? this.form : JSON.parse(JSON.stringify(this.template_form))
    this.editingEp = this.editingEp ? this.editingEp : JSON.parse(JSON.stringify(this.template_ep))
  }
}
</script>

<style lang="stylus" scoped>
  .manualentry >>> .icon-kouling
    margin .4rem
  .score >>> .iconfont
    font-size: 2.5rem
  .score-tencent >>> .el-input__suffix
    margin-top -.2rem
  .score >>> .el-input__inner
    height: 2rem
  .manualentry >>> input[readonly='readonly']
    color #c30d0d
    background-color #f5f7fa;
  .infoshow >>> .el-input-group__prepend
    padding: 0 .5rem;
  .title-autocomplete
    min-width 30rem
    li
      padding .5rem
      line-height 1rem
    .title-options
      display inline-block
      margin-top: .3rem;
      vertical-align: top;
      p
        font-size 1rem
        padding 0
        line-height 1.1rem
  .manualentry >>> .input-mocode
    input
      text-transform uppercase
</style>
