<template>
  <page-header-wrapper :title="false" content="开放平台参数配置">
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
      >
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
    title: '更新时间',
    dataIndex: 'update_time',
    sorter: true
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
    CreateForm
  },
  data() {
    this.columns = columns
    return {
      // create model
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
  computed: {},
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
    }
  }
}
</script>
