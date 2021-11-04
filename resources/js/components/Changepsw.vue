<template>
  <el-form
      ref="form"
      :model="form"
      label-position="top"
      @submit.native.prevent
      :rules="rules"
  >
    <el-form-item label="原密码" prop="ori_password">
      <el-input
          v-model.trim="form.ori_password"
          clearable
          show-password
      ></el-input>
    </el-form-item>
    <el-form-item label="密码" prop="password">
      <el-input
          v-model.trim="form.password"
          clearable
          show-password
      ></el-input>
    </el-form-item>
    <el-form-item label="密码确认" prop="password_confirm">
      <el-input
          v-model.trim="form.password_confirm"
          clearable
          show-password
      ></el-input>
    </el-form-item>
    <br/><br/>
    <el-checkbox label="记住账号" v-model="isRemember"></el-checkbox>
    <br/>
    <el-button type="primary" native-type="submit" @click="onSubmit">改变密码</el-button>
  </el-form>

</template>


<script>


export default {
  name: "Changepsw",
  data() {

    return {

      form: {
        password: '',
        ori_password: '',
        password_confirm: '',
      },
      rules: {
        password: [
          {required: true, message: '不能为空', trigger: 'blur'},
        ],
        ori_password: [
          {required: true, message: '密码不能为空', trigger: 'blur'},
        ],
        password_confirm: [
          {required: true, message: '密码不能为空', trigger: 'blur'},
        ],
      },
      isRemember: false,
    };
  },
  mounted() {

  },
  methods: {
      to_register() {
      this.$router.replace({
        path: "/login",
      });
    },
    onSubmit() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          if (this.$store.state.debug) {
            this.$router.replace({path: '/home'} )
          }
          else {
            this.$http
                .post('/physlet_api/changePassword', this.form)
                .then(response => {
                  if (response.data.code !== 200) {
                    this.$notify.error({
                      title: '错误',
                      message: response.data.message,
                    });
                  } else {
                    this.$notify({
                      type: 'success',
                      title: response.data.message,
                      message: null,

                    });
                    if (this.isRemember) {
                      localStorage.setItem('username',this.form.userName)
                      localStorage.removeItem('userpsw')
                      localStorage.setItem('userpsw',this.form.userName)
                      this.$router.replace({path: '/home'})
                    }
                    else {
                      localStorage.removeItem('userpsw')
                      localStorage.removeItem('username')
                      this.$router.replace({path: '/login'})
                    }
                  }
                })
                .catch()
                .then(() => {
                });

          }
        } else {
          this.$message.error('请确认表单填写正确后再试');
          return false;
        }
      });
    },


  },
}
</script>
