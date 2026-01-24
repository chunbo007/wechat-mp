<template>
  <div class="install-container">
    <div class="install-card">
      <div class="install-header">
        <img src="/logo.png" alt="Logo" class="logo" />
        <h1>微信开放平台管理工具</h1>
        <p class="subtitle">安装向导</p>
      </div>

      <a-steps :current="currentStep" class="steps">
        <a-step title="环境检测" />
        <a-step title="数据库配置" />
        <a-step title="管理员设置" />
        <a-step title="系统配置" />
        <a-step title="安装执行" />
      </a-steps>

      <div class="step-content">
        <a-spin :spinning="loading" tip="处理中...">
          <div v-if="currentStep === 0" class="step-0">
            <a-alert
              v-if="envCheck.pass"
              message="环境检测通过"
              description="您的服务器环境满足系统要求，可以进行下一步。"
              type="success"
              show-icon
              class="mb-20"
            />
            <a-alert
              v-else
              message="环境检测未通过"
              description="请修复以下问题后再继续安装。"
              type="error"
              show-icon
              class="mb-20"
            />

            <a-table
              :columns="envColumns"
              :data-source="envCheck.requirements || []"
              :pagination="false"
              size="middle"
              bordered
              rowKey="name"
            >
              <template #status="text">
                <a-tag v-if="text" color="green">通过</a-tag>
                <a-tag v-else color="red">未通过</a-tag>
              </template>
            </a-table>

            <div class="mt-20">
              <h4>Nginx路由测试</h4>
              <a-alert
                message="测试Nginx配置"
                description="点击测试按钮，访问各模块路由，验证Nginx配置是否正确。"
                type="info"
                show-icon
                class="mb-20"
              />

              <a-table
                :columns="nginxColumns"
                :data-source="nginxDataSource"
                :pagination="false"
                size="middle"
                bordered
                rowKey="key"
              >
                <template #status="text">
                  <a-tag v-if="text.loading" color="blue">
                    <a-icon type="loading" />
                    测试中
                  </a-tag>
                  <a-tag v-else-if="text.tested && text.success" color="green">已配置</a-tag>
                  <a-tag v-else-if="text.tested && !text.success" color="red">未配置</a-tag>
                  <a-tag v-else color="default">未测试</a-tag>
                </template>
                <template #action="record">
                  <a-button
                    size="small"
                    :loading="record.loading"
                    @click="testNginxRoute(record.key)"
                  >
                    测试
                  </a-button>
                </template>
              </a-table>

              <div class="mt-20">
                <h4>必需的Nginx配置</h4>
                <pre class="nginx-config">location /admin {
  proxy_pass http://127.0.0.1:8789/admin;
  proxy_set_header   X-Forwarded-Proto $scheme;
  proxy_set_header   X-Real-IP         $remote_addr;
}

location /wechat {
  proxy_pass http://127.0.0.1:8789/wechat;
  proxy_set_header   X-Forwarded-Proto $scheme;
  proxy_set_header   X-Real-IP         $remote_addr;
}

location /openapi {
  proxy_pass http://127.0.0.1:8789/openapi;
  proxy_set_header   X-Forwarded-Proto $scheme;
  proxy_set_header   X-Real-IP         $remote_addr;
}

