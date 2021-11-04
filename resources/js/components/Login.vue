<template>
    <el-form
        ref="form"
        :model="form"
        label-position="top"
        @submit.native.prevent
        :rules="rules"
    >
        <el-form-item label="邮箱" prop="emailOrUsername">
            <el-input
                v-model.trim="form.emailOrUsername"
                clearable
            ></el-input>
        </el-form-item>
        <el-form-item label="密码" prop="password">
            <el-input
                v-model.trim="form.password"
                clearable
                show-password
            ></el-input>
        </el-form-item>
        <br/><br/>
        <el-checkbox label="记住账号" v-model="isRemember"></el-checkbox>
        <br/>
        <el-button type="text" @click="to_register">没有帐号？点击注册</el-button>
        <br/>
        <el-button type="primary" native-type="submit" @click="onSubmit">登录</el-button>
    </el-form>

</template>


<script>


export default {
    name: "Login",
    data() {
        return {
            form: {
                emailOrUsername: '',
                password: '',
            },
            rules: {
                emailOrUsername: [
                    {required: true, message: '邮箱不能为空', trigger: 'blur'},
                ],
                password: [
                    {required: true, message: '密码不能为空', trigger: 'blur'},
                ],
            },
            isRemember: false,
        };
    },
    mounted() {

    },
    methods: {
        onSubmit() {
            this.$refs.form.validate((valid) => {
                if (valid) {
                    if (this.$store.state.debug) {
                        this.$router.replace({path: '/home'})
                    } else {
                        this.$http
                            .post('/physlet_api/login', this.form)
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

                                    //使用localstorage储存token
                                    if (this.isRemember) {
                                        localStorage.setItem('username', this.form.emailOrUsername)
                                        localStorage.setItem('userpsw', this.form.password)
                                    } else {
                                        localStorage.setItem('username', this.form.emailOrUsername)
                                        this.$store.state.authorized = true
                                    }
                                    localStorage.setItem('is_authorized', 'true')
                                    this.$router.replace({path: '/home'})
                                    location.reload()
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

        to_register() {
            this.$router.replace({
                path: "/login",
            });
        },

    },
}
</script>
