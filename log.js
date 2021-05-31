// 财务路由
export default [
  {
    path: '/',
    hidden: true,
    component: import('@/components/layout'),
    children: [
      {
        path: 'log',
        name: 'log',
        component: import('@components/log.vue'),
        meta: { title: '操作日志' }
      }
    ]
  }
];