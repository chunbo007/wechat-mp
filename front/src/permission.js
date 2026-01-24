import router, {resetRouter} from './router'
import store from './store'
import storage from 'store'
import NProgress from 'nprogress' // progress bar
import '@/components/NProgress/nprogress.less' // progress bar custom style
import notification from 'ant-design-vue/es/notification'
import {domTitle, setDocumentTitle} from '@/utils/domUtil'
import {ACCESS_TOKEN} from '@/store/mutation-types'
import {i18nRender} from '@/locales'
import { checkInstall } from '@/api/install'

NProgress.configure({ showSpinner: false }) // NProgress Configuration

const allowList = ['login', 'register', 'registerResult', 'wx-authorizer', 'wx-callback', 'install'] // no redirect allowList
const loginRoutePath = '/user/login'
const defaultRoutePath = '/dashboard/workplace'
const installRoutePath = '/install'
let installStatusChecked = false

router.beforeEach(async (to, from, next) => {
  NProgress.start() // start progress bar
  to.meta && typeof to.meta.title !== 'undefined' && setDocumentTitle(`${i18nRender(to.meta.title)} - ${domTitle}`)

  // 只在首次访问时检查安装状态
  if (!installStatusChecked) {
    try {
      const res = await checkInstall()
      const installed = res.data.installed

      // 缓存安装状态到 localStorage
      localStorage.setItem('system_installed', installed)
      installStatusChecked = true

      if (!installed) {
        // 未安装，跳转到安装向导
        if (to.path !== installRoutePath) {
          next({ path: installRoutePath })
          NProgress.done()
          return
        }
      } else {
        // 已安装，不允许访问安装页面
        if (to.path === installRoutePath) {
          next({ path: loginRoutePath })
          NProgress.done()
          return
        }
      }
    } catch (error) {
      // 如果检查失败，使用本地缓存的状态
      const cachedStatus = localStorage.getItem('system_installed')
      if (cachedStatus === 'false' && to.path !== installRoutePath) {
        next({ path: installRoutePath })
        NProgress.done()
        return
      } else if (cachedStatus === 'true' && to.path === installRoutePath) {
        next({ path: loginRoutePath })
        NProgress.done()
        return
      }
    }
  } else {
    // 使用缓存的安装状态
    const cachedStatus = localStorage.getItem('system_installed')
    if (cachedStatus === 'false' && to.path !== installRoutePath) {
      next({ path: installRoutePath })
      NProgress.done()
      return
    } else if (cachedStatus === 'true' && to.path === installRoutePath) {
      next({ path: loginRoutePath })
      NProgress.done()
      return
    }
  }

  /* has token */
  const token = storage.get(ACCESS_TOKEN)
  if (token) {
    if (to.path === loginRoutePath) {
      next({ path: defaultRoutePath })
      NProgress.done()
    } else {
      // check login user.roles is null
      if (store.getters.roles.length === 0) {
        // request login userInfo
        store
          .dispatch('GetInfo')
          .then(res => {
            // 根据用户权限信息生成可访问的路由表
            store.dispatch('GenerateRoutes', { token, ...res }).then(() => {
              // 动态添加可访问路由表
              // VueRouter@3.5.0+ New API
              resetRouter() // 重置路由 防止退出重新登录或者 token 过期后页面未刷新，导致的路由重复添加
              store.getters.addRouters.forEach(r => {
                router.addRoute(r)
              })
              // 请求带有 redirect 重定向时，登录自动重定向到该地址
              const redirect = decodeURIComponent(from.query.redirect || to.path)
              if (to.path === redirect) {
                // set the replace: true so the navigation will not leave a history record
                next({ ...to, replace: true })
              } else {
                // 跳转到目的路由
                next({ path: redirect })
              }
            })
          })
          .catch((e) => {
            console.log(e)
            notification.error({
              message: '错误',
              description: '请求用户信息失败，请重试'
            })
            // 失败时，获取用户信息失败时，调用登出，来清空历史保留信息
            store.dispatch('Logout').then(() => {
              next({ path: loginRoutePath, query: { redirect: to.fullPath } })
            })
          })
        store.dispatch('GetPlatform').then((res) => {
        })
      } else {
        next()
      }
    }
  } else {
    if (allowList.includes(to.name)) {
      // 在免登录名单，直接进入
      next()
    } else {
      next({ path: loginRoutePath, query: { redirect: to.fullPath } })
      NProgress.done() // if current page is login will not trigger afterEach hook, so manually handle it
    }
  }
})

router.afterEach(() => {
  NProgress.done() // finish progress bar
})
