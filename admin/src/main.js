import Vue from 'vue'
// 引入ant
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/antd.css';

import App from './App.vue'
import router from './router'
import store from './store'

// 引入样式初始化
import './assets/css/normalize.css'

Vue.config.productionTip = false

Vue.use(Antd);

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
