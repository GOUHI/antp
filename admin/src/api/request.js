import axios from 'axios'
import Message from 'ant-design-vue/lib/message'

// 创建axios实例
const service = axios.create({
  baseUrl:'/',
//   baseUrl:process.env.VUE_APP_BASE_URL,
  // 超时参数
  timeout:20000
})

// 请求操作
service.interceptors.request.use(
  config => {
      //判断token是否存在
      // const token = window.sessionStorage.getItem('token')
      // if (token) {
      //     config.headers['Authorization'] = token // 让每个请求携带自定义token 请根据实际情况自行修改
      // }
      // config.headers['Content-Type'] = 'application/json'
      return config
  },
  error => {
      // Do something with request error
      console.log(error) // for debug
      Promise.reject(error)
  }
)

// response 拦截器
service.interceptors.response.use(
  response => {
      // console.log(response);
      // 判断浏览器状态
      const code = response.status
      if (code < 200 || code > 300) {
        Message.error(response.message)
        return Promise.reject('error')
      } else {
          if(response.data.code == 200){
              return response.data.data
          }else if(response.data.code === 400){
              Message.error(response.data.msg)
          }
          return null;
      }
  },
  error => {
      let httpStatus = 0
      try {
        httpStatus = error.response.status
      } catch (e) {
          if (error.toString().indexOf('Error: timeout') !== -1) {
              Notification.error({
                  title: '网络请求超时',
                  duration: 2500
              })
              return Promise.reject(error)
          }
          if (error.toString().indexOf('Error: Network Error') !== -1) {
              Notification.error({
                  title: '网络请求错误',
                  duration: 2500
              })
              console.log('error');
              return Promise.reject(error)
          }
      }

      // 当前请求不存在
      if(httpStatus === 404 ){
        Message.error('当前请求不存在，请联系管理员')
      }

      //服务器报错
      if (httpStatus === 500) {
        Message.error('服务器错误，请联系管理员')
      }
      //     MessageBox.confirm(
      //         '登录状态已过期，您可以继续留在该页面，或者重新登录',
      //         '系统提示',
      //         {
      //             confirmButtonText: '重新登录',
      //             cancelButtonText: '取消',
      //             type: 'warning'
      //         }
      //     ).then(() => {
      //         // store.dispatch('LogOut').then(() => {
      //         //   location.reload() // 为了重新实例化vue-router对象 避免bug
      //         // })
      //     })
      // } else if (code === 403) {
      //     router.push({ path: '/401' })
      // } else {
      //     const errorMsg = error.response.data.message
      //     if (errorMsg !== undefined) {
      //         Notification.error({
      //             title: errorMsg,
      //             duration: 3000
      //         })
      //     }
      // }
      return Promise.reject(error)
  }
)
export default service