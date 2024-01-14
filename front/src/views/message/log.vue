<template>
  <page-header-wrapper :title="false">
    <a-card :bordered="false">
      <a-tabs default-active-key="1">
        <a-tab-pane key="1" tab="授权事件">
          <authorizer/>
        </a-tab-pane>
        <a-tab-pane key="2" force-render tab="普通消息与事件">
          <event/>
        </a-tab-pane>
        <a-tab-pane key="3" force-render tab="转发日志">
          <forward/>
        </a-tab-pane>
      </a-tabs>
    </a-card>
  </page-header-wrapper>
</template>

<script>
import {Ellipsis, STable} from '@/components'
import moment from 'moment'
import event from '@/views/message/modules/event'
import authorizer from "@/views/message/modules/authorizer";
import forward from "@/views/message/modules/forward";

export default {
  name: 'log',
  components: {
    STable,
    Ellipsis,
    event,
    authorizer,
    forward
  },
  data() {
    return {}
  },
  created() {
  },
  computed: {},
  methods: {
    moment,
  }
}
</script>
<style lang="less" scoped>
</style>
