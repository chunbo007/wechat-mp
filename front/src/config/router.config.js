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
    redirect: '/dashboard/workplace',
    children: [
      // dashboard
      {
        path: '/dashboard',
        name: 'dashboard',
        redirect: '/dashboard/workplace',
        component: RouteView,
        meta: { title: 'menu.dashboard', keepAlive: true, icon: bxAnaalyse },
        children: [
          {
            path: '/dashboard/analysis/:pageNo([1-9]\\d*)?',
            name: 'Analysis',
            component: () => import('@/views/dashboard/Analysis'),
            meta: { title: 'menu.dashboard.analysis', keepAlive: false }
          },
          // 外部链接
          {
            path: 'https://www.baidu.com/',
            name: 'Monitor',
            meta: { title: 'menu.dashboard.monitor', target: '_blank', permission: ['customer'] }
          },
          {
            path: '/dashboard/workplace',
            name: 'Workplace',
            component: () => import('@/views/dashboard/Workplace'),
            meta: { title: 'menu.dashboard.workplace', keepAlive: true }
          }
        ]
      },
      // forms
      {
        path: '/form',
        redirect: '/form/base-form',
        component: RouteView,
        meta: { title: 'menu.form', icon: 'form' },
        children: [
          {
            path: '/form/base-form',
            name: 'BaseForm',
            component: () => import('@/views/form/basicForm'),
            meta: { title: 'menu.form.basic-form', keepAlive: true }
          },
          {
            path: '/form/step-form',
            name: 'StepForm',
            component: () => import('@/views/form/stepForm/StepForm'),
            meta: { title: 'menu.form.step-form', keepAlive: true }
          },
          {
            path: '/form/advanced-form',
            name: 'AdvanceForm',
            component: () => import('@/views/form/advancedForm/AdvancedForm'),
            meta: { title: 'menu.form.advanced-form', keepAlive: true }
          }
        ]
      },
      {
        path: '/list',
        name: 'list',
        component: RouteView,
        redirect: '/list/table-list',
        meta: {title: 'menu.list', icon: 'table'},
        children: [
          {
            path: '/list/table-list/:pageNo([1-9]\\d*)?',
            name: 'TableListWrapper',
            hideChildrenInMenu: true, // 强制显示 MenuItem 而不是 SubMenu
            component: () => import('@/views/list/TableList'),
            meta: {title: 'menu.list.table-list', keepAlive: true}
          },
          {
            path: '/list/basic-list',
            name: 'BasicList',
            component: () => import('@/views/list/BasicList'),
            meta: {title: 'menu.list.basic-list', keepAlive: true}
          },
          {
            path: '/list/card',
            name: 'CardList',
            component: () => import('@/views/list/CardList'),
            meta: {title: 'menu.list.card-list', keepAlive: true}
          },
          {
            path: '/list/search',
            name: 'SearchList',
            component: () => import('@/views/list/search/SearchLayout'),
            redirect: '/list/search/article',
            meta: {title: 'menu.list.search-list', keepAlive: true},
            children: [
              {
                path: '/list/search/article',
                name: 'SearchArticles',
                component: () => import('../views/list/search/Article'),
                meta: {title: 'menu.list.search-list.articles'}
              },
              {
                path: '/list/search/project',
                name: 'SearchProjects',
                component: () => import('../views/list/search/Projects'),
                meta: {title: 'menu.list.search-list.projects'}
              },
              {
                path: '/list/search/application',
                name: 'SearchApplications',
                component: () => import('../views/list/search/Applications'),
                meta: {title: 'menu.list.search-list.applications'}
              }
            ]
          }
        ]
      },
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
