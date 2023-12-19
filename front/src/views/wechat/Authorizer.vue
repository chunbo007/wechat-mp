<template>
  <div>
    <div style="margin: 10px">
      <a :href="url">点击授权</a>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Authorize',
  data() {
    return {
      url: null,
      isMobile: false
    };
  },
  created() {
    let url = this.$route.query.url || ''
    this.url = decodeURIComponent(url)
    this.checkIsMobile();
    window.addEventListener('resize', this.checkIsMobile);
  },
  methods: {
    checkIsMobile() {
      this.isMobile = window.innerWidth <= 600; // 根据需要调整阈值
      if (this.isMobile) {
        this.addViewportMetaTag();
      } else {
        this.removeViewportMetaTag();
      }
    },
    addViewportMetaTag() {
      const viewportMetaTag = document.createElement('meta');
      viewportMetaTag.name = 'viewport';
      viewportMetaTag.content = 'width=device-width, initial-scale=1.0';
      document.head.appendChild(viewportMetaTag);
    },
    removeViewportMetaTag() {
      const viewportMetaTag = document.querySelector('meta[name="viewport"]');
      if (viewportMetaTag) {
        document.head.removeChild(viewportMetaTag);
      }
    }
  }
}
</script>

<style scoped>
a:-webkit-any-link {
  color: -webkit-link;
  cursor: pointer;
  font-size: 16px;
  text-decoration: underline;
}
</style>