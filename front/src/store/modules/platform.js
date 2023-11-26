import {getPlatform} from "@/api/platform";

const platform = {
  state: {
    info: {}
  },

  mutations: {
    SET_PLATFORM_INFO: (state, info) => {
      state.info = info
    }
  },

  actions: {
    GetPlatform({commit}, info) {
      getPlatform().then(res => {
        console.log(res.data.data)
        commit('SET_PLATFORM_INFO', res.data.data)
      })
    }
  }
}

export default platform