<template>
  <a-dropdown>
    <a-menu slot="overlay">
      <a-menu-item v-for="(item, key) in platform" :key="key">{{ item.name }}</a-menu-item>
    </a-menu>
    <a-button style="margin-left: 8px"> 筋斗云小程序
      <a-icon type="appstore"/>
    </a-button>
  </a-dropdown>
</template>

<script>
import {mapState} from 'vuex'

export default {
  name: 'AppSwitch',
  computed: {
    ...mapState({
      platform: state => state.platform.info
    })
  }
}
</script>