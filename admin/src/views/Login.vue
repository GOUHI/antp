<template>
  <div class="login-main">
    <div class="page-account-header">
      <div class="page-login-top"></div>
    </div>
    <div class="page-account-container">
      <div class="page-account-top">
        <div class="page-account-top-logo">
          <img src="@/assets/images/logo.png" alt="logo" />
        </div>
        <div class="page-account-top-desc">Ant Admin Pro 企业级中台前端/设计解决方案</div>
      </div>

      <!-- 表单数据 -->
      <div class="login">
      <a-form-model ref="ruleForm" :model="query" :rules="rules" @submit="handleSubmit">
        <a-form-model-item prop="account">
          <a-input
            placeholder="请输入账户"
            v-model="query.account"
          >
          <a-icon slot="prefix" type="user" style="color:rgba(0,0,0,.25)" />
          </a-input>
        </a-form-model-item>
        <a-form-model-item prop="password">
          <a-input
            type="password"
            placeholder="请输入密码"
            v-model="query.password"
          >
          <a-icon slot="prefix" type="lock" style="color:rgba(0,0,0,.25)" />
          </a-input>
        </a-form-model-item>
        <a-form-model-item>
          <div class="jus">
            <a-checkbox @change="onAutomatic" v-model="query.automatic">
              自动登录
            </a-checkbox>
            <a>忘记密码</a>
          </div>
        </a-form-model-item>
        <a-form-model-item>
          <a-spin :spinning="spinning">
            <a-button type="primary" html-type="submit" :disabled="retButtonDisabled" block>
              登 录
            </a-button>
          </a-spin>
        </a-form-model-item>
      </a-form-model>
        <!-- <Form ref="formInfo" :model="formInfo" :rules="ruleInfo">
          <FormItem prop="account">
            <Input type="text" prefix="ios-contact-outline" v-model="formInfo.account" size="large"></Input>
          </FormItem>
          <FormItem prop="password">
            <Input
              type="password"
              prefix="ios-lock-outline"
              v-model="formInfo.password"
              size="large"
            ></Input>
          </FormItem>
          <FormItem>
            <Checkbox v-model="automatic" class="fl" size="large">自动记录</Checkbox>
            <a class="fr">忘记密码</a>
          </FormItem>
          <FormItem>
            <Button type="primary" size="large" long @click="handleSubmit('formInfo')">登录</Button>
          </FormItem>
        </Form> -->
        <!-- <div class="page-account-other">
          <span class="fl">
            其他登录方式
            <img src="../assets/img/icon-social-wechat.svg" alt="wechat" />
            <img src="../assets/img/icon-social-qq.svg" alt="qq" />
            <img src="../assets/img/icon-social-weibo.svg" alt="weibo" />
          </span>
          <a href class="fr">注册账户</a>
        </div>-->
      </div>
    </div>
    <div class="global-footer">Copyright © 2021 antp中后台产品</div>
  </div>
</template>
<script>
export default {
  name:'login',
  data() {
    return {
      spinning: false,
      form: this.$form.createForm(this, { name: 'coordinated' }),
      query:{
        account:'',
        password:'',
        automatic: false
      },
      rules:{
        account:[
          {required:true,message:'请输入账户',trigger: 'blur'}
        ],
        password:[
          {required:true,message:'请输入密码',trigger: 'blur'},
          { min:6, max:20, message:'密码长度6-20位', trigger: 'blur'}
        ]
      }
    };
  },
  computed:{
    retButtonDisabled:function(){
      let ret = true
      if(this.query.account !=='' && this.query.password !==''){
        ret = false
      }
      return ret
    }
  },
  mounted(){
    this.$nextTick(()=>{
      this.form.validateFields()
    })
  },
  methods: {
    // 是否勾选自动登录
    onAutomatic(e){
      console.log(e.target.checked);
    },
    
    /**
     * 提交表单
     */
    handleSubmit(){
      this.$refs.ruleForm.validate(valid => {
        if (valid) {
          const data = this.query
          this.$api.login.index(data)
          .then((res)=>{
            this.$utils.common.setStorage('token',res.token)
            this.$utils.common.setStorage('adminInfo',res,2)
            this.$store.dispatch({
              type:'user/setUserInfo',
              userInfo:res
            })
          })
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    }
  }
}
</script>
<style lang="less" scoped>
.login-main{
  background-image: url(../assets/images/body.8aa7c4a6.svg);
  background-repeat: no-repeat;
  background-position: 50%;
  background-size: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow: auto;
}

.page-account-container {
  flex: 1;
  padding: 32px 0 24px 0;
  width: 384px;
  margin: 0 auto;
  .page-account-top {
    text-align: center;
    padding: 32px 0;
    img {
      height: 75px;
    }
    .page-account-top-desc {
      font-size: 14px;
      color: #808695;
    }
  }
  .page-account-other {
    img {
      width: 24px;
      margin-left: 16px;
      cursor: pointer;
      vertical-align: middle;
      opacity: 0.7;
      transition: all 0.2s ease-in-out;
    }
  }
}

.global-footer {
  margin: 48px 0 24px 0;
  padding: 0 16px;
  text-align: center;
}

.jus{
  display:flex;
  justify-content:space-between;
}
</style>