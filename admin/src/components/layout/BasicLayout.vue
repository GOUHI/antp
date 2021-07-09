<template>
  <a-layout id="components-layout-demo-side" style="min-height: 100vh" >
    <a-layout-sider v-model="collapsed" collapsible :trigger="null" width="256px" theme="light">
      <div class="logo" />
      <SideMenu />
    </a-layout-sider>
    <a-layout>
      <a-layout-header style="background: #fff; padding: 0" >
        <a-icon class="trigger" :type="collapsed ? 'menu-unfold' : 'menu-fold'" @click="collapsed = !collapsed"/>
        <Header />
      </a-layout-header>
      <a-layout-content style="margin: 0 16px">
        <a-breadcrumb style="margin: 16px 0" v-if="breadCrumbData.length > 0">
          <a-breadcrumb-item v-for="(item,index) in breadCrumbData" :key="index">{{item}}</a-breadcrumb-item>
        </a-breadcrumb>
        <div :style="{ padding: '24px', background: '#fff', minHeight: '360px' }">
          <router-view></router-view>
        </div>
      </a-layout-content>
    </a-layout>
  </a-layout>
</template>
<script>
import SideMenu from './components/SideMenu.vue'
import Header from './components/Header.vue';
export default {
  components: { 
    SideMenu,
    Header 
  },
  data() {
    return {
      collapsed: false,
      breadCrumbData:[],
      routeData:{}
    };
  },
  watch:{
    // 监听路由变化
    "$route":function(v){
      this.routeData = v
      this.getBreadCrumbData()
    }
  },
  mounted(){
    this.routeData = this.$route
    this.getBreadCrumbData()
  },
  methods:{
    getBreadCrumbData(){
      this.breadCrumbData = []
      this.routeData.matched.forEach(item=>{
        if(item.meta && item.meta.title){
          this.breadCrumbData.push(item.meta.title)
        }
      })
    }
  }
};
</script>
<style lang="less" scoped>
.trigger{
  padding: 0 20px;
  line-height: 64px;
}
.trigger:hover{
  color: #409EFF;
}
</style>

<style>
#components-layout-demo-side .logo {
  height: 32px;
  background: rgb(0 0 0 / 20%);
  margin: 16px;
}
</style>