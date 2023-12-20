<template>
  <div v-if="currentUser && currentUser.name">
    <a-dropdown placement="bottomRight">
      <span class="ant-pro-account-avatar">
        <a-avatar class="antd-pro-global-header-index-avatar" size="small"
                  src="https://gw.alipayobjects.com/zos/antfincdn/XAosXuNZyF/BiazfanxmamNRoxxVxka.png"/>
        <span>{{ currentUser.name }}</span>
      </span>
      <template v-slot:overlay>
        <a-menu :selected-keys="[]" class="ant-pro-drop-down menu">
          <a-menu-item v-if="menu" key="settings" @click="() => modalVisible = true">
            <a-icon type="setting"/>
            修改密码
          </a-menu-item>
          <a-menu-divider v-if="menu"/>
          <a-menu-item key="logout" @click="handleLogout">
            <a-icon type="logout"/>
            {{ $t('menu.account.logout') }}
          </a-menu-item>
        </a-menu>
      </template>
    </a-dropdown>
    <a-modal
      :confirm-loading="modalLoading"
      :visible="modalVisible"
      title="修改密码"
      @cancel="() => modalVisible = false"
      @ok="handleChangePassword"
    >
      <a-form :form="form" v-bind="formLayout">
        <a-form-item label="原始密码">
          <a-input-password
            v-decorator="['original_password', {rules: [{required: true, message: '请输入原始密码'}]}]"
            placeholder="请输入原始密码"/>
        </a-form-item>
        <a-form-item label="新密码">
          <a-input-password
            v-decorator="['new_password', {
              rules: [
                { required: true, message: '请输入确认新密码' },
                { validator: validateNewPassword, message: '新旧密码不能相同' }
              ]
            }]"
            placeholder="请输入新密码"/>
        </a-form-item>
        <a-form-item label="确认新密码">
          <a-input-password
            v-decorator="['new_password_confirm', {
              rules: [
                { required: true, message: '请输入确认新密码' },
                { validator: validateConfirmPassword, message: '两次输入的密码不一致' }
              ]
            }]"
            placeholder="请再次输入新密码"/>
        </a-form-item>
      </a-form>
    </a-modal>
  </div>
  <span v-else>
    <a-spin :style="{ marginLeft: 8, marginRight: 8 }" size="small"/>
  </span>
</template>

<script>
import md5 from 'md5'
import {Modal} from 'ant-design-vue'
import {changePassword} from "@/api/login";

export default {
  name: 'AvatarDropdown',
  data() {
    return {
      modalLoading: false,
      modalVisible: false,
      passwordVisible: false,
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
  props: {
    currentUser: {
      type: Object,
      default: () => null
    },
    menu: {
      type: Boolean,
      default: true
    }
  },
  methods: {
    handleChangePassword() {
      this.modalLoading = true
      this.form.validateFields((errors, values) => {
        if (!errors) {
          Object.keys(values).forEach(key => {
            values[key] = md5(values[key])
          })
          changePassword(values).then(res => {
            console.log(res)
            this.modalVisible = false
            this.$message.success('修改成功，请重新登录', 3, () => {
              this.$store.dispatch('Logout').then(() => {
                this.$router.push({name: 'login'})
              })
            })
          }).finally(() => {
            this.modalLoading = false
          })
        }
      })
    },
    validateNewPassword(rule, value, callback) {
      const {form} = this;
      // 校验新密码和原始密码是否相等
      if (value && value == form.getFieldValue('original_password')) {
        callback('新旧密码不能相同');
      } else {
        callback();
      }
    },
    validateConfirmPassword(rule, value, callback) {
      const {form} = this;
      // 校验新密码和确认新密码是否相等
      if (value && value !== form.getFieldValue('new_password')) {
        callback('两次输入的密码不一致');
      } else {
        callback();
      }
    },
    handleLogout (e) {
      Modal.confirm({
        title: this.$t('layouts.usermenu.dialog.title'),
        content: this.$t('layouts.usermenu.dialog.content'),
        onOk: () => {
          // return new Promise((resolve, reject) => {
          //   setTimeout(Math.random() > 0.5 ? resolve : reject, 1500)
          // }).catch(() => console.log('Oops errors!'))
          return this.$store.dispatch('Logout').then(() => {
            this.$router.push({ name: 'login' })
          })
        },
        onCancel() {
        }
      })
    }
  }
}
</script>

<style lang="less" scoped>
.ant-pro-drop-down {
  :deep(.action) {
    margin-right: 8px;
  }

  :deep(.ant-dropdown-menu-item) {
    min-width: 160px;
  }
}
</style>
