<template>
  <div class="sidemenu">
    <el-menu
      class="el-menu-vertical"
      background-color="#545c64"
      text-color="#fff"
      active-text-color="#ffd04b"
      :collapse="iscollapse"
    >
      <el-switch
        v-if="false"
        style="margin-left: 1rem; margin-top:1rem;"
        active-color="#3d41445c"
        inactive-color="#3d41445c"
        v-model="iscollapse"
        :active-value="true"
        :inactive-value="false">
      </el-switch>
      <side-menu-item v-for="md in menu_data" :key="md.id" :md="md"></side-menu-item>
    </el-menu>
  </div>
</template>

<script>
import SideMenuItem from './SideMenuItem'
export default {
  name: 'SideMenu',
  components: {
    SideMenuItem
  },
  data () {
    return {
      iscollapse: false,
      menu_data: require('@/assets/js/menu.data.js')
    }
  }
}
</script>

<style lang="stylus" scoped>
  .sidemenu
    height: 100%
    .el-menu-vertical:not(.el-menu--collapse)
      width: 100%
      min-height: 100%
    .el-menu
      border: none
</style>
