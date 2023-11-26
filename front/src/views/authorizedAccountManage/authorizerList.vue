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
        <a-button :disabled="refreshButton" icon="plus" type="primary" @click="refresh">刷新</a-button>
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
        <span slot="action" slot-scope="text">
          <template>
            <a>获取token</a>
            <a-divider type="vertical"/>
            <a>复制refresh_token</a>
            <a-divider type="vertical"/>
            <a>原始报文</a>
          </template>
        </span>
      </s-table>
    </a-card>
  </page-header-wrapper>
</template>

<script>
import data from "@/config/data";
import {Ellipsis, STable} from '@/components'
import {getAuthorizer, refresh} from '@/api/authorizer'
import Message from "ant-design-vue/lib/message";
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
    width: 300,
    fixed: 'right',
    scopedSlots: {customRender: 'action'}
  }
]

export default {
  name: 'account',
  components: {
    STable,
    Ellipsis
  },
  data() {
    return {
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
    }
  },
  created() {
    // getRoleList({ t: new Date() })
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
    }
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
