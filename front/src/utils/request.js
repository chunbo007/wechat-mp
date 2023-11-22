import axios from 'axios'
import store from '@/store'
import storage from 'store'
import notification from 'ant-design-vue/es/notification'
import message from 'ant-design-vue/es/message'
import {VueAxios} from './axios'
import {ACCESS_TOKEN} from '@/store/mutation-types'

// 创建 axios 实例
const request = axios.create({
  // API 请求的默认前缀
  baseURL: process.env.VUE_APP_API_BASE_URL,
  timeout: 6000 // 请求超时时间
})

// 异常拦截处理器
const errorHandler = (error) => {
  if (error.response) {
    const data = error.response.data
    // 从 localstorage 获取 token
    const token = storage.get(ACCESS_TOKEN)
    if (error.response.status === 403) {
      notification.error({
        message: 'Forbidden',
        description: data.message
      })
    } else if (error.response.status === 404) {
      notification.error({
        message: '404',
        description: '请求地址不存在'
      })
    } else if (error.response.status === 401) {
      notification.error({
        message: '登录失效',
        description: '登录失效，请重新登录'
      })
      if (token) {
        store.dispatch('RemoveToken').then(() => {
          setTimeout(() => {
            window.location.reload()
          }, 1500)
        })
      } else {
        window.location.reload()
      }
    } else if (error.response.status === 402) {
      notification.error({
        message: '登录超时',
        description: '登录超时，请重新登录'
      })
      if (token) {
        store.dispatch('RemoveToken').then(() => {
          setTimeout(() => {
            window.location.reload()
          }, 1500)
        })
      } else {
        window.location.reload()
      }
    }
  }
  return Promise.reject(error)
}

// request interceptor
request.interceptors.request.use(config => {
  const token = storage.get(ACCESS_TOKEN)
  // 如果 token 存在
  // 让每个请求携带自定义 token 请根据实际情况自行修改
  if (token) {
    config.headers[ACCESS_TOKEN] = token
  }
  return config
}, errorHandler)

// response interceptor
request.interceptors.response.use((response) => {
  if (response.data.code !== 0) {
    message.error(response.data.msg || '请求出错')
    return Promise.reject(response.data.msg)
  }
  return response.data
}, errorHandler)

const installer = {
  vm: {},
  install (Vue) {
    Vue.use(VueAxios, request)
  }
}

export default request

export {
  installer as VueAxios,
  request as axios
}
