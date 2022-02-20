<template>
    <el-container>
        <el-aside></el-aside>
        <el-main style="text-align: center">
            <el-card style="text-align: center;width: 400px;display: inline-block;margin-top: 150px; border-radius: 15px">
                <el-form
                    ref="form"
                    :model="form"
                    label-position="top"
                    @submit.native.prevent
                    :rules="rules"
                    :show-message="false"
                    :hide-required-asterisk="true"
                    label-width="30px"
                >
                    <el-form-item label="邮箱" prop="email_or_username">
                        <el-input
                            v-model.trim="form.email_or_username"
                            clearable
                            style="width: 250px"
                        ></el-input>
                    </el-form-item>
                    <el-form-item label="密码" prop="password">
                        <el-input
                            v-model.trim="form.password"
                            clearable
                            show-password
                            :hide-required-asterisk="true"
                            :show-message="false"
                            style="width: 250px"
                        ></el-input>
                    </el-form-item>
                    <br/>
                    <el-button type="text" @click="to_register">没有帐号？点击注册</el-button>
                    <br/>
                    <el-button type="primary" native-type="submit" @click="onSubmit">登录</el-button>
                </el-form>
            </el-card>
        </el-main>
        <el-aside></el-aside>
    </el-container>
</template>


<script>


export default {
    name: "Login",
    data() {
        return {
            form: {
                email_or_username: '',
                password: '',
            },
            rules: {
                email_or_username: [
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
        this.$api
            .get('/physlet_api/myInfo')
            .then(response => {
                if (response.data.code === 200) {
                        this.$router.replace({path: '/me'})
                }
            })
            .catch(()=>{})
    },
    methods: {
        onSubmit() {
            this.$refs.form.validate((valid) => {
                if (valid) {
                    if (this.$store.state.debug) {
                        this.$router.replace({path: '/portal'})
                    } else {
                        this.$api
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
                                    this.$router.replace({path: '/portal'})
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
                path: "/register",
            });
        },

    },
}
</script>
