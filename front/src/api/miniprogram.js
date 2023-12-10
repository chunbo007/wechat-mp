import request from '@/utils/request'

const api = {
  detail: '/miniprogram/detail',
  commit: '/miniprogram/commit',
  GetCategory: '/miniprogram/getCategory',
  SubmitAudit: '/miniprogram/submitAudit',
  UndoAudit: '/miniprogram/undoAudit',
  SpeedupCodeAudit: '/miniprogram/speedupCodeAudit',
  release: '/miniprogram/release',
  RevertCodeRelease: '/miniprogram/revertCodeRelease',
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

export function getCategory(parameter) {
  return request({
    url: api.GetCategory,
    method: 'post',
    data: parameter
  })
}

export function submitAudit(parameter) {
  return request({
    url: api.SubmitAudit,
    method: 'post',
    data: parameter
  })
}

export function undoAudit(parameter) {
  return request({
    url: api.UndoAudit,
    method: 'post',
    data: parameter
  })
}

export function speedupCodeAudit(parameter) {
  return request({
    url: api.SpeedupCodeAudit,
    method: 'post',
    data: parameter
  })
}

export function release(parameter) {
  return request({
    url: api.release,
    method: 'post',
    data: parameter
  })
}

export function revertCodeRelease(parameter) {
  return request({
    url: api.RevertCodeRelease,
    method: 'post',
    data: parameter
  })
}