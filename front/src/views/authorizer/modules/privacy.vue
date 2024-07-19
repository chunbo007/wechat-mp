<template>
    <div>
      <a-form :form="form" v-bind="formLayout">
        <a-card :bordered="false">
          <div class="tips">
            <p class="text">提示</p>
            <p>- 参考步骤执行：提交代码--->稍等(若小程序是首次提交代码后台需要些时间检测)--->get接口看下开发版本需要补什么信息(privacy_list中就是你用到的权限)---->set隐私(在下面表单中完善信息并提交，ctrl+F搜：把我换成具体用途，填写自己的用途，不用加"为了"这两个字)---->提交审核---->提交发布</p>
            <p>- 参考文档：<a target="_blank" href="https://developers.weixin.qq.com/doc/oplatform/openApi/OpenApiDoc/miniprogram-management/privacy-management/getPrivacySetting.html">《获取小程序用户隐私保护指引》</a>、
              <a target="_blank" href="https://developers.weixin.qq.com/doc/oplatform/openApi/OpenApiDoc/miniprogram-management/privacy-management/getPrivacySetting.html">《设置小程序用户隐私保护指引》</a>
            </p>
            <p>- 参考权限说明：
              UserInfo:用户信息（微信昵称、头像）
              Location:位置信息
              Address:地址
              Invoice:发票信息
              RunData:微信运动数据
              Record:麦克风
              Album:选中的照片或视频信息
              Camera:摄像头
              PhoneNumber:手机号码
              Contact:通讯录（仅写入）权限
              DeviceInfo:设备信息
              EXIDNumber:身份证号码
              EXOrderInfo:订单信息
              EXUserPublishContent:发布内容
              EXUserFollowAcct:所关注账号
              EXUserOpLog:操作日志
              AlbumWriteOnly:相册（仅写入）权限
              LicensePlate:车牌号
              BlueTooth:蓝牙
              CalendarWriteOnly:日历（仅写入）权限
              Email:邮箱
              MessageFile:选中的文件
              ChooseLocation:选择的位置信息
              Accelerometer:加速传感器
              Compass:磁场传感器
              DeviceMotion:方向传感器
              Gyroscope:陀螺仪传感器
              Clipboard:剪切板
            </p>
            <p>- 其他提示：联系方式四选一，sdk_privacy_info_list未做处理</p>
          </div>
        </a-card>
        <a-card :bordered="false">
          <a-form-item :wrapper-col="{ span: 12, offset: 6 }">
            <a-row>
              <a-col :span="8">
                <a-button type="primary" @click="getProPrivacy">
                  查看现网版本
                </a-button>
              </a-col>
              <a-col :span="8">
                <a-button type="primary" @click="getDevPrivacy">
                  查看开发版本
                </a-button>
              </a-col>
              <a-col :span="8">
                <a-button type="primary" @click="createPrivacy">
                  根据开发版本配置生成隐私配置
                </a-button>
              </a-col>
            </a-row>
          </a-form-item>
        </a-card>
        <a-card :bordered="false" title="配置新的隐私保护指引">
          <a-form-item label="协议内容">
            <a-textarea rows="12" v-decorator="['newPrivacyData', {rules: [{required: false, message: '请输入名称'}]}]"/>
          </a-form-item>
          <a-form-item :wrapper-col="{ span: 12, offset: 10 }">
            <a-button type="primary" @click="submit">
              提交
            </a-button>
          </a-form-item>
        </a-card>
      </a-form>
      <a-modal
        :footer="null"
        :visible="jsonDataVisible"
        title="原始报文"
        width="1000px"
        @cancel="() => jsonDataVisible = false"
      >
        <json-viewer
          :copyable="{copyText: '复制', copiedText: '复制成功'}"
          :expand-depth='3'
          :value="jsonData"
          expanded
        ></json-viewer>
      </a-modal>
    </div>
</template>

<script>
import { getPrivacy, setPrivacy } from '@/api/miniprogram'
import JsonViewer from 'vue-json-viewer'
import data from "@/config/data";
import Message from "ant-design-vue/lib/message";
import {addPlatform} from "@/api/platform";

export default {
  components: {
    JsonViewer
  },
  data() {
    return {
      id: null,
      jsonDataVisible: false,
      jsonData: {},
      devPrivacyData: {},
      enumData: data,
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
    getProPrivacy() {
      this.jsonDataVisible = true
      getPrivacy({id: this.id, privacy_ver: 1}).then(res => {
        this.jsonData = res.data
      })
    },
    getDevPrivacy() {
      this.jsonDataVisible = true
      getPrivacy({id: this.id, privacy_ver: 2}).then(res => {
        this.jsonData = res.data
        this.devPrivacyData = res.data
      })
    },
    createPrivacy() {
      if (Object.keys(this.devPrivacyData).length === 0){
        Message.error('请先获取开发版隐私信息')
        return
      }
      let jsonData = {
        "setting_list":this.devPrivacyData.setting_list || [],
        "owner_setting": {
          "contact_phone": this.devPrivacyData.owner_setting.contact_phone,
          "contact_email": this.devPrivacyData.owner_setting.contact_email,
          "contact_qq": this.devPrivacyData.owner_setting.contact_qq,
          "contact_weixin": this.devPrivacyData.owner_setting.contact_weixin,
          "ext_file_media_id": this.devPrivacyData.owner_setting.ext_file_media_id,
          "notice_method": this.devPrivacyData.owner_setting.notice_method || '公告'
        },
        "sdk_privacy_info_list": this.devPrivacyData.sdk_privacy_info_list || []
      }

      // 筛选出 privacy_list 中有的但 setting_list 没有的项
      let missingPrivacyItems = this.devPrivacyData.privacy_list.filter(item => {
        // 判断 item 是否在 setting_list 中已经存在
        return !this.devPrivacyData.setting_list.some(setting => setting.privacy_key === item);
      });
      if (missingPrivacyItems.length > 0){
        missingPrivacyItems.forEach(item => {
          jsonData.setting_list.push({
            "privacy_key": item,
            "privacy_desc": '把我换成具体用途'
          })
        })
      }
      jsonData.setting_list.forEach(item => {
        if (item.hasOwnProperty('privacy_label')) {
          delete item.privacy_label;
        }
      });
      this.form.setFieldsValue({newPrivacyData: JSON.stringify(jsonData)})
    },
    submit() {
      this.form.validateFields((errors, values) => {
        if (!errors) {
          setPrivacy({id:this.id, privacy: values.newPrivacyData}).then(res => {
            this.$message.success(res['msg'])
          })
        }
      })
    }
  },
  created() {
    const {id} = this.$route.query
    this.id = id
    this.form.getFieldDecorator('newPrivacyData', {})
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