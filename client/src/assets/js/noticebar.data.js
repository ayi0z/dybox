module.exports = [{
  name: 'invalidmotoken',
  type: 'info',
  msgcontent: '请输入正确的有效的观影密令！=> 关注ymlshow公众号免费获取观影密令。'
},
{
  name: 'reqfail',
  type: 'info',
  msgcontent: '请求异常！=> 请到微信公众号(ymlshow)留言反馈问题。'
},
{
  name: 'noepisodes',
  type: 'info',
  msgcontent: 'Oops！暂时没有可播放片源！=> 请关注微信公众号(ymlshow)留言预约，第一时间获取片源信息。'
},
{
  name: 'loadmedias',
  type: 'loading',
  msgcontent: '正在加载媒体数据,请稍后...'
},
{
  name: 'loadepisodes',
  type: 'loading',
  msgcontent: '正在加载分集列表数据,请稍后...'
},
{
  name: 'gotoscreen',
  type: 'action',
  msgcontent: '影片已就绪，点击这里观看',
  action (ctx) {
    document.getElementById('app').scrollTop = 0
    ctx.closenoticebar()
  }
}]
