<template>
  <a-layout-sider v-model="collapsed" :trigger="null" collapsible>
    <div class="logo" >
        <h3>logo</h3>
    </div>
    <a-menu
      mode="inline"
      :default-selected-keys="['1']"
      :default-open-keys="['sub1']"
      :style="{ borderRight: 0}"
    >
      <!-- 一层菜单循环 -->
      <template v-for="(firstItem) in menuData">
        <!-- 判断一层是否有子级 -->
        <template v-if="firstItem.subMenus && firstItem.subMenus.length > 0">
          <a-sub-menu :key="firstItem.id">
            <span slot="title"><a-icon :type="firstItem.icon" />{{collapsed ? '' : firstItem.name }}</span>
            <template v-if="firstItem.subMenus">
              <template v-for="secondItem in firstItem.subMenus">
                <template v-if="secondItem.subMenus && secondItem.subMenus.length > 0">
                  <a-sub-menu :key="secondItem.id" :title="secondItem.name">
                    <!-- <span slot="title"><a-icon :type="secondItem.icon" />{{collapsed ? '' : secondItem.name }}</span> -->
                    <template v-for="thirdItem in secondItem.subMenus">
                      <a-menu-item v-if="!thirdItem.hide" :key="thirdItem.id">
                        {{thirdItem.name}}
                      </a-menu-item>
                    </template>
                  </a-sub-menu>
                </template>
                <template v-else>
                  <a-menu-item v-if="!secondItem.hide" :key="secondItem.id" @click="toRouter()">
                    <router-link :to="{path:'home/log'}"> {{ secondItem.name }} -</router-link>
                  </a-menu-item>
                </template>
              </template>
            </template>
            <template v-else>
              <a-menu-item>
                {{ firstItem.name }}
              </a-menu-item>
            </template>
          </a-sub-menu>
        </template>
        <!-- 一层没有子级直接显示 -->
        <template v-else>
          <a-menu-item :key="firstItem.id">
            <a-icon :type="firstItem.icon" />
            <span>{{firstItem.name}}</span>
          </a-menu-item>
        </template>
      </template>
    </a-menu>
  </a-layout-sider>
</template>
<script>
export default {
  name:'side',
  props:{
    collapsed:Boolean
  },
  data(){
    return{
      menuData:[
        { id:1,
          name:"用户管理",
          icon:'user',
          subMenus:[
            {id:2,name:'用户列表',hide:false},
            {id:3,name:'用户统计',hide:false,subMenus:[
              {id:4,name:'行为统计',hide:false},
              {id:5,name:'营销统计',hide:false},
              {id:5,name:'3333',hide:true},
            ]},
            {id:6,name:'登录记录',hide:false}
          ]
        },
        { id:7,
          name:"系统管理",
          icon:'laptop',
          subMenus:[
            {id:8,name:'操作日志',hide:false},
            {id:9,name:'修复密码',hide:false},
            {id:10,name:'ssss',hide:true}
          ]
        },
        { id:11,
          name:"控制台",
          icon:'laptop'
        },
      ]
    }
  },
  methods:{
    toRouter(){
      this.$router.push({
        name:'log'
      })
    }
  }
}
</script>
<style lang="less" scoped>
.ant-layout-sider{
  background: #fff;
}

.logo{
  border:1px solid red;
  height: 32px;
  background: #001529;
  background: rgba(255, 255, 255, 0.2);
  margin: 16px;
}
</style>