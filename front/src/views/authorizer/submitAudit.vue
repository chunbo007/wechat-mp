<template>
  <page-header-wrapper :title="false">
    <a-form :form="form" v-bind="formLayout">
      <a-card :bordered="false">
        <div class="tips">
          <p class="text">提交审核前端须知</p>
          <p>- 提交审核前小程序需完成名称、头像、简介以及类目设置</p>
          <p>- 如该小程序中使用了涉及用户隐私接口，例如获取用户头像、手机号等，需先完成"用户隐私保护指引"</p>
          <p>- 如该小程序已经绑定为第三方平台开发小程序，需前往第三方平台-代开发小程序进行解除绑定</p>
          <p>- 提交的小程序功能完整，可正常打开和运行，而不是测试版或 Demo，多次提交测试内容或 Demo，将受到相应处罚</p>
          <p style="margin: 0px;">- 确保小程序符合<a class="a"
                                                     href="https://developers.weixin.qq.com/miniprogram/product/"
                                                     target="_blank">《微信小程序平台运营规范》</a>和确保已经提前了解<a
            class="a" href="https://developers.weixin.qq.com/miniprogram/product/reject.html" target="_blank">《微信小程序平台审核常见被拒绝情形》</a>
          </p>
        </div>
      </a-card>
      <a-card :bordered="false" title="配置审核列表">
        <a-form-item label="小程序类目">
          <a-select
            v-decorator="['template_id', {rules: [{required: true, message: '请选择小程序类目'}]}]"
            mode="multiple"
            placeholder="请选择小程序类目"
            style="width: 100%"
          >
            <a-select-option v-for="(item, index) in categoryList" :key="index">
              {{
                item['first_class'] + ' - ' + item['second_class'] + (item['third_class'] ? ' - ' + item['third_class'] : '')
              }}
            </a-select-option>
          </a-select>
        </a-form-item>
      </a-card>
      <!--      <a-card :bordered="false" title="配置预览信息">
              <a-form-item label="视频预览(video_id_list)">
                <a-input v-decorator="['name1', {rules: [{required: false, message: '请输入名称'}]}]"/>
              </a-form-item>
              <a-form-item label="图片预览(pic_id_list)">
                <a-input v-decorator="['name1', {rules: [{required: false, message: '请输入名称'}]}]"/>
              </a-form-item>
            </a-card>-->
      <a-card :bordered="false" title="信息安全声明">
        <a-form-item label="UGC场景(scene)">
          <a-select
            v-decorator="['scene', {rules: [{required: false, message: '请输入名称'}]}]"
            mode="multiple"
            placeholder="请选择"
            style="width: 100%"
          >
            <a-select-option v-for="(item, index) in enumData.ugc_scene" :key="index">
              {{ item }}
            </a-select-option>
          </a-select>
        </a-form-item>
        <a-form-item label="场景说明(other_scene_desc)">
          <a-input v-decorator="['other_scene_desc', {rules: [{required: false, message: '请输入名称'}]}]"/>
        </a-form-item>
        <a-form-item label="内容安全机制(method)">
          <a-select
            v-decorator="['method', {rules: [{required: false, message: '请输入名称'}]}]"
            mode="multiple"
            placeholder="请选择"
            style="width: 100%"
          >
            <a-select-option v-for="(item, index) in enumData.ugc_method" :key="index">
              {{ item }}
            </a-select-option>
          </a-select>
        </a-form-item>
        <a-form-item label="是否有审核团队(has_audit_team)">
          <a-select
            v-decorator="['has_audit_team', {rules: [{required: false, message: '请输入名称'}]}]"
            placeholder="请选择"
            style="width: 100%"
          >
            <a-select-option v-for="(item, index) in enumData.true_or_false" :key="index">
              {{ item }}
            </a-select-option>
          </a-select>
        </a-form-item>
        <a-form-item label="审核机制描述(audit_desc)">
          <a-input v-decorator="['audit_desc', {rules: [{required: false, message: '请输入名称'}]}]"/>
        </a-form-item>
      </a-card>
      <a-card :bordered="false" title="其他配置信息">
        <a-form-item label="版本描述(version_desc)">
          <a-input v-decorator="['version_desc', {rules: [{required: false, message: '请输入名称'}]}]"/>
        </a-form-item>
        <a-form-item label="反馈内容(feedback_info)">
          <a-input
            v-decorator="['feedback_info', {rules: [{required: false, max: 200, message: '最长不能超过200个字符'}]}]"/>
        </a-form-item>
        <!--        <a-form-item label="反馈截图(feedback_stuff)">-->
        <!--          <a-input v-decorator="['feedback_stuff', {rules: [{required: false, message: '请输入名称'}]}]"/>-->
        <!--        </a-form-item>-->
        <a-form-item label="订单中心path(order_path)">
          <a-input v-decorator="['order_path', {rules: [{required: false, message: '请输入名称'}]}]"/>
        </a-form-item>
        <a-form-item label="是否不使用“代码中检测出但是未配置的隐私相关接口”">
          <a-select
            v-decorator="['privacy_api_not_use', {rules: [{required: false, message: '请输入名称'}]}]"
            placeholder="请选择"
            style="width: 100%"
          >
            <a-select-option v-for="(item, index) in enumData.true_or_false" :key="index">
              {{ item }}
            </a-select-option>
          </a-select>
        </a-form-item>
        <a-form-item :wrapper-col="{ span: 12, offset: 10 }">
          <a-button type="primary" @click="submit">
            提交
          </a-button>
        </a-form-item>
      </a-card>
    </a-form>
  </page-header-wrapper>
