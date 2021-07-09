<template>
  <div style="width: 256px">
    <a-menu
      :selectedKeys="collapsed ? [] : selectedKeys"
      :openKeys.sync="openKeys"
      mode="inline"
      theme="light"
    >
      <template v-for="item in menuData">
        <a-menu-item v-if="!item.children" :key="item.path" @click="() => $router.push({path:item.key})">
          <a-icon v-if="item.meta && item.meta.icon" :type="item.meta && item.meta.icon" />
          <span>{{ item.meta.title }}</span>
        </a-menu-item>
        <sub-menu v-else :key="item.path" :menu-info="item" />
      </template>
    </a-menu>
  </div>
</template>

<script>
import SubMenu from './SubMenu.vue'
import {check} from '../../../utils/modules/auth'
export default {
  components: {
    SubMenu
  },
  watch:{
    "$route.path":function(val){
      this.selectedKeys = this.selectedKeysMap[val]
      this.openKeys = this.collapsed ? [] : this.openKeysMap[val]
    }
  },
  data() {
    this.selectedKeysMap = {}
    this.openKeysMap = {}
    const menuData = this.getMenuData(this.$router.options.routes);
    return {
      collapsed: false,
      menuData,
      selectedKeys: this.selectedKeysMap[this.$route.path],
      openKeys:this.collapsed ? [] : this.openKeysMap[this.$router.path]
    };
  },
  // mounted(){
  //   console.log(this.$router);
  // },
  methods: {
    toggleCollapsed() {
      this.collapsed = !this.collapsed;
    },

    getMenuData(routes = [],parentKeys = [],selectedKey){
      const menuData = []
      // 循环路由
      for(let item of routes){
        if(item.meta && item.meta.authority && !check(item.meta.authority)){
          break;
        }
        if(item.name && !item.hideInMenu){
          this.openKeysMap[item.path] = parentKeys
          this.selectedKeysMap[item.path] = [selectedKey || item.path]
          const newItem = {...item}
          delete newItem.children
          if(item.children && !item.hideChildrenInMenu){
            newItem.children = this.getMenuData(item.children,[...parentKeys,item.path])
          }else{
            this.getMenuData(item.children,selectedKey ? parentKeys : [...parentKeys,item.path],selectedKey || item.path)
          }
          menuData.push(newItem)
        }else if(!item.hideInMenu && !item.hideChildrenInMenu && item.children){
          menuData.push(...this.getMenuData(item.children,[...parentKeys,item.path]))
        }
      }
      return menuData
    }
  },
};
</script>
