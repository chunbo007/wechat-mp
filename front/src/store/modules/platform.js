import {getPlatform} from "@/api/platform";

const platform = {
  state: {
    platformList: [],
    currentPlatform: {}
  },

  mutations: {
    GET_PLATFORM_LIST: (state, info) => {
      state.platformList = info
    },

    SET_PLATFORM: (state, info) => {
      state.currentPlatform = info
    }
  },

  actions: {
    GetPlatform({commit}, info) {
      getPlatform().then(res => {
        commit('GET_PLATFORM_LIST', res.data.data)
      })
    },

    SetPlatform({commit}, info) {
      commit('SET_PLATFORM', info)
    }
  }
}

export default platform