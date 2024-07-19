<template>
  <page-header-wrapper :title="false">
    <a-card :bordered="false">
      <a-tabs default-active-key="1">
        <a-tab-pane key="1" tab="版本管理">
          <version/>
        </a-tab-pane>
        <a-tab-pane key="2" force-render tab="信息配置">
          <setting/>
        </a-tab-pane>
        <a-tab-pane key="3" tab="体验者">
          <tests/>
        </a-tab-pane>
        <a-tab-pane key="4" tab="隐私协议">
          <privacy/>
        </a-tab-pane>
      </a-tabs>
    </a-card>
  </page-header-wrapper>
</template>

<script>
import version from '@/views/authorizer/modules/version'
import setting from '@/views/authorizer/modules/setting'
import tests from '@/views/authorizer/modules/tests'
import Privacy from "@/views/authorizer/modules/privacy.vue";

export default {
  name: 'Version',
  components: {
    Privacy,
    version,
    setting,
    tests
  },
  data() {
    return {
    }
  },
  methods: {
  },
  computed: {
  },
  created() {
    const {id} = this.$route.query
    this.id = id
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