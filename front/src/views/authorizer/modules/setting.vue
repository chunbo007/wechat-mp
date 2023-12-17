<template>
  <div>
    <a-card :bordered="false" title="小程序服务器域名">
      <a-button slot="extra" type="primary" @click="changeDomain">
        修改
      </a-button>
      <a-descriptions :column=1 bordered>
        <a-descriptions-item label="request合法域名">
          {{ domain['RequestDomain'] | join(';') }}
        </a-descriptions-item>
        <a-descriptions-item label="socket合法域名">
          {{ domain['WsRequestDomain'] | join(';') }}
        </a-descriptions-item>
        <a-descriptions-item label="uploadFile合法域名">
          {{ domain['UploadDomain'] | join(';') }}
        </a-descriptions-item>
        <a-descriptions-item label="downloadFile合法域名">
          {{ domain['DownloadDomain'] | join(';') }}
        </a-descriptions-item>
        <a-descriptions-item label="udp合法域名">
          {{ domain['UDPDomain'] | join(';') }}
        </a-descriptions-item>
        <a-descriptions-item label="tcp合法域名">
          {{ domain['TCPDomain'] | join(';') }}
        </a-descriptions-item>
      </a-descriptions>
    </a-card>
    <a-modal
      v-model="changeDomainModal"
      title="小程序服务器域名"
      width="800px"
      @cancel="domainCancel"
      @ok="domainOk">
      <a-form :form="form" v-bind="formLayout">
        <!-- 检查是否有 id 并且大于0，大于0是修改。其他是新增，新增不显示主键ID -->
        <a-form-item label="request合法域名">
          <a-input v-decorator="['requestdomain']" placeholder="以 https:// 开头。可填写200个域名，域名间请用 ; 分割"/>
        </a-form-item>
        <a-form-item label="socket合法域名">
          <a-input v-decorator="['wsrequestdomain']" placeholder="以 wss:// 开头。可填写200个域名，域名间请用 ; 分割"/>
        </a-form-item>
        <a-form-item label="uploadFile合法域名">
          <a-input v-decorator="['uploaddomain']" placeholder="以 https:// 开头。可填写200个域名，域名间请用 ; 分割"/>
        </a-form-item>
        <a-form-item label="downloadFile合法域名">
          <a-input v-decorator="['downloaddomain']" placeholder="以 https:// 开头。可填写200个域名，域名间请用 ; 分割"/>
        </a-form-item>
        <a-form-item label="udp合法域名">
          <a-input v-decorator="['udpdomain']" placeholder="以 udp:// 开头。可填写200个域名，域名间请用 ; 分割"/>
        </a-form-item>
        <a-form-item label="tcp合法域名">
          <a-input v-decorator="['tcpdomain']" placeholder="以 tcp:// 开头。可填写200个域名，域名间请用 ; 分割"/>
        </a-form-item>
      </a-form>
    </a-modal>
  </div>
</template>

<script>
import {eventBus} from '@/main'
import {setDomain} from "@/api/miniprogram";

// 表单字段
const fields = ['requestdomain', 'wsrequestdomain', 'uploaddomain', 'downloaddomain', 'udpdomain', 'tcpdomain']

export default {
  name: 'Setting',
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
      changeDomainModal: false,
      form: this.$form.createForm(this)
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
    domainOk() {
      this.form.validateFields((errors, values) => {
        if (!errors) {
          for (let key in values) {
            values[key] = values[key] ? values[key].split(';') : [];
          }
          setDomain({id: this.id, ...values}).then(res => {
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
    domainCancel() {

    }
  },
  created() {
    const {id} = this.$route.query
    this.id = id
    // 防止表单未注册
    fields.forEach(v => this.form.getFieldDecorator(v, {}))
  },
  mounted() {
    eventBus.$on('profile-update', (value) => {
      this.profile = JSON.parse(value)
    });
  }
}
</script>

<style lang="less" scoped>

</style>