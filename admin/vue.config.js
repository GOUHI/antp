const path = require('path')

// 地址指向
// function resolve (dir) {
//   return path.join(__dirname, './', dir)
// }

module.exports = {
  chainWebpack: (config) => {
    config.resolve.symlinks(true) //热更新
  },
  pages: {
    index: {
      entry: 'src/main.js',
      template: 'index.html',
      filename: 'index.html',
      chunks: ['chunk-vendors', 'chunk-common', 'index']
    }
  },
  configureWebpack: (config) => {
    Object.assign(config, {
      // 开发生产共同配置
      resolve: {
        extensions: ['.js', '.vue', '.json'],//请求本地json
        alias: {
          '@': path.resolve(__dirname, './src'),
          '@c': path.resolve(__dirname, './src/components'),
          '@p': path.resolve(__dirname, './src/pages')
        } // 别名配置
      }
    })
  },
  css: {
    loaderOptions: {
      less: {
        // globalVars
        javascriptEnabled:true
      }
    }
  },
  // configureWebpack: {
  //   resolve: {
  //     alias: {
  //       model: path.resolve(__dirname, 'src/js/model/'),
  //       js: path.resolve(__dirname, 'src/js/'),
  //       components: path.resolve(__dirname, 'src/components/')
  //     }
  //   },
  //   plugins: [
  //     new webpack.ProvidePlugin({
  //       R: [path.resolve(__dirname, 'src/js/common/request'), 'default'],
  //       C: [path.resolve(__dirname, 'src/js/config/config'), 'default']
  //     })
  //   ]
  // },
    devServer: {
      proxy:{
        '/': {
          target: process.env.VUE_APP_BASE_URL,
          changeOrigin: true,
        }
      }
      // host: '0.0.0.0',
      // port: 8080,
      // open: true,
      // disableHostCheck: true,
      // overlay: {
      //   warnings: false,
      //   errors: true
      // }
    }
  // devServer: {
  //   proxy: {
    // 此处应该配置为开发服务器的后台地址
    // 配置文档： https://cli.vuejs.org/zh/config/#devserver-proxy
    //   '/applet': {
    //     target: 'https://xyd.youline.cn/'
    //   },
      //测试
      // '/admin': {
      //   target: 'http://127.0.0.1:8000/admin',
      //   changeOrigin: true
      // }
      //开发
      //   '/applet': {
      //     target: 'http://localhost:8763/'
      //   },

  //   }
  // }
};
  