import request from '@/utils/request'

const api = {
  Authorizer: '/message/list',
  Event: '/message/EventList',
  Forward: '/message/Forward',
}

export function getAuthorizerMessage(parameter) {
  return request({
    url: api.Authorizer,
    method: 'post',
    data: parameter
  })
}

export function getEventMessage(parameter) {
  return request({
    url: api.Event,
    method: 'post',
    data: parameter
  })
}

export function getForwardMessage(parameter) {
  return request({
    url: api.Forward,
    method: 'post',
    data: parameter
  })
}
