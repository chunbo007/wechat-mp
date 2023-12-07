<template>
  <page-header-wrapper :title="false">
    <a-card :bordered="false" :loading="loading" title="线上版本">
      <a-row v-if="release_info">
        <a-col span="8">
          <a-descriptions>
            <a-descriptions-item :span="3" label="版本号">
              {{ release_info['release_version'] }}
            </a-descriptions-item>
            <a-descriptions-item :span="3" label="发布时间">
              {{ release_info['release_time'] | momentUnix }}
            </a-descriptions-item>
            <a-descriptions-item :span="3" label="版本描述">
              {{ release_info['release_desc'] }}
            </a-descriptions-item>
          </a-descriptions>
        </a-col>
        <a-col v-if="release_info['release_qrcode']" span="16">
          <img :src="'data:image/jpge;base64,' + release_info['release_qrcode']" alt="" width="150">
        </a-col>
      </a-row>
      <span v-else> 尚未提交线上版本 </span>
    </a-card>
    <a-card :bordered="false" :loading="loading" title="审核版本">
      <span v-if="audit_info.errcode === 85058"> 暂无提交审核的版本或者版本已发布上线 </span>
      <a-descriptions v-else-if="audit_info.errcode === 0">
        <a-dropdown slot="extra">
          <a-menu slot="overlay">
            <a-menu-item v-for="(item) in audit_option" :key="item.value">{{ item.content }}</a-menu-item>
          </a-menu>
          <a-button type="primary"> 操作
            <a-icon type="down"/>
          </a-button>
        </a-dropdown>
        <a-descriptions-item :span="3" label="版本号">
          {{ audit_info['user_version'] }}
        </a-descriptions-item>
        <a-descriptions-item :span="3" label="审核ID">
          {{ audit_info['auditid'] }}
        </a-descriptions-item>
        <a-descriptions-item :span="3" label="提交时间">
          {{ audit_info['submit_audit_time'] | momentUnix }}
        </a-descriptions-item>
        <a-descriptions-item :span="3" label="版本描述">
          {{ audit_info['user_desc'] }}
        </a-descriptions-item>
        <a-descriptions-item :span="3" label="审核状态">
          {{ enum_data.audit_status[audit_info['status']] }}
        </a-descriptions-item>
        <a-descriptions-item v-if="audit_info['status'] === 1" :span="3" label="驳回原因">
          {{ audit_info['reason'] }}
        </a-descriptions-item>
      </a-descriptions>
      <span v-else> {{ audit_info['errmsg'] }} </span>
    </a-card>
    <a-card :bordered="false" :loading="loading" title="体验版本">
      <template v-if="exp_info">
        <a-dropdown slot="extra">
          <a-menu slot="overlay">
            <a-menu-item v-for="(item) in audit_option" :key="item.value">{{ item.content }}</a-menu-item>
          </a-menu>
          <a-button type="primary"> 操作
            <a-icon type="down"/>
          </a-button>
        </a-dropdown>
        <a-col span="8">
          <a-descriptions v-if="exp_info">
            <a-descriptions-item :span="3" label="版本号">
              {{ exp_info['exp_version'] }}
            </a-descriptions-item>
            <a-descriptions-item :span="3" label="发布时间">
              {{ exp_info['exp_time'] | momentUnix }}
            </a-descriptions-item>
            <a-descriptions-item :span="3" label="版本描述">
              {{ exp_info['exp_desc'] }}
            </a-descriptions-item>
          </a-descriptions>
        </a-col>
        <a-col v-if="exp_info['exp_qrcode']" span="16">
          <img :src="'data:image/jpge;base64,' + exp_info['exp_qrcode']" alt="" width="150">
        </a-col>
      </template>
      <template v-else>
        <div style="text-align: center; margin: 60px 0">
          <p>尚未提交体验版</p>
          <a-button type="primary" @click="() => this.modal_visible = true">
            提交代码
          </a-button>
        </div>
      </template>
    </a-card>
    <a-modal
      :confirm-loading="modal_loading"
      :visible="modal_visible"
      title="提交代码"
      width="700px"
      @cancel="modalCancel"
      @ok="modalOk"
    >
      <a-form :form="form" v-bind="formLayout">
        <a-form-item help="第三方平台小程序模板库的模板id。需从开发者工具上传代码到第三方平台草稿箱，然后从草稿箱添加到模板库"
                     label="模板ID(template_id)">
          <a-select v-decorator="['template_id', {rules: [{required: true, message: '请输入app_id'}]}]"
                    placeholder="请选择">
            <a-select-option disabled value="-1">
              <div class="normal_flex">
                <p style="width:100px; margin:0">模板ID</p>
                <p style="width:100px; margin:0">版本号</p>
                <p style="flex:1; margin:0">模板描述</p>
              </div>
            </a-select-option>
            <a-select-option v-for="(item, i) in code_template" :key="item['template_id']">
              <div class="normal_flex">
                <p style="width:100px; margin:0">{{ item['template_id'] }}</p>
                <p style="width:100px; margin:0">{{ item['user_version'] }}</p>
                <p style="flex:1; margin:0">{{ item['user_desc'] | truncate(15) }}</p>
              </div>
            </a-select-option>
          </a-select>
        </a-form-item>
        <a-form-item label="ext.json配置(ext_json)">
          <div slot="help">用于控制ext.json配置文件的内容的参数 <a
            href="https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/code/commit.html"
            target="_blank">提交代码api说明</a></div>
          <a-textarea v-decorator="['ext_json', {rules: [{required: true, message: '请输入app_id'}]}]"/>
        </a-form-item>
        <a-form-item help="代码版本号，开发者可自定义(长度不超过64个字符)" label="代码版本号(user_version)">
          <a-input v-decorator="['user_version', {rules: [{required: true, message: '请输入secret'}]}]"/>
        </a-form-item>
        <a-form-item help="代码版本描述，开发者可自定义" label="版本描述(user_desc)">
          <a-input v-decorator="['user_desc', {rules: [{required: true, message: '请输入token'}]}]"/>
        </a-form-item>
      </a-form>
    </a-modal>
  </page-header-wrapper>
