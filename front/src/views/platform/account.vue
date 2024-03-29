<template>
  <page-header-wrapper :title="false">
    <template v-slot:content>
      <div class="normal_flex">
        <div class="blue_circle"></div>
        <div>需要在<a href="https://open.weixin.qq.com/" target="_blank">开放平台</a>后台配置 "授权事件接收配置" 和
          "消息与事件接收配置" 才能生效
        </div>
      </div>
      <div class="normal_flex">
        <div class="blue_circle"></div>
        <div>
          授权事件接收配置：
          <span v-clipboard:copy="authorizer_url" class="text_copy" @click="onCopy">{{ authorizer_url }}</span>
        </div>
      </div>
      <div class="normal_flex">
        <div class="blue_circle"></div>
        <div>
          消息与事件接收配置：
          <span v-clipboard:copy="authorizer_event" class="text_copy" @click="onCopy">{{ authorizer_event }}</span>
        </div>
      </div>
    </template>
    <a-card :bordered="false">
      <div class="table-page-search-wrapper">
        <a-form layout="inline">
          <a-row :gutter="48">
            <a-col :md="8" :sm="24">
              <a-form-item label="名称">
                <a-input v-model="queryParam.name" placeholder=""/>
              </a-form-item>
            </a-col>
            <a-col :md="8" :sm="24">
              <a-form-item label="app_id">
                <a-input v-model="queryParam.app_id" placeholder=""/>
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
        <a-button icon="plus" type="primary" @click="handleAdd">新建</a-button>
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
        <span slot="is_default" slot-scope="text, record">
          {{ enumData['true_or_false'][text] }}
        </span>
        <span slot="action" slot-scope="text, record">
          <template>
            <a @click="handleEdit(record)">编辑</a>
            <a-divider type="vertical"/>
            <a-popconfirm title="确定删除该记录吗？" @confirm="handleDel(record.id)">
                <a>删除</a>
            </a-popconfirm>
          </template>
        </span>
      </s-table>

      <create-form
        ref="createModal"
        :loading="confirmLoading"
        :model="mdl"
        :visible="visible"
        @cancel="handleCancel"
        @ok="handleOk"
      />
    </a-card>
  </page-header-wrapper>
</template>

<script>
import {Ellipsis, STable} from '@/components'
import {addPlatform, deletePlatform, editPlatform, getPlatform} from '@/api/platform'
import CreateForm from './modules/CreateForm'
import data from "@/config/data";
import Message from "ant-design-vue/lib/message";
import Vue from 'vue';
import VueClipboard from 'vue-clipboard2';

VueClipboard.config.autoSetContainer = true;
Vue.use(VueClipboard);

const columns = [
  {
    title: '名称',
    dataIndex: 'name'
  },
  {
    title: 'app_id',
    dataIndex: 'app_id'
  },
  {
    title: 'secret',
    dataIndex: 'secret'
  },
  {
    title: 'token',
    dataIndex: 'token'
  },
  {
    title: 'aes_key',
    dataIndex: 'aes_key'
  },
  {
    title: '是否默认',
    dataIndex: 'is_default',
    scopedSlots: {customRender: 'is_default'}
  },
  {
    title: '更新时间',
    dataIndex: 'update_time',
  },
  {
    title: '操作',
    dataIndex: 'action',
    width: '150px',
    scopedSlots: {customRender: 'action'}
  }
]

export default {
  name: 'account',
  components: {
    STable,
    Ellipsis,
    CreateForm,
  },
  data() {
    return {
      // create model
      columns: columns,
      enumData: data,
      visible: false,
      confirmLoading: false,
      mdl: null,
      // 高级搜索 展开/关闭
      advanced: false,
      // 查询参数
      queryParam: {},
      // 加载数据方法 必须为 Promise 对象
      loadData: parameter => {
        const requestParameters = Object.assign({}, parameter, this.queryParam)
        return getPlatform(requestParameters)
          .then(res => {
            return res.data
          })
      },
      selectedRowKeys: [],
      selectedRows: []
    }
  },
  created() {
    // getRoleList({ t: new Date() })
  },
  computed: {
    authorizer_url() {
      return 'https://' + window.location.hostname + '/wechat'
    },
    authorizer_event() {
      return 'https://' + window.location.hostname + '/wechat/$APPID$'
    }
  },
  methods: {
    handleAdd() {
      this.mdl = null
      this.visible = true
    },
    handleEdit(record) {
      this.visible = true
      this.mdl = {...record}
    },
    handleOk() {
      const form = this.$refs.createModal.form
      this.confirmLoading = true
      form.validateFields((errors, values) => {
        if (!errors) {
          if (values.id > 0) {
            editPlatform(values).then(res => {
              this.visible = false
              form.resetFields()
              this.$refs.table.refresh()
              this.$message.success(res['msg'])
            }).catch(e => {
              console.log(e)
            }).finally(() => {
              this.confirmLoading = false
            })
          } else {
            addPlatform(values).then(res => {
              this.visible = false
              form.resetFields()
              this.$refs.table.refresh()
              this.$message.success(res['msg'])
            }).catch(e => {
              console.log(e)
            }).finally(() => {
              this.confirmLoading = false
            })
          }
        } else {
          this.confirmLoading = false
        }
      })
    },
    handleCancel() {
      this.visible = false
      const form = this.$refs.createModal.form
      form.resetFields() // 清理表单数据（可不做）
    },
    handleDel(id) {
      deletePlatform({id}).then(res => {
        // 刷新表格
        this.$refs.table.refresh()
        this.$message.success(res['msg'])
      }).catch(e => {
        console.log(e)
      })
    },
    onCopy() {
      Message.success('复制成功')
    }
  }
}
</script>
<style lang="less" scoped>
.text_copy {
  color: #1890ff;
  cursor: pointer;
}

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