location / {
  try_files $uri $uri/ /index.html;
}</pre>
              </div>
            </div>
          </div>

          <div v-if="currentStep === 1" class="step-1">
            <a-form :label-col="{ span: 5 }" :wrapper-col="{ span: 18 }">
              <a-form-item label="数据库主机">
                <a-input v-model="dbForm.host" placeholder="127.0.0.1" />
              </a-form-item>

              <a-form-item label="数据库端口">
                <a-input-number v-model="dbForm.port" :min="1" :max="65535" style="width: 100%" />
              </a-form-item>

              <a-form-item label="数据库名">
                <a-input v-model="dbForm.database" placeholder="wechat_mp" />
              </a-form-item>

              <a-form-item label="数据库用户">
                <a-input v-model="dbForm.username" placeholder="wechat_mp" />
              </a-form-item>

              <a-form-item label="数据库密码">
                <a-input-password v-model="dbForm.password" placeholder="请输入数据库密码" />
              </a-form-item>

              <a-form-item :wrapper-col="{ span: 18, offset: 5 }">
                <a-button type="primary" @click="testDbConnection" :loading="testLoading">
                  测试连接
                </a-button>
              </a-form-item>
            </a-form>
          </div>

          <div v-if="currentStep === 2" class="step-2">
            <a-form :label-col="{ span: 5 }" :wrapper-col="{ span: 18 }">
              <a-form-item label="管理员用户名">
                <a-input v-model="adminForm.username" placeholder="admin" />
                <div class="form-tip">用户名长度为3-20个字符</div>
              </a-form-item>

              <a-form-item label="管理员密码">
                <a-input-password v-model="adminForm.password" placeholder="请输入密码" />
                <div class="form-tip">密码长度不能少于6位</div>
              </a-form-item>

              <a-form-item label="确认密码">
                <a-input-password v-model="adminForm.password_confirm" placeholder="请再次输入密码" />
              </a-form-item>
            </a-form>
          </div>

          <div v-if="currentStep === 3" class="step-3">
            <a-form :label-col="{ span: 5 }" :wrapper-col="{ span: 18 }">
              <a-form-item label="网站域名">
                <a-input v-model="systemForm.site_url" placeholder="https://www.example.com" />
                <div class="form-tip">请填写网站的完整域名地址，包括 http:// 或 https://</div>
              </a-form-item>
            </a-form>
          </div>

          <div v-if="currentStep === 4" class="step-4">
            <a-alert
              message="准备安装"
              description="确认以下信息无误后，点击开始安装按钮。"
              type="info"
              show-icon
              class="mb-20"
            />

            <a-descriptions bordered :column="1">
              <a-descriptions-item label="数据库主机">{{ dbForm.host }}:{{ dbForm.port }}</a-descriptions-item>
              <a-descriptions-item label="数据库名">{{ dbForm.database }}</a-descriptions-item>
              <a-descriptions-item label="数据库用户">{{ dbForm.username }}</a-descriptions-item>
              <a-descriptions-item label="管理员用户名">{{ adminForm.username }}</a-descriptions-item>
              <a-descriptions-item label="网站域名">{{ systemForm.site_url }}</a-descriptions-item>
            </a-descriptions>

            <a-alert
              v-if="installSuccess"
              message="安装成功!"
              description="系统已成功安装，点击下方按钮跳转到登录页面。"
              type="success"
              show-icon
              class="mt-20"
            />
          </div>
        </a-spin>
      </div>

      <div class="step-footer">
        <a-button v-if="currentStep > 0" @click="prevStep" :disabled="loading">
          上一步
        </a-button>
        <a-button
          v-if="currentStep < 4"
          type="primary"
          @click="nextStep"
          :loading="loading"
          :disabled="currentStep === 0 && !envCheck.pass"
        >
          下一步
        </a-button>
        <a-button
          v-if="currentStep === 4 && !installSuccess"
          type="primary"
          @click="doInstall"
          :loading="loading"
        >
          开始安装
        </a-button>
        <a-button
          v-if="installSuccess"
          type="primary"
          @click="goToLogin"
        >
          进入系统
        </a-button>
      </div>
    </div>
  </div>
</template>

<script>
import Message from "ant-design-vue/lib/message"
import { Tooltip, Icon } from 'ant-design-vue'
import { checkEnv, testDb, install, testNginxAdmin, testNginxWechat, testNginxOpenapi } from '@/api/install'

