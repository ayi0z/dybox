module.exports = [
  {
    type: 0,
    id: 1,
    template: {
      ico: 'iconfont icon-shuju',
      label: '数据收录'
    },
    items: [
      {
        type: 1,
        id: 1,
        label: '腾讯视频',
        img: require('@/assets/imgs/tencent.ico'),
        click (ctx) {
          ctx.$store.commit('activemain', 'CrawlingData')
          ctx.$store.commit('spider', 'tencent')
        }
      },
      {
        type: 1,
        id: 2,
        label: '爱奇艺',
        img: require('@/assets/imgs/iqiyi.ico'),
        click (ctx) {
          ctx.$store.commit('activemain', 'CrawlingData')
          ctx.$store.commit('spider', 'iqiyi')
        }
      },
      {
        type: 1,
        id: 3,
        label: '优酷',
        img: require('@/assets/imgs/youku.ico'),
        click (ctx) {
          ctx.$store.commit('activemain', 'CrawlingData')
          ctx.$store.commit('spider', 'youku')
        }
      },
      {
        type: 1,
        id: 4,
        label: '蛋蛋赞',
        img: require('@/assets/imgs/dandanzan.ico'),
        click (ctx) {
          ctx.$store.commit('activemain', 'CrawlingData')
          ctx.$store.commit('spider', 'dandanzan')
        }
      },
      {
        type: 1,
        id: 5,
        label: '手工录入',
        ico: 'iconfont icon-pingjia',
        click (ctx) {
          ctx.$store.commit('activemain', 'ManualEntry')
        }
      }
    ]
  },
  {
    type: 1,
    id: 2,
    ico: 'iconfont icon-shujuliebiao',
    label: '视频列表',
    click (ctx) {
      ctx.$store.commit('activemain', 'PlayList')
    }
  },
  {
    type: 1,
    id: 3,
    ico: 'iconfont icon-wx',
    label: '微信记录',
    click (ctx) {
      ctx.$store.commit('activemain', 'ManualEntry')
    }
  },
  {
    type: 1,
    id: 4,
    ico: 'iconfont icon-URLlianjie',
    label: '播放接口',
    click (ctx) {
      ctx.$store.commit('activemain', 'PlayGate')
    }
  },
  {
    type: 0,
    id: 5,
    template: {
      ico: 'iconfont icon-index',
      label: '指数分析'
    },
    items: [
      {
        type: 1,
        id: 1,
        ico: 'iconfont icon-huo',
        label: '密令热度',
        click () { console.log(this.label) }
      },
      {
        type: 1,
        id: 2,
        ico: 'iconfont icon-dianyingzhiye-dianyingbangdianjitai',
        label: '电影热度',
        click () { console.log(this.label) }
      },
      {
        type: 1,
        id: 3,
        ico: 'iconfont icon-tubiao11',
        label: '流量曲线',
        click () { console.log(this.label) }
      }
    ]
  },
  {
    type: 0,
    id: 6,
    template: {
      ico: 'iconfont icon-shuju1',
      label: '数据存储'
    },
    items: [
      {
        type: 1,
        id: 1,
        ico: 'iconfont icon-shangchuan',
        label: '上传数据',
        click () { console.log(this.label) }
      },
      {
        type: 1,
        id: 2,
        ico: 'iconfont icon-xiazai',
        label: '导出数据',
        click () { console.log(this.label) }
      }
    ]
  },
  {
    type: 1,
    id: 10,
    ico: 'iconfont icon-url',
    label: '发布站',
    click (ctx) {
      window.open('http://www.dybox.top')
    }
  }
]