</template>

<script>
import {getDetail} from '@/api/miniprogram'
import data from "@/config/data";

export default {
  name: 'Version',
  data() {
    return {
      enum_data: data,
      release_info: undefined,
      exp_info: undefined,
      audit_info: {},
      code_template: [],
      loading: true,
      modal_visible: true,
      modal_loading: false,
      formLayout: {
        labelCol: {
          xs: {span: 24},
          sm: {span: 7}
        },
        wrapperCol: {
          xs: {span: 24},
          sm: {span: 17}
        }
      },
      form: this.$form.createForm(this)
    }
  },
  methods: {
    getDetail(id) {
      this.loading = true
      getDetail({id}).then(res => {
        this.release_info = res.data.version?.release_info
        this.exp_info = res.data.version?.exp_info
        this.audit_info = res.data.version.audit_info
        this.code_template = res.data.code_template?.template_list
        this.loading = false
      })

    },
    modalOk() {
      // this.modal_visible = false
      this.form.validateFields((errors, values) => {
        console.log(errors, values)
      })
    },
    modalCancel() {
      this.modal_visible = false
    },
  },
  computed: {
    audit_option() {
      if (this.audit_info?.status === 0) {
        return [{
          content: '提交发布',
          value: 4,
        }]
      }
      if (this.audit_info?.status === 1 || this.audit_info?.status === 3) {
        return [{
          content: '提交审核',
          value: 1,
        }]
      }
      if (this.audit_info?.status === 2 || this.audit_info?.status === 4) {
        return [{
          content: '加急审核',
          value: 2,
        }, {
          content: '撤回审核',
          value: 3,
        }]
      }
      return []
    }
  },
  created() {
    const {id} = this.$route.query
    this.getDetail(id)
  },
}
</script>

<style lang="less" scoped>
.normal_flex {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
}
</style>