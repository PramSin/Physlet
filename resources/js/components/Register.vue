<template>
    <el-container class="background-wrapper">
        <el-aside></el-aside>
        <el-main>
            <div class="form-wrapper">
                <h2 class="form-title">注册</h2>
                <el-form :model="registerForm" :rules="rules" ref="registerForm" style="width: 50%">
                    <el-form-item label="用户名" prop="username">
                        <el-input v-model="registerForm.username"
                                  clearable
                        ></el-input>
                    </el-form-item>
                    <el-form-item :inline="true" label="邮箱" prop="email">
                        <el-input v-model="registerForm.email"
                                  clearable
                        ></el-input>
                    </el-form-item>
                    <el-form-item label="手机号" prop="number">
                        <el-input v-model.number="registerForm.number"
                                  clearable
                        ></el-input>
                    </el-form-item>
                    <el-form-item label="密码" prop="password">
                        <el-input v-model="registerForm.password" clearable
                                  show-password
                        ></el-input>
                    </el-form-item>
                    <el-form-item label="确认密码" prop="password_check">
                        <el-input v-model="password_check" clearable
                                  show-password
                                  @keyup.enter.native="submitForm"
                        ></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="submitForm('registerForm') " v-loading="loading">立即注册</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </el-main>
        <el-aside></el-aside>
    </el-container>
</template>
<script>
export default {
    name: 'Register',
    data() {
        let if_email_legal = (rule, value, callback) => {
            let reg = /^([a-z0-9_.-]+)@([a-z0-9_.-]+)$/;
            if (value === '') {
                return callback(new Error('请输入邮箱！'))
            } else if (!reg.test(value)) {
                return callback(new Error('邮箱格式不匹配！'))
            } else {
                callback();
            }

        };
        const psw_check = (rule, value, callback) => {
            if (value !== this.registerForm.password) {
                callback(new Error('两次输入密码不一致!'));
            } else {
                callback();
            }
        };
        const phone_check = (rule, value, callback) => {
            if (!value) {
                callback(new Error('手机号不能为空！'));
            } else {
                const reg = /^1[34578][0-9]\d{8}$/;
                if (reg.test(value)) {
                    callback();
                } else {
                    return callback(new Error('请输入正确的手机号'));
                }

            }

        };
        return {
            registerForm: {
                email: '',
                username: '',
                number: '',
                password: '',
            },
            password_check: "",
            rules: {
                email: [
                    {required: true, validator: if_email_legal, trigger: 'blur'}
                ],
                email_check: [
                    {required: true, trigger: 'blur'}
                ],
                username: [
                    {required: true, message: '请输入用户名', trigger: 'blur'}
                ],
                number: [
                    {required: true, validator: phone_check, trigger: 'blur'}
                ],
                password: [
                    {required: true, message: '请设置密码', trigger: 'blur'}
                ],
                password_twice: [
                    {required: true, validator: psw_check, trigger: 'blur'}
                ],
            }
        };
    },
    methods: {
        submitForm() {
            this.$refs["registerForm"].validate((valid) => {
                if (valid) {
                    this.loading = true;
                    this.$api
                        .post('/physlet_api/register', this.registerForm)
                        .then(response => {
                            if (response.data.code !== 0) {
                                this.$notify.error({
                                    title: '错误',
                                    message: response.data.message,
                                });
                            } else {
                                this.textLoginBtn = '注册成功';
                                this.$notify({
                                    type: 'success',
                                    title: this.textLoginBtn,
                                    message: null,
                                });
                                this.$store.dispatch('initialize');
                                this.$router.push({path: '/Me'});
                            }
                        })
                        .catch()
                        .then(() => {
                            this.loading = false;
                        });
                } else {
                    this.$message.error('请确认表单填写正确后再试');
                    return false;
                }
            });
        },
    }
}
</script>
