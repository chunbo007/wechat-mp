import request from '@/utils/request'

const api = {
  Refresh: '/authorizer/refresh',
  list: '/authorizer/list'
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
