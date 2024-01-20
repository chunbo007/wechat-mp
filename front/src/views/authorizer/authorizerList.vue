<template>
  <page-header-wrapper :title="false">
    <template v-slot:content>
      <div class="normal_flex">
        <div class="blue_circle"></div>
        <div>授权帐号指的是获得公众号或者小程序管理员授权的帐号，服务商可为授权帐号提供代开发、代运营等服务。</div>
      </div>
    </template>
    <a-card :bordered="false">
      <div class="table-page-search-wrapper">
        <a-form layout="inline">
          <a-row :gutter="48">
            <a-col :md="8" :sm="24">
              <a-form-item label="名称">
                <a-input v-model="queryParam['nick_name']" placeholder=""/>
              </a-form-item>
            </a-col>
            <a-col :md="8" :sm="24">
              <a-form-item label="appid">
                <a-input v-model="queryParam.appid" placeholder=""/>
              </a-form-item>
            </a-col>
            <span :style="advanced && { float: 'right', overflow: 'hidden' } || {} "
                  class="table-page-search-submitButtons">
                <a-button type="primary" @click="$refs.table.refresh(true)">查询</a-button>
                <a-button style="margin-left: 8px" @click="() => this.queryParam = {}">重置</a-button>
              </span>
          </a-row>
        </a-form>
      </div>

      <div class="table-operator">
        <a-button :disabled="refreshButton" icon="sync" type="primary" @click="refresh">刷新</a-button>
        <a-button icon="plus" type="primary" @click="addAuthorizer">新增授权</a-button>
      </div>

      <s-table
        ref="table"
        :columns="columns"
        :data="loadData"
        rowKey="id"
        showPagination="auto"
        size="default"
        :scroll="{ x: 1000 }"
      >
        <span slot="app_type" slot-scope="text">
          {{ enumData["app_type"][text] }}
        </span>
        <span slot="register_type" slot-scope="text">
          {{ enumData["register_type"][text] }}
        </span>
        <span slot="account_status" slot-scope="text">
          {{ enumData["account_status"][text] }}
        </span>
        <span slot="is_phone" slot-scope="text">
          {{ enumData["true_or_false"][text] }}
        </span>
        <span slot="is_email" slot-scope="text">
          {{ enumData["true_or_false"][text] }}
        </span>
        <span slot="verify_info" slot-scope="text">
          {{ enumData["verify_info"][text] }}
        </span>
        <span slot="action" slot-scope="_, row">
          <template>
            <a @click="originalMessage(row.appid)">原始报文</a>
            <template v-if="row.app_type === 1">
            <a-divider type="vertical"/>
            <a @click="detail(row.id)">版本管理</a>
            </template>
            <a-divider type="vertical"/>
            <a-dropdown>
              <a-menu slot="overlay">
                <a-menu-item>
                  <a @click="getToken(row.appid)">获取token</a>
                </a-menu-item>
                <a-menu-item>
                  <a @click="getTokenLink(row.appid)">获取token外链</a>
                </a-menu-item>
                <a-menu-item>
                  <a @click="getRefreshToken(row.appid)">复制refresh_token</a>
                </a-menu-item>
              </a-menu>
              <a>更多<a-icon type="down"/></a>
            </a-dropdown>
          </template>
        </span>
      </s-table>
    </a-card>
    <a-modal
      :footer="null"
      :visible="jsonDataVisible"
      title="原始报文"
      width="1000px"
      @cancel="() => jsonDataVisible = false"
    >
      <json-viewer
        :copyable="{copyText: '复制', copiedText: '复制成功'}"
        :expand-depth='3'
        :value="jsonData"
        expanded
      ></json-viewer>
    </a-modal>
    <a-modal
      :footer="null"
      :visible="addAuthorizerVisible"
      title="新增授权"
      width="600px"
      @cancel="() => addAuthorizerVisible = false"
    >
      <a-card :loading="addAuthorizerLoading">
        <a-row>
          <a-col :span="8">
            PC 版授权链接
          </a-col>
          <a-col :span="8">
            在电脑浏览器里打开后，使用微信扫码
          </a-col>
          <a-col :span="8" style="text-align: center">
            <a target="_blank" @click="onCopy(pcAuthorizerUrl)">复制链接</a>
          </a-col>
        </a-row>
        <a-row>
          <a-col :span="8">
            H5 版授权链接
          </a-col>
          <a-col :span="8">
            在手机微信里直接访问授权链接
          </a-col>
          <a-col :span="8" style="text-align: center">
            <a target="_blank" @click="onCopy(mobileAuthorizerUrl)">复制链接</a>
          </a-col>
        </a-row>
      </a-card>
    </a-modal>
  </page-header-wrapper>
</template>

<script>
import data from "@/config/data";
import {Ellipsis, STable} from '@/components'
import {getAuthorizer, getRefreshToken, getToken, originalMessage, refresh} from '@/api/authorizer'
import {getPcAuthorizerUrl} from '@/api/miniprogram'
import Message from "ant-design-vue/lib/message"
import JsonViewer from 'vue-json-viewer'
import {mapState} from "vuex";

