<template>
  <div id="app" class="app">
    <router-view/>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style lang="stylus" scoped>
  .app
    overflow-y:auto
    height: 100%
    box-shadow: 0rem 1rem 35rem 0rem #252b42 inset
    background-color: #2c444c
    scroll-behavior: smooth
</style>
