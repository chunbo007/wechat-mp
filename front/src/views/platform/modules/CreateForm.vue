<template>
  <a-modal
    :confirmLoading="loading"
    :visible="visible"
    :width="640"
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
const fields = ['id', 'name', 'app_id', 'secret', 'token', 'aes_key', 'is_default']

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
      form: this.$form.createForm(this)
    }
  },
  created() {
    // 防止表单未注册
    fields.forEach(v => this.form.getFieldDecorator(v, {}))
    this.form.getFieldDecorator('is_default', {})
    // 当 model 发生改变时，为表单设置值
    this.$watch('model', () => {
      if (this.model) {
        this.form.setFieldsValue(pick(this.model, fields))
        this.form.setFieldsValue({is_default: this.model.is_default === 1})
      }
    })
  }
}
</script>
