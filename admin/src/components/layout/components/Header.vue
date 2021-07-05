<template>
  <!-- <a-layout-header style="background: #fff; padding: 0">
    <a-icon
      class="trigger"
      :type="collapsed ? 'menu-unfold' : 'menu-fold'"
      @click="onUpdate()"
    />
  </a-layout-header> -->
  <div class="header">111
  </div>
</template>
<script>
export default {
  name:'baseHeader',
  props:{
    collapsed:Boolean
  },
  methods:{
    // 折叠菜单操作
    onUpdate(){
      this.$emit('fold')
    }
  }
}
</script>
<style lang="less" scoped>
.header{
  float:right;
}
</style>
