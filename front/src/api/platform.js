import request from '@/utils/request'

const api = {
  Add: '/platform/add',
  List: '/platform/list',
  Edit: '/platform/edit',
  Delete: '/platform/delete'
}

export function addPlatform(parameter) {
  return request({
    url: api.Add,
    method: 'post',
    data: parameter
  })
}

export function getPlatform(parameter) {
  return request({
    url: api.List,
    method: 'get',
    params: parameter
  })
}

export function editPlatform(parameter) {
  return request({
    url: api.Edit,
    method: 'post',
    data: parameter
  })
}

export function deletePlatform(parameter) {
  return request({
    url: api.Delete,
    method: 'post',
    data: parameter
  })
}
