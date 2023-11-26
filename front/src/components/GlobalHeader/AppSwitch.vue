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
      const defaultPlatform = this.platform.find(item => item['is_default'] === 1)
      this.SetPlatform(defaultPlatform).then(() => {
        this.current_name = this.current.name
      })
    } else {
      this.current_name = this.current.name
    }
  }
}
</script>