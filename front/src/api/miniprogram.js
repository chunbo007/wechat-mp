import request from '@/utils/request'

const api = {
  detail: '/miniprogram/detail',
  commit: '/miniprogram/commit',
}

export function getDetail(parameter) {
  return request({
    url: api.detail,
    method: 'post',
    data: parameter
  })
}

export function commit(parameter) {
  return request({
    url: api.commit,
    method: 'post',
    data: parameter
  })
}
