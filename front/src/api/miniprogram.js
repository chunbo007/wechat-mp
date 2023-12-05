import request from '@/utils/request'

const api = {
  detail: '/miniprogram/detail',
}

export function getDetail(parameter) {
  return request({
    url: api.detail,
    method: 'post',
    data: parameter
  })
}

