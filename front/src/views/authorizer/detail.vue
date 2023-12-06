<template>
  <page-header-wrapper :title="false">
    <a-card :bordered="false" title="线上版本">
      <a-row>
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
    </a-card>
    <a-card :bordered="false" title="审核版本">
      <a-dropdown slot="extra">
        <a-menu slot="overlay">
          <a-menu-item v-for="(item) in audit_option" :key="item.value">{{ item.content }}</a-menu-item>
        </a-menu>
        <a-button type="primary"> 操作
          <a-icon type="down"/>
        </a-button>
      </a-dropdown>
      <span v-if="audit_info.errcode === 85058"> 暂无提交审核的版本或者版本已发布上线 </span>
      <a-descriptions v-else-if="audit_info.errcode === 0">
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
    <a-card :bordered="false" title="体验版本">
      <a-col span="8">
        <a-descriptions>
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
    </a-card>
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
      release_info: {},
      exp_info: {},
      audit_info: {},
    }
  },
  methods: {
    getDetail(id) {
      getDetail({id}).then(res => {
        this.release_info = res.data.version.release_info
        this.exp_info = res.data.version.exp_info
        this.audit_info = res.data.version.audit_info
      })

    }
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

</style>