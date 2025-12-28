<template>
  <a-dropdown>
    <a-menu slot="overlay" @click="handleMenuClick">
      <a-menu-item v-for="(item, key) in platform" :key="item['app_id']">{{ item.name }}</a-menu-item>
    </a-menu>
    <a-button style="margin-left: 8px"> {{ current_name }}
      <a-icon type="appstore"/>
    </a-button>
  </a-dropdown>
</template>

<script>
import {mapActions, mapState} from 'vuex'

export default {
  name: 'AppSwitch',
  data() {
    return {
      current_name: '切换应用'
    }
  },
  computed: {
    ...mapState({
      platform: state => state.platform.platformList,
      current: state => state.platform.currentPlatform
    })
  },
  methods: {
    ...mapActions(['SetPlatform']),
    handleMenuClick(e) {
      const currentPlatform = this.platform.find(item => item['app_id'] === e.key)
      this.SetPlatform(currentPlatform).then(() => {
        this.current_name = this.current.name
      })
    }
  },
  created() {
    if (Object.keys(this.current).length === 0) {
      this.current_name = '切换应用'
      // 检查 platform 是否为数组且有数据
      if (Array.isArray(this.platform) && this.platform.length > 0) {
        const defaultPlatform = this.platform.find(item => item['is_default'] === 1)
        if (defaultPlatform) {
          this.SetPlatform(defaultPlatform).then(() => {
            this.current_name = this.current.name
          })
        } else {
          // 如果没有默认平台，给出提示
          console.warn('未找到默认开放平台，请先在「开放平台管理」中添加平台并设置为默认')
          this.current_name = '请先添加平台'
        }
      } else {
        // platform 为空或不是数组
        console.warn('未找到开放平台数据，请先在「开放平台管理」中添加平台')
        this.current_name = '请先添加平台'
      }
    } else {
      this.current_name = this.current.name
    }
  }
}
</script>