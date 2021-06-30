<template>
  <a-layout id="components-layout-demo-custom-trigger">
    <BaseSide :collapsed="collapsed"/>
    <a-layout>
      <BaseHeader :collapsed="collapsed" @fold="onFold()"/>
      <a-layout-content
        :style="{ margin: '24px 16px', padding: '24px', background: '#fff', minHeight: '280px' }"
      >
        <router-view></router-view>
      </a-layout-content>
    </a-layout>
  </a-layout>
</template>
<script>
import BaseSide from './components/Side.vue'
import BaseHeader from './components/Header.vue'
export default {
  name:'home',
  components:{
    BaseSide,
    BaseHeader
  },
  data() {
    return {
      collapsed:false
    };
  },
  methods:{
    // 折叠操作
    onFold(){
      this.collapsed = !this.collapsed
    }
  }
}
</script>
<style lang="less" scoped>
.ant-layout{
  height: 100%;
}
#components-layout-demo-custom-trigger .trigger {
  font-size: 18px;
  line-height: 64px;
  padding: 0 24px;
  cursor: pointer;
  transition: color 0.3s;
}

#components-layout-demo-custom-trigger .trigger:hover {
  color: #1890ff;
}
</style>