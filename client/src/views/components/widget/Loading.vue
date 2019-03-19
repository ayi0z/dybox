<template>
  <div class="loading">
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
  </div>
</template>

<script>
export default {
  name: 'Loading'
}
</script>

<style lang="stylus" scoped>
 .loading
    display: inline
    width: 1rem
    height: .5rem
    margin: 0 auto
    span
      display: inline-block
      width: .05rem
      height: .5rem
      background: 5c78ec
      animation: loading 1.6s ease infinite
    @keyframes loading
      0%,100%
        height: .1rem
        background: 5c78ec
      30%
        height: .2rem
        background: #7afb5f
      60%
        height: .4rem
        background: #ef4991
      80%
        height: .2rem
        background: #ecff25
  .loading span:nth-child(2)
    animation-delay:0.2s
  .loading span:nth-child(3)
    animation-delay:0.4s
  .loading span:nth-child(4)
    animation-delay:0.6s
  .loading span:nth-child(5)
    animation-delay:0.8s
  .loading span:nth-child(4)
    animation-delay:1s
  .loading span:nth-child(5)
    animation-delay:1.2s
  .loading span:nth-child(6)
    animation-delay:1.4s
</style>
