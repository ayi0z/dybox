<template>
  <div>
    <el-submenu v-if="md.type == 0" :index="pid + '-' + md.id" @click="md.click ? menuclick(md) : null">
      <template v-if="md.template" slot="title">
        <i v-if="md.template.ico" :class="md.template.ico"></i>
        <img style="height: 1.6rem; vertical-align: text-bottom;"
          v-else-if="md.template.img"
          :src="md.template.img"
          :alt="md.template.label">
        <span slot="title">{{md.template.label}}</span>
      </template>
      <div v-if="md.items">
        <side-menu-item
          v-for="mi in md.items"
          :key="mi.id"
          :pid="pid + '-' + md.id"
          :md="mi">
        </side-menu-item>
      </div>
    </el-submenu>
    <el-menu-item
      v-else-if="md.type == 1"
      :index="pid + '-' + md.id"
      @click="md.click ? menuclick(md) : null">
      <i v-if="md.ico" :class="md.ico"></i>
      <img v-if="md.img" style="height: 1.6rem; vertical-align: text-bottom;" :src="md.img" :alt="md.label">
      <span slot="title">{{md.label}}</span>
      <div v-if="md.items">
        <side-menu-item
          v-for="mi in md.items"
          :key="mi.id"
          :pid="pid + '-' + md.id"
          :md="mi"></side-menu-item>
      </div>
    </el-menu-item>
  </div>
</template>

<script>
export default {
  name: 'SideMenuItem',
  methods: {
    menuclick (e) {
      e.click(this)
    }
  },
  props: {
    pid: '',
    md: Object
  }
}
</script>

<style lang="stylus" scoped>

</style>
