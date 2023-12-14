<template>
  <div>
    <div class="table-page-search-wrapper">
      <a-form layout="inline">
        <a-row :gutter="48">
          <a-col :md="4" :sm="24">
            <a-form-item label="appid">
              <a-input v-model="queryParam.appid" placeholder=""/>
            </a-form-item>
          </a-col>
          <a-col :md="4" :sm="24">
            <a-form-item label="InfoType">
              <a-input v-model="queryParam.infotype" placeholder=""/>
            </a-form-item>
          </a-col>
          <a-col :md="4" :sm="24">
            <a-form-item label="Event">
              <a-input v-model="queryParam.event" placeholder=""/>
            </a-form-item>
          </a-col>
          <a-col :md="8" :sm="24">
            <a-form-item label="时间">
              <a-range-picker
                v-model="queryParam.receivetime"
                :show-time="{
                        hideDisabledOptions: true, defaultValue: [moment('00:00:00', 'HH:mm:ss'), moment('23:59:59', 'HH:mm:ss')]
                      }"/>
            </a-form-item>
          </a-col>
          <a-col :md="4" :sm="24">
                <span :style=" {} "
                      class="table-page-search-submitButtons">
                      <a-button type="primary" @click="$refs.table.refresh(true)">查询</a-button>
                      <a-button style="margin-left: 8px" @click="() => this.queryParam = {}">重置</a-button>
                </span>
          </a-col>
        </a-row>
      </a-form>
    </div>
    <s-table
      ref="table"
      :columns="columns"
      :data="loadData"
      rowKey="id"
      showPagination="auto"
      size="default"
    >
          <span slot="receivetime" slot-scope="text, record">
            {{ text | momentUnix }}
          </span>
      <span slot="postbody" slot-scope="text, record" v-clipboard:copy="text" class="text_copy" @click="onCopy">
            {{ text }}
          </span>
    </s-table>
  </div>
</template>

<script>
import {Ellipsis, STable} from '@/components'
import {getEventMessage} from '@/api/message'
import Vue from 'vue';
import VueClipboard from 'vue-clipboard2';
import Message from "ant-design-vue/lib/message";
import moment from 'moment'

VueClipboard.config.autoSetContainer = true;
Vue.use(VueClipboard);

const columns = [
  {
    title: '推送时间',
    dataIndex: 'receivetime',
    scopedSlots: {customRender: 'receivetime'},
    width: 180
  },
  {
    title: 'appid',
    dataIndex: 'appid',
    width: 180
  },
  {
    title: 'MsgType',
    dataIndex: 'msgtype',
    width: 220
  },
  {
    title: '推送内容',
    dataIndex: 'postbody',
    scopedSlots: {customRender: 'postbody'},
    width: 600
  }
]

export default {
  name: 'even',
  components: {
    STable,
    Ellipsis,
  },
  data() {
    return {
      columns: columns,
      // 查询参数
      queryParam: {},
      // 加载数据方法 必须为 Promise 对象
      loadData: parameter => {
        const requestParameters = Object.assign({}, parameter, this.queryParam)
        return getEventMessage(requestParameters)
          .then(res => {
            return res.data
          })
      },
    }
  },
  created() {
  },
  computed: {},
  methods: {
    moment,
    onCopy() {
      Message.success('复制成功！')
    }
  }
}
</script>
<style lang="less" scoped>
.text_copy {
  cursor: pointer;
}

.normal_flex {
  display: flex;
  align-items: center;
  flex-wrap: wrap;

  .blue_circle {
    min-width: 10px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #1890ff;
    margin-right: 10px;
  }
}
</style>
