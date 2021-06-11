const state = {
  userInfo: {}
};

const getters = {
  user: state => state.user
};

const actions = {
  setUserInfo(content,data){
    content.commit({
      type:'SET_USER_INFO',
      userInfo:data.userInfo
    })
  }
};

const mutations = {
  // 初始化数据
  init(state){
    console.log(JSON.parse(window.sessionStorage.getItem('token')));
    state.userInfo = this.$utils.common.getStorage('totken')
  },
  SET_USER_INFO:(state,data)=>{
    state.userInfo = data.userInfo
  }
};

export default {
  // 是否开启命名空间
  namespaced:true,
  state,
  getters,
  actions,
  mutations
};
