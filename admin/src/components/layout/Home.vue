<template>
  <a-layout id="components-layout-demo-custom-trigger">
    <!-- <a-layout-sider v-model="collapsed" :trigger="null" collapsible>
      <div class="logo" />
      <a-menu theme="dark" mode="inline" :default-selected-keys="['1']">
        <a-menu-item key="1">
          <a-icon type="user" />
          <span>nav 1</span>
        </a-menu-item>
        <a-menu-item key="2">
          <a-icon type="video-camera" />
          <span>nav 2</span>
        </a-menu-item>
        <a-menu-item key="3">
          <a-icon type="upload" />
          <span>nav 3</span>
        </a-menu-item>
      </a-menu>
    </a-layout-sider> -->
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
                    <a-menu-item v-if="!secondItem.hide" :key="secondItem.id">
                      {{ secondItem.name }}
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

        <!-- <a-sub-menu key="sub1">
          <span slot="title"><a-icon type="user" />{{collapsed ? '' : '用户管理' }}</span>
          <a-menu-item key="1">
            用户列表
          </a-menu-item>
          <a-menu-item key="2">
            用户统计
          </a-menu-item>
          <a-menu-item key="3">
            登录记录
          </a-menu-item>
          <a-menu-item key="4">
            option4
          </a-menu-item>
        </a-sub-menu>
        <a-sub-menu key="sub2">
          <span slot="title"><a-icon type="laptop" />{{collapsed ? '' : 'subnav 2' }}</span>
          <a-menu-item key="5">
            option5
          </a-menu-item>
          <a-menu-item key="6">
            option6
          </a-menu-item>
          <a-menu-item key="7">
            option7
          </a-menu-item>
          <a-menu-item key="8">
            option8
          </a-menu-item>
        </a-sub-menu>
        <a-sub-menu key="sub3">
          <span slot="title"><a-icon type="notification" />{{collapsed ? '' : 'subnav 3' }}</span>
          <a-menu-item key="9">
            option9
          </a-menu-item>
          <a-menu-item key="10">
            option10
          </a-menu-item>
          <a-menu-item key="11">
            option11
          </a-menu-item>
          <a-sub-menu key="000" title="Submenu">
            <a-menu-item key="7">
              Option 7
            </a-menu-item>
            <a-menu-item key="8">
              Option 8
            </a-menu-item>
          </a-sub-menu>
        </a-sub-menu> -->


      </a-menu>
    </a-layout-sider>
    <a-layout>
      <a-layout-header style="background: #fff; padding: 0">
        <a-icon
          class="trigger"
          :type="collapsed ? 'menu-unfold' : 'menu-fold'"
          @click="() => (collapsed = !collapsed)"
        />
      </a-layout-header>
      <a-layout-content
        :style="{ margin: '24px 16px', padding: '24px', background: '#fff', minHeight: '280px' }"
      >
        <router-view></router-view>
      </a-layout-content>
    </a-layout>
  </a-layout>
</template>
<script>
// import 
export default {
  name:'home',
  data() {
    return {
      collapsed: false,
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
    };
  },
}
</script>
<style lang="less" scoped>
.ant-layout-sider{
  background: #fff;
}
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

#components-layout-demo-custom-trigger .logo {
  border:1px solid red;
  height: 32px;
  background: #001529;
  background: rgba(255, 255, 255, 0.2);
  margin: 16px;
}
</style>