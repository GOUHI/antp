<template>
  <div style="width: 256px">
    <a-menu
      :default-selected-keys="['1']"
      :default-open-keys="['2']"
      mode="inline"
      theme="light"
      :inline-collapsed="collapsed"
    >
      <template v-for="item in menuData">
        <a-menu-item v-if="!item.children" :key="item.path">
          <a-icon v-if="item.meta.icon" :type="item.meta.icon" />
          <span>{{ item.meta.title }}</span>
        </a-menu-item>
        <sub-menu v-else :key="item.path" :menu-info="item" />
      </template>
    </a-menu>
  </div>
</template>

<script>
// recommend use functional component
// <template functional>
//   <a-sub-menu :key="props.menuInfo.key">
//     <span slot="title">
//       <a-icon type="mail" /><span>{{ props.menuInfo.title }}</span>
//     </span>
//     <template v-for="item in props.menuInfo.children">
//       <a-menu-item v-if="!item.children" :key="item.key">
//         <a-icon type="pie-chart" />
//         <span>{{ item.title }}</span>
//       </a-menu-item>
//       <sub-menu v-else :key="item.key" :menu-info="item" />
//     </template>
//   </a-sub-menu>
// </template>
// export default {
//   props: ['menuInfo'],
// };
import SubMenu from './SubMenu.vue'
export default {
  components: {
    SubMenu
  },
  data() {
    const menuData = this.getMenuData(this.$router.options.routes);
    return {
      collapsed: false,
      menuData,
    };
  },
  // mounted(){
  //   console.log(this.$router);
  // },
  methods: {
    toggleCollapsed() {
      this.collapsed = !this.collapsed;
    },

    getMenuData(routes){
      const menuData = []
      routes.forEach(item=>{
        if(item.name && !item.hideInMenu){
          const newItem = {...item}
          delete newItem.children
          if(item.children && !item.hideChildrenInMenu){
            newItem.children = this.getMenuData(item.children)
          }
          menuData.push(newItem)
        }else if(!item.hideInMenu && !item.hideChildrenInMenu && item.children){
          // console.log(item.children);
          menuData.push(...this.getMenuData(item.children))
        }
      })

      return menuData
    }
  },
};
</script>
