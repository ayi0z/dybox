<template>
  <div class="welcome">
      <p class="title">DYBOX</p>
      <span class="subtitle">ONLINE FREE MOVIES</span>
      <div class="article">
        <p>
            “电影就是每秒24格的真实”
            <span>—— 戈达尔</span>
        </p>
        <p>
            有人就有恩怨，有恩怨就有江湖。人就是江湖，你怎么退出？
            <span>——《东方不败》</span>
        </p>
        <p>
            生活这条狗啊，追的我连从容撒泡尿的时间都没有。
            <span>——《英雄时代》</span>
        </p>
        <p>
            这么多年，牵着你的手，就象左手牵着右手，没有感觉，但砍了去也会疼的厉害。
            <span>——《一声叹息》</span>
        </p>
        <p>
            曾经拥有的，不要忘记；不能得到的，更要珍惜；属于自己的，不要放弃；已经失去的，留做回忆。
            <span>——《曾经》</span>
        </p>
        <p>
            好的爱情是你通过一个人看到整个世界，坏的爱情是你为了一个人舍弃世界。
            <span>——《两小无猜》</span>
        </p>
        <p>
            我知道这世上有人在等我，尽管我不知道我在等谁，但是因为这样，我每天都非常快乐。
            <span>——《返老还童》</span>
        </p>
        <p>
            This path has been placed before you. The choice is yours alone.
            <span>——《星球大战》</span>
        </p>
        <p>
            Death smiles at us all and all a man can do is smile back.
            <span>——《角斗士》</span>
        </p>
        <p>
            距离精彩电影，只差一个<i>观影密令</i>的距离。即刻关注微信公众号<i class="iconfont">&#xe637;</i><i class="wechat">ymlshow</i>获得电影推荐信息，免费领取观影密令。
            <span>—— 优觅Lu电影</span>
        </p>
        <p class="logo">
            <img :src="ymlshowlogojpg" alt="关注公众号优觅Lu电影(ymlshow),免费获取观影密令">
            <img :src="ymlshowqrcode" alt="关注公众号优觅Lu电影(ymlshow),免费获取观影密令">
            <img :src="ymlshowlogopng" alt="关注公众号优觅Lu电影(ymlshow),免费获取观影密令">
            <span>
                即将上映的热门电影预告资讯 先睹为快<br/>
                精挑细选的电影佳作 <i>观影密令</i>免费领取<br/>
                欢迎关注微信公众号 <i>优觅Lu电影</i>
            </span>
        </p>
      </div>
  </div>
</template>

<script>
export default {
  name: 'Welcome',
  computed: {
    ymlshowlogopng () {
      return require('@/assets/imgs/ymlshow@logo.png')
    },
    ymlshowlogojpg () {
      return require('@/assets/imgs/ymlshow@logo.jpg')
    },
    ymlshowqrcode () {
      return require('@/assets/imgs/ymlshow@qrcode.jpg')
    }
  }
}
</script>

<style lang="stylus" scoped >
.welcome
    height: 100%
    width: auto
    font-size: .25rem
    line-height: .5rem
    letter-spacing: .03rem
    color: #ffffff94
    text-align: center
    font-family: cursive
    .title
        width: auto
        text-align: center
        margin-top: 1.5rem
        margin-bottom: .3rem
        z-index: 1
        font-size: 1rem
        text-shadow: 0.1rem 0.02rem 0.2rem #13080847
        font-family: 'Cinzel Decorative', cursive
    .subtitle
        font-size: .2rem
        color: #ffffff
        font-family: 'Cinzel Decorative', cursive
        text-shadow: .01rem .01rem .01rem #010506;
    .article
        text-align: left
        width: 70%
        max-width: 10rem
        margin: .4rem auto
        font-size: .2rem
        line-height: .4rem
        p
            overflow: hidden
            text-shadow: 0rem 0.05rem 0.1rem #0b122d
            padding-top: .5rem
            i
                color: #bf1919
            img
                width: 1rem
            span
                float: right
            .clear
                clear: both
                height: 0
                font-size: 1px
                line-height: 0px;
        .iconfont, .wechat
            color: #00cd00
            text-shadow: none
</style>
