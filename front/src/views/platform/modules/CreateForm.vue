<template>
  <a-modal
    :confirmLoading="loading"
    :visible="visible"
    :width="800"
    :title="this.model ? '编辑' : '新增'"
    @cancel="() => { $emit('cancel') }"
    @ok="() => { $emit('ok') }"
  >
    <a-spin :spinning="loading">
      <a-form :form="form" v-bind="formLayout">
        <!-- 检查是否有 id 并且大于0，大于0是修改。其他是新增，新增不显示主键ID -->
        <a-form-item v-show="model && model.id > 0" label="主键ID">
          <a-input v-decorator="['id', { initialValue: 0 }]" disabled/>
        </a-form-item>
        <a-form-item label="名称">
          <a-input v-decorator="['name', {rules: [{required: true, message: '请输入名称'}]}]"/>
        </a-form-item>
        <a-form-item label="app_id">
          <a-input v-decorator="['app_id', {rules: [{required: true, message: '请输入app_id'}]}]"/>
        </a-form-item>
        <a-form-item label="secret">
          <a-input v-decorator="['secret', {rules: [{required: true, message: '请输入secret'}]}]"/>
        </a-form-item>
        <a-form-item label="token">
          <a-input v-decorator="['token', {rules: [{required: true, message: '请输入token'}]}]"/>
        </a-form-item>
        <a-form-item label="aes_key">
          <a-input v-decorator="['aes_key', {rules: [{required: true, message: '请输入aes_key'}]}]"/>
        </a-form-item>
        <a-form-item extra="转发授权事件：验证票据、授权成功、取消授权、授权更新、快速注册企业小程序、快速注册个人小程序、注册试用小程序、试用小程序快速认证、发起小程序管理员人脸核身、申请小程序备案"
                     label="转发授权事件URL">
          <a-input v-decorator="['forward_platform']"/>
        </a-form-item>
        <a-form-item extra="将转发授权事件URL的响应结果返回给平台" label="返回响应结果">
          <a-checkbox v-decorator="['return_forward_platform', { valuePropName: 'checked' }]">
            <!--            将转发授权事件URL的响应结果返回给平台-->
          </a-checkbox>
        </a-form-item>
        <a-form-item extra="转发消息与事件：设置小程序名称、添加类目、提交代码审核、审核结果 会向URL进行事件推送，该参数按规则填写（需包含/$APPID$，如https://www.abc.com/$APPID$/callback），实际接收消息时$APPID$将被替换为公众号或小程序AppId"
                     label="转发消息与事件URL">
          <a-input
            v-decorator="['forward_app', {rules: [{validator: validateForwardApp, message: '转发消息与事件URL需包含$APPID$'}]}]"/>
        </a-form-item>
        <a-form-item extra="将转发消息与事件URL的响应结果返回给平台" label="返回响应结果">
          <a-checkbox v-decorator="['return_forward_app', { valuePropName: 'checked' }]">
            <!--            将转发消息与事件URL的响应结果返回给平台-->
          </a-checkbox>
        </a-form-item>
        <a-form-item :label-col="formLayout.labelCol" :wrapper-col="formLayout.wrapperCol" label="第三方平台解密secret">
          <a-input v-decorator="['third_secret']"/>
        </a-form-item>
        <a-form-item label="设为默认">
          <a-switch v-decorator="['is_default', { valuePropName: 'checked' }]"/>
        </a-form-item>
      </a-form>
    </a-spin>
  </a-modal>
</template>

<script>
import pick from 'lodash.pick'

// 表单字段
const fields = ['id', 'name', 'app_id', 'secret', 'token', 'aes_key', 'forward_platform', 'forward_app', 'return_forward_platform', 'return_forward_app', 'third_secret', 'is_default']

export default {
  props: {
    visible: {
      type: Boolean,
      required: true
    },
    loading: {
      type: Boolean,
      default: () => false
    },
    model: {
      type: Object,
      default: () => null
    }
  },
  data() {
    return {
      form: this.$form.createForm(this),
      formLayout: {
        labelCol: {
          xs: {span: 24},
          sm: {span: 7}
        },
        wrapperCol: {
          xs: {span: 24},
          sm: {span: 13}
        }
      }
    }
  },
  methods: {
    validateForwardApp(rule, value, callback) {
      // 校验新密码和原始密码是否相等
      if (value && value.indexOf('$APPID$') === -1) {
        callback('转发消息与事件URL需包含$APPID$');
      } else {
        callback();
      }
    },
  },
  created() {
    // 防止表单未注册
    fields.forEach(v => this.form.getFieldDecorator(v, {}))
    this.form.getFieldDecorator('is_default', {})
    this.form.getFieldDecorator('return_forward_platform', {})
    this.form.getFieldDecorator('return_forward_app', {})
    // 当 model 发生改变时，为表单设置值
    this.$watch('model', () => {
      if (this.model) {
        this.form.setFieldsValue(pick(this.model, fields))
        this.form.setFieldsValue({is_default: this.model.is_default === 1})
        this.form.setFieldsValue({
          is_default: this.model.is_default === 1,
          return_forward_platform: this.model.return_forward_platform === 1,
          return_forward_app: this.model.return_forward_app === 1
        })
      }
    })
  }
}
</script>