const columns = [
  {
    title: 'AppId',
    dataIndex: 'appid',
    width: 180
  },
  {
    title: '名称',
    dataIndex: 'nick_name',
    width: 200
  },
  {
    title: '账号类型',
    dataIndex: 'app_type',
    scopedSlots: {customRender: 'app_type'},
    width: 100
  },
  {
    title: '授权时间',
    dataIndex: 'auth_time',
    width: 200

  },
  {
    title: '主体信息',
    dataIndex: 'principal_name',
    width: 250
  },
  {
    title: '账号状态',
    dataIndex: 'account_status',
    width: 200,
    scopedSlots: {customRender: 'account_status'}
  },
  {
    title: '注册类型',
    dataIndex: 'register_type',
    width: 200,
    ellipsis: true,
    scopedSlots: {customRender: 'register_type'}
  },
  {
    title: '已绑手机号',
    dataIndex: 'is_phone',
    width: 200,
    scopedSlots: {customRender: 'is_phone'}
  },
  {
    title: '已绑邮箱',
    dataIndex: 'is_email',
    width: 200,
    scopedSlots: {customRender: 'is_email'}
  },
  {
    title: '认证类型',
    dataIndex: 'verify_info',
    width: 200,
    scopedSlots: {customRender: 'verify_info'}
  },
  {
    title: '原始ID',
    dataIndex: 'user_name',
    width: 180
  },
  {
    title: '更新时间',
    dataIndex: 'update_time',
    width: 200
  },
  {
    title: '操作',
    dataIndex: 'action',
    width: 250,
    fixed: 'right',
    scopedSlots: {customRender: 'action'}
  }
]

export default {
  name: 'account',
  components: {
    STable,
    Ellipsis,
    JsonViewer
  },
  data() {
    return {
      jsonData: {},
      // create model
      enumData: data,
      columns: columns,
      visible: false,
      confirmLoading: false,
      mdl: null,
      // 高级搜索 展开/关闭
      advanced: false,
      // 查询参数
      queryParam: {},
      // 加载数据方法 必须为 Promise 对象
      loadData: parameter => {
        const requestParameters = Object.assign({platform_id: this.currentPlatform.id}, parameter, this.queryParam)
        return getAuthorizer(requestParameters)
          .then(res => {
            return res.data
          })
      },
      selectedRowKeys: [],
      selectedRows: [],
      // 刷新按钮
      refreshButton: false,
      jsonDataVisible: false,
      // 新增按钮
      addAuthorizerVisible: false,
      addAuthorizerLoading: false,
      pcAuthorizerUrl: null,
      mobileAuthorizerUrl: null,
    }
  },
  created() {
  },
  computed: {
    ...mapState({
      currentPlatform: state => state.platform.currentPlatform
    })
  },
  methods: {
    refresh() {
      // console.log(this.$store.state.platform.currentPlatform)
      this.refreshButton = true
      refresh({platform_id: this.currentPlatform.id}).then(res => {
        Message.success(res['msg'])
        this.loadData()
      }).finally(() => {
        this.refreshButton = false
      })
    },

    getToken(appid) {
      let params = {
        platform_id: this.currentPlatform.id,
        appid: appid
      }
      getToken(params).then(res => {

        let authorizer_access_token = res['data']['authorizer_access_token']
        navigator.clipboard.writeText(authorizer_access_token)
          .then(() => {
            Message.success('复制成功')
          })
          .catch((error) => {
            Message.error('复制失败' + error)
          });
      }).finally(() => {
      })
    },

    getTokenLink(appid) {
      const url = window.location.origin + '/openapi/getToken?platform_appid=' + this.currentPlatform['app_id'] + '&appid=' + appid
      navigator.clipboard.writeText(url)
        .then(() => {
          Message.success('复制成功')
        })
        .catch((error) => {
          Message.error('复制失败' + error)
        });
    },

    getRefreshToken(appid) {
      let params = {
        platform_id: this.currentPlatform.id,
        appid: appid
      }
      getRefreshToken(params).then(res => {
        let refreshtoken = res['data']['refreshtoken']
        navigator.clipboard.writeText(refreshtoken)
          .then(() => {
            Message.success('复制成功')
          })
          .catch((error) => {
            Message.error('复制失败' + error)
          });
      }).finally(() => {
      })
    },

    originalMessage(appid) {
      let params = {
        platform_id: this.currentPlatform.id,
        appid: appid
      }
      originalMessage(params).then(res => {
        this.jsonDataVisible = true
        this.jsonData = JSON.parse(res['data'])
      })
    },

    detail(id) {
      this.$router.push({
        path: '/authorizer/detail',
        query: {
          id: id
        }
      })
    },

    addAuthorizer() {
      this.addAuthorizerLoading = true
      this.addAuthorizerVisible = true
      getPcAuthorizerUrl({id: this.currentPlatform.id}).then(res => {
        this.pcAuthorizerUrl = res.data['pc_url']
        this.mobileAuthorizerUrl = res.data['mobile_url']
      }).finally(() => {
        this.addAuthorizerLoading = false
      })
    },

    onCopy(text) {
      navigator.clipboard.writeText(window.location.origin + '/auth/authorizer?url=' + text)
        .then(() => {
          Message.success('复制成功，发送给用户打开完成授权')
        })
        .catch((error) => {
          Message.error('复制失败' + error)
        });
    },
  }
}
</script>
<style lang="less" scoped>
.normal_flex {
  display: flex;
  align-items: center;
  flex-wrap: wrap;

  .blue_circle {
    min-width: 10px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #1890ff;
    margin-right: 10px;
  }
}
</style>
