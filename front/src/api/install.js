import request from '@/utils/request'
import axios from 'axios'
import message from 'ant-design-vue/es/message'

const rawRequest = axios.create({
  baseURL: process.env.VUE_APP_API_BASE_URL,
  timeout: 10000
})

rawRequest.interceptors.response.use(
  response => response.data,
  error => {
    if (error.response?.data) {
      return Promise.reject(error.response.data)
    }
    return Promise.reject(error)
  }
)

export function checkInstall() {
  return request({
    url: '/install/checkInstall',
    method: 'get'
  })
}

export function checkEnv() {
  return request({
    url: '/install/checkEnv',
    method: 'get'
  })
}

export function testDb(data) {
  return request({
    url: '/install/testDb',
    method: 'post',
    data
  })
}

export function install(data) {
  return request({
    url: '/install/install',
    method: 'post',
    data
  })
}

export function testNginxAdmin() {
  return rawRequest.get('/nginxTest/admin').then(data => {
    console.log(data)
    return {
      success: true,
      data,
      message: data?.data?.message || '测试成功'
    }
  }).catch(error => {
    const errorMsg = error?.data?.error_message || error?.msg || error?.message || '测试失败'
    return {
      success: false,
      message: errorMsg
    }
  })
}

export function testNginxWechat() {
  return rawRequest.get('/nginxTest/wechat').then(data => {
    return {
      success: true,
      data,
      message: data?.data?.message || '测试成功'
    }
  }).catch(error => {
    const errorMsg = error?.data?.error_message || error?.msg || error?.message || '测试失败'
    return {
      success: false,
      message: errorMsg
    }
  })
}

export function testNginxOpenapi() {
  return rawRequest.get('/nginxTest/openapi').then(data => {
    return {
      success: true,
      data,
      message: data?.data?.message || '测试成功'
    }
  }).catch(error => {
    const errorMsg = error?.data?.error_message || error?.msg || error?.message || '测试失败'
    return {
      success: false,
      message: errorMsg
    }
  })
}
