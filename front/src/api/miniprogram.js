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
  SetDomain: '/miniprogram/setDomain',
  getPcAuthorizerUrl: '/miniprogram/getPcAuthorizerUrl',
  getTests: '/miniprogram/getTests',
  bindTester: '/miniprogram/bindTester',
  unbindTester: '/miniprogram/unbindTester',
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

export function setDomain(parameter) {
  return request({
    url: api.SetDomain,
    method: 'post',
    data: parameter
  })
}

export function getPcAuthorizerUrl(parameter) {
  return request({
    url: api.getPcAuthorizerUrl,
    method: 'post',
    data: parameter
  })
}

export function getTests(parameter) {
  return request({
    url: api.getTests,
    method: 'post',
    data: parameter
  })
}

export function bindTester(parameter) {
  return request({
    url: api.bindTester,
    method: 'post',
    data: parameter
  })
}

export function unbindTester(parameter) {
  return request({
    url: api.unbindTester,
    method: 'post',
    data: parameter
  })
}