</template>

<script>
import {getCategory, submitAudit} from '@/api/miniprogram'
import data from "@/config/data";
import Message from "ant-design-vue/lib/message";

export default {
  data() {
    return {
      id: null,
      enumData: data,
      categoryList: [],
      formLayout: {
        labelCol: {
          xs: {span: 7},
          sm: {span: 7},
          lg: {span: 7}
        },
        wrapperCol: {
          xs: {span: 10},
          sm: {span: 10},
          lg: {span: 10}
        }
      },
      form: this.$form.createForm(this)
    }
  },
  methods: {
    getCategoryList() {
      getCategory({id: this.id}).then(res => {
        this.categoryList = res.data['category_list']
      })
    },
    submit() {
      this.form.validateFields((errors, values) => {
        if (!errors) {
          let data = {
            id: this.id,
            item_list: values['template_id'].map(item => this.categoryList[item]),
            ugc_declare: {
              scene: values['scene'] && values['scene'].map(str => parseInt(str)),
              method: values['method'] && values['method'].map(str => parseInt(str)),
              other_scene_desc: values['other_scene_desc'],
              has_audit_team: values['has_audit_team'] && parseInt(values['has_audit_team']),
              audit_desc: values['audit_desc'],
            },
            version_desc: values['version_desc'],
            feedback_info: values['feedback_info'],
            privacy_api_not_use: values['privacy_api_not_use'] && values['privacy_api_not_use'] === '1',
            order_path: values['order_path'],
          }
          if (data.ugc_declare.scene.includes(0)) {
            data.ugc_declare.method = data.ugc_declare.other_scene_desc = data.ugc_declare.has_audit_team = data.ugc_declare.audit_desc = undefined
            data.ugc_declare.scene = [0]
          }
          submitAudit(data).then(res => {
            Message.success('提交成功')
            this.$router.push({
              path: '/authorizer/detail',
              query: {
                id: this.id
              }
            })
          })
        }
      })
    }
  },
  created() {
    const {id} = this.$route.query
    this.id = id
    this.getCategoryList()
  }
}
</script>

<style lang="less" scoped>
.tips {
  padding: 20px;
  background: #e6f7ff;
}

.text {
  font-size: 18px;
}
</style>