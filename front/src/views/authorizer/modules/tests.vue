<template>
  <div>
    <a-card :bordered="false" title="小程序体验者列表">
      <a-button slot="extra" type="primary" @click="changeDomain">
        添加
      </a-button>
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
            <a-divider type="vertical"/>
            <a-popconfirm title="确定删除该记录吗？" @confirm="handleDel(record.userstr)">
                <a>删除</a>
            </a-popconfirm>
          </template>
        </span>
      </s-table>
    </a-card>
    <a-modal
      v-model="changeDomainModal"
      title="新增小程序体验者"
      @cancel="modalCancel"
      @ok="modalOk">
      <a-form :form="form" v-bind="formLayout">
        <!-- 检查是否有 id 并且大于0，大于0是修改。其他是新增，新增不显示主键ID -->
        <a-form-item label="微信号">
          <a-input v-decorator="['wechat_id']" placeholder="微信号"/>
        </a-form-item>
        <a-form-item label="备注">
          <a-input v-decorator="['remark']" placeholder="可填写体验者名字或其他内容"/>
        </a-form-item>
      </a-form>
    </a-modal>
  </div>
</template>

<script>
import {getTests, bindTester, unbindTester} from '@/api/miniprogram'
import {STable} from "@/components";
import {getPlatform} from "@/api/platform";

const columns = [
  {
    title: '微信号',
    dataIndex: 'wechat_id'
  },
  {
    title: 'userstr',
    dataIndex: 'userstr'
  },
  {
    title: 'remark',
    dataIndex: 'remark'
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

// 表单字段
const fields = ['wechat_id']

export default {
  name: 'tests',
  components: {STable},
  data() {
    this.formLayout = {
      labelCol: {
        xs: {span: 24},
        sm: {span: 7}
      },
      wrapperCol: {
        xs: {span: 24},
        sm: {span: 13}
      }
    }
    return {
      id: null,
      profile: null,
      columns: columns,
      changeDomainModal: false,
      form: this.$form.createForm(this),
      loadData: parameter => {
        const requestParameters = Object.assign({}, parameter, this.queryParam)
        return getTests({id: this.id})
          .then(res => {
            return res.data
          })
      },
    }
  },
  computed: {
    domain() {
      if (this.profile) {
        return this.profile['authorizer_info']['MiniProgramInfo']['network']
      } else {
        return []
      }
    }
  },
  methods: {
    changeDomain() {
      this.changeDomainModal = true
      let data = {
        requestdomain: this.domain['RequestDomain'] ? this.domain['RequestDomain'].join(';') : '',
        wsrequestdomain: this.domain['WsRequestDomain'] ? this.domain['WsRequestDomain'].join(';') : '',
        uploaddomain: this.domain['UploadDomain'] ? this.domain['UploadDomain'].join(';') : '',
        downloaddomain: this.domain['DownloadDomain'] ? this.domain['DownloadDomain'].join(';') : '',
        udpdomain: this.domain['UDPDomain'] ? this.domain['UDPDomain'].join(';') : '',
        tcpdomain: this.domain['TCPDomain'] ? this.domain['TCPDomain'].join(';') : ''
      }
      this.form.setFieldsValue(data)

    },
    modalOk() {
      this.form.validateFields((errors, values) => {
        if (!errors) {
          bindTester({id: this.id, ...values}).then(res => {
            this.visible = false
            this.form.resetFields()
            this.$message.success(res['msg'])
            setTimeout(() => {
              location.reload()
            }, 1500)
          }).catch(e => {
            console.log(e)
          }).finally(() => {
          })
        }
      })
    },
    modalCancel() {

    },
    handleDel(userstr) {
      console.log(userstr)
      unbindTester({id:this.id, userstr}).then(res => {
        this.$message.success(res['msg'])
        setTimeout(() => {
          location.reload()
        }, 1500)
      }).catch(e => {
        console.log(e)
      })
    },
  },
  created() {
    const {id} = this.$route.query
    this.id = id
    // 防止表单未注册
    fields.forEach(v => this.form.getFieldDecorator(v, {}))
  },
  mounted() {

  }
}
</script>

<style lang="less" scoped>

</style>