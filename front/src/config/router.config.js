// eslint-disable-next-line
import {BasicLayout, BlankLayout, UserLayout} from '@/layouts'
// eslint-disable-next-line
import {bxAnaalyse} from '@/core/icons'

const RouteView = {
  name: 'RouteView',
  render: h => h('router-view')
}

export const asyncRouterMap = [
  {
    path: '/',
    name: 'index',
    component: BasicLayout,
    meta: { title: 'menu.home' },
    redirect: '/authorizer',
    children: [
      // authorizer
      {
        name: 'authorizer',
        path: '/authorizer',
        redirect: '/authorizer/list',
        component: RouteView,
        meta: {title: '授权管理', keepAlive: true, icon: bxAnaalyse},
        hideChildrenInMenu: true,
        children: [
          {
            name: 'authorizer-list',
            path: '/authorizer/list',
            component: () => import('@/views/authorizer/authorizerList'),
            meta: {title: '授权管理', keepAlive: true, icon: bxAnaalyse},
          },
          {
            name: 'authorizer-detail',
            path: '/authorizer/detail',
            component: () => import('@/views/authorizer/detail'),
            meta: {title: '版本管理', keepAlive: true, icon: bxAnaalyse},
          },
          {
            name: 'authorizer-audit',
            path: '/authorizer/audit',
            component: () => import('@/views/authorizer/submitAudit'),
            meta: {title: '提交审核', keepAlive: true, icon: bxAnaalyse},
          },
        ]
      },
      // platform
      {
        name: 'platform',
        path: '/platform',
        component: () => import('@/views/platform/account'),
        meta: {title: '开放平台', keepAlive: true, icon: bxAnaalyse}
      },
      // message
      {
        name: 'message',
        path: '/message',
        component: () => import('@/views/message/log'),
        meta: {title: '消息日志', keepAlive: true, icon: bxAnaalyse}
      }
    ]
  },
  {
    path: '*',
    redirect: '/404',
    hidden: true
  }
]

/**
 * 基础路由
 * @type { *[] }
 */
export const constantRouterMap = [
  {
    path: '/user',
    component: UserLayout,
    redirect: '/user/login',
    hidden: true,
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import(/* webpackChunkName: "user" */ '@/views/user/Login')
      },
      {
        path: 'register',
        name: 'register',
        component: () => import(/* webpackChunkName: "user" */ '@/views/user/Register')
      },
      {
        path: 'register-result',
        name: 'registerResult',
        component: () => import(/* webpackChunkName: "user" */ '@/views/user/RegisterResult')
      },
      {
        path: 'recover',
        name: 'recover',
        component: undefined
      }
    ]
  },

  // wechat authorizer
  {
    name: 'wechat',
    path: '/wechat',
    component: BlankLayout,
    children: [
      {
        path: 'authorizer',
        name: 'wx-authorizer',
        component: () => import('@/views/wechat/Authorizer')
      },
      {
        path: 'callback',
        name: 'wx-callback',
        component: () => import('@/views/wechat/Callback')
      }
    ]
  },

  {
    path: '/404',
    component: () => import(/* webpackChunkName: "fail" */ '@/views/exception/404')
  }
]
