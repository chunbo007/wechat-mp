<template>
  <page-header-wrapper :title="false">
    <a-card :bordered="false" title="线上版本">
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
    </a-card>
    <a-card :bordered="false" title="审核版本">
      hello
    </a-card>
    <a-card :bordered="false" title="体验版本">
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
    </a-card>
  </page-header-wrapper>
</template>

<script>
import {getDetail} from '@/api/miniprogram'

export default {
  name: 'Version',
  data() {
    return {
      release_info: {},
      exp_info: {},
    }
  },
  methods: {
    getDetail(id) {
      getDetail({id}).then(res => {
        this.release_info = res.data.version.release_info
        this.exp_info = res.data.version.exp_info
      })

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