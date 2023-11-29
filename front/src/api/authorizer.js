import request from '@/utils/request'

const api = {
  Refresh: '/authorizer/refresh',
  list: '/authorizer/list',
  getToken: '/authorizer/getToken',
  getRefreshToken: '/authorizer/getRefreshToken',
  originalMessage: '/authorizer/originalMessage',
}

export function getAuthorizer(parameter) {
  return request({
    url: api.list,
    method: 'post',
    data: parameter
  })
}

export function refresh(parameter) {
  return request({
    url: api.Refresh,
    method: 'post',
    data: parameter
  })
}

export function getToken(parameter) {
  return request({
    url: api.getToken,
    method: 'post',
    data: parameter
  })
}

export function getRefreshToken(parameter) {
  return request({
    url: api.getRefreshToken,
    method: 'post',
    data: parameter
  })
}

export function originalMessage(parameter) {
  return request({
    url: api.originalMessage,
    method: 'post',
    data: parameter
  })
}