export default {
  name: 'InstallWizard',
  components: {
    'a-tooltip': Tooltip,
    'a-icon': Icon
  },
  data() {
    return {
      currentStep: 0,
      loading: false,
      testLoading: false,
      installSuccess: false,
      envCheck: {
        pass: false,
        requirements: []
      },
      nginxCheck: {
        admin: { tested: false, success: false, message: '未测试' },
        wechat: { tested: false, success: false, message: '未测试' },
        openapi: { tested: false, success: false, message: '未测试' }
      },
      nginxDataSource: [
        { key: 'admin', name: '后台管理(/admin)', tested: false, success: false, message: '未测试', loading: false },
        { key: 'wechat', name: '微信回调(/wechat)', tested: false, success: false, message: '未测试', loading: false },
        { key: 'openapi', name: '开放API(/openapi)', tested: false, success: false, message: '未测试', loading: false }
      ],
      dbForm: {
        host: '127.0.0.1',
        port: 3306,
        database: 'wechat_mp',
        username: 'wechat_mp',
        password: ''
      },
      dbRules: {
        host: [{ required: true, message: '请输入数据库主机' }],
        port: [{ required: true, message: '请输入数据库端口' }],
        database: [{ required: true, message: '请输入数据库名' }],
        username: [{ required: true, message: '请输入数据库用户' }],
        password: [{ required: true, message: '请输入数据库密码' }]
      },
      adminForm: {
        username: 'admin',
        password: '',
        password_confirm: ''
      },
      adminRules: {
        username: [
          { required: true, message: '请输入管理员用户名' },
          { min: 3, max: 20, message: '用户名长度为3-20个字符' }
        ],
        password: [
          { required: true, message: '请输入密码' },
          { min: 6, message: '密码长度不能少于6位' }
        ],
        password_confirm: [
          { required: true, message: '请确认密码' },
          { validator: (rule, value, callback) => {
            if (value !== this.adminForm.password) {
              callback(new Error('两次输入的密码不一致'))
            } else {
              callback()
            }
          } }
        ]
      },
      systemForm: {
        site_url: ''
      },
      systemRules: {
        site_url: [
          { required: true, message: '请输入网站域名' },
          { type: 'url', message: '请输入有效的URL地址' }
        ]
      },
      envColumns: [
        { title: '检测项', dataIndex: 'name', key: 'name' },
        { title: '要求', dataIndex: 'required', key: 'required' },
        { title: '当前', dataIndex: 'current', key: 'current' },
        { title: '状态', key: 'status', scopedSlots: { customRender: 'status' } }
      ],
      nginxColumns: [
        { title: '路由', dataIndex: 'name', key: 'name' },
        { title: '状态', key: 'status', scopedSlots: { customRender: 'status' }, width: 200 },
        { title: '操作', key: 'action', scopedSlots: { customRender: 'action' }, width: 200 }
      ]
    }
  },
  mounted() {
      this.checkEnvironment()
      this.testAllNginxRoutes()
    },
    methods: {
    async checkEnvironment() {
      this.loading = true
      try {
        const res = await checkEnv()
        if (res.code === 0) {
          this.envCheck = res.data
        } else {
          Message.error(res.msg || '环境检测失败')
        }
      } catch (error) {
        Message.error('环境检测失败: ' + error.message)
      } finally {
        this.loading = false
      }
    },
    async testAllNginxRoutes() {
      const routes = ['admin', 'wechat', 'openapi']
      for (const route of routes) {
        await this.testNginxRoute(route)
      }
    },
    async testNginxRoute(route) {
      const dataSourceItem = this.nginxDataSource.find(item => item.key === route)
      if (dataSourceItem) {
        dataSourceItem.loading = true
      }
      try {
        let res
        if (route === 'admin') {
          res = await testNginxAdmin()
        } else if (route === 'wechat') {
          res = await testNginxWechat()
        } else if (route === 'openapi') {
          res = await testNginxOpenapi()
        }
console.log(res)
        this.nginxCheck[route] = {
          tested: true,
          success: res.success,
          message: res.message
        }
        if (dataSourceItem) {
          dataSourceItem.tested = true
          dataSourceItem.success = res.success
          dataSourceItem.message = res.message
        }
      } catch (error) {
        const errorMessage = '测试失败: ' + error.message
        this.nginxCheck[route] = {
          tested: true,
          success: false,
          message: errorMessage
        }
        if (dataSourceItem) {
          dataSourceItem.tested = true
          dataSourceItem.success = false
          dataSourceItem.message = errorMessage
        }
      } finally {
        if (dataSourceItem) {
          dataSourceItem.loading = false
        }
      }
    },
    async testDbConnection() {
      if (!this.dbForm.host || !this.dbForm.port || !this.dbForm.database || !this.dbForm.username || !this.dbForm.password) {
        Message.error('请填写完整的数据库配置')
        return
      }
      this.testLoading = true
      try {
        const res = await testDb(this.dbForm)
        console.log(res)
        if (res.code === 0) {
          Message.success(res.msg)
        } else {
          Message.error(res.msg)
        }
      } finally {
        this.testLoading = false
      }
    },
    prevStep() {
      if (this.currentStep > 0) {
        this.currentStep--
      }
    },
    async nextStep() {
      const step = this.currentStep + 1
      this.loading = true

      try {
        if (step === 1) {
          await this.checkEnvironment()
          if (!this.envCheck.pass) {
            Message.warning('请先修复环境问题')
            this.loading = false
            return
          }
          const allTested = this.nginxDataSource.every(item => item.tested)
          if (!allTested) {
            Message.warning('请先测试所有Nginx路由')
            this.loading = false
            return
          }
          const allSuccess = this.nginxDataSource.every(item => item.success)
          if (!allSuccess) {
            Message.warning('请确保所有Nginx路由配置正确')
            this.loading = false
            return
          }
        } else if (step === 2) {
          await this.saveDbConfig()
        } else if (step === 3) {
          await this.saveAdminConfig()
        } else if (step === 4) {
          await this.saveSystemConfig()
        }
        this.currentStep = step
      } catch (error) {
        console.error(error)
      } finally {
        this.loading = false
      }
    },
    async saveDbConfig() {
      if (!this.dbForm.host || !this.dbForm.port || !this.dbForm.database || !this.dbForm.username || !this.dbForm.password) {
        Message.error('请填写完整的数据库配置')
        throw new Error('请填写完整的数据库配置')
      }
      const res = await install({
        step: 2,
        ...this.dbForm
      })
      if (res.code === 0) {
        Message.success(res.msg)
      } else {
        Message.error(res.msg)
        throw new Error(res.msg)
      }
    },
    async saveAdminConfig() {
      if (!this.adminForm.username || !this.adminForm.password || !this.adminForm.password_confirm) {
        Message.error('请填写完整的管理员信息')
        throw new Error('请填写完整的管理员信息')
      }
      if (this.adminForm.password !== this.adminForm.password_confirm) {
        Message.error('两次输入的密码不一致')
        throw new Error('两次输入的密码不一致')
      }
      if (this.adminForm.username.length < 3 || this.adminForm.username.length > 20) {
        Message.error('用户名长度为3-20个字符')
        throw new Error('用户名长度为3-20个字符')
      }
      if (this.adminForm.password.length < 6) {
        Message.error('密码长度不能少于6位')
        throw new Error('密码长度不能少于6位')
      }
      try {
        const res = await install({
          step: 3,
          ...this.adminForm
        })
        if (res.code === 0) {
          Message.success(res.msg)
        } else {
          Message.error(res.msg)
          throw new Error(res.msg)
        }
      } catch (error) {
        Message.error('管理员信息保存失败: ' + error.message)
        throw error
      }
    },
    async saveSystemConfig() {
      if (!this.systemForm.site_url) {
        Message.error('请填写网站域名')
        throw new Error('请填写网站域名')
      }
      try {
        const res = await install({
          step: 4,
          ...this.systemForm
        })
        if (res.code === 0) {
          Message.success(res.msg)
        } else {
          Message.error(res.msg)
          throw new Error(res.msg)
        }
      } catch (error) {
        Message.error('系统配置保存失败: ' + error.message)
        throw error
      }
    },
    async doInstall() {
      this.loading = true
      try {
        const res = await install({ step: 5 })
        if (res.code === 0) {
          Message.success(res.msg)
          this.installSuccess = true
        } else {
          Message.error(res.msg)
        }
      } catch (error) {
        Message.error('安装失败: ' + error.message)
      } finally {
        this.loading = false
      }
    },
    goToLogin() {
      window.location.href = '/user/login'
    }
  }
}
</script>

<style scoped>
.install-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.install-card {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  width: 100%;
  max-width: 900px;
  padding: 40px;
}

.install-header {
  text-align: center;
  margin-bottom: 40px;
}

.logo {
  width: 80px;
  height: 80px;
  margin-bottom: 20px;
}

.install-header h1 {
  font-size: 28px;
  font-weight: 600;
  margin-bottom: 8px;
  color: #303133;
}

.subtitle {
  color: #909399;
  font-size: 16px;
}

.steps {
  margin-bottom: 40px;
}

.step-content {
  min-height: 300px;
  margin-bottom: 40px;
}

.form-tip {
  font-size: 12px;
  color: #909399;
  margin-top: 4px;
}

.step-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.mb-20 {
  margin-bottom: 20px;
}

.mt-20 {
  margin-top: 20px;
}

.nginx-config {
  background: #f5f5f5;
  padding: 15px;
  border-radius: 4px;
  font-size: 13px;
  line-height: 1.6;
  color: #333;
}
</style>
