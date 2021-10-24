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
                loginPwd: [
                    {required: true, message: '密码不能为空', trigger: 'blur'},
                ],
            },
            isRemember: false,
        };
    },
    mounted() {

        this.getCookie();
    },
    methods: {
        onSubmit() {
            this.$refs.form.validate((valid) => {
                if (valid) {
                    this.$http
                        .post('/physlet_api/login', this.form, /*{emulateJSON: true}*/)
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
                                this.$router.push({path: '/home'});
                            }
                        })
                        .catch()
                } else {
                    this.$message.error('请确认表单填写正确后再试');
                    return false;
                }
            });
        },

        /*  第3步，当用户执行登录操作的时候，先看看用户名密码对不对
                  若不对，就提示登录错误
                  若对，就再看一下用户有没有勾选记住密码
                        若没勾选，就及时清空cookie，回到最初始状态
                        若勾选了，就把用户名和密码存到cookie中并设置7天有效期，以供使用
                          （当然也有可能是更新之前的cookie时间）
        */
        to_register() {
            this.$router.push({
                path: "/register",
            });
        },

        async loginPage() {
            // 发请求看看用户输入的用户名和密码是否正确
            /*const res = await this.$api.loginByUserName(this.form)
            if(res.isSuccess === false){
              this.$message.error("登录错误")
            }
            else{
              const self = this;
              // 第4步，若复选框被勾选了，就调用设置cookie方法，把当前的用户名和密码和过期时间存到cookie中
              if (self.isRemember === true) {
                // 传入账号名，密码，和保存天数（过期时间）3个参数
                //  1/24/60 测试可用一分钟测试，这样看着会比较明显
                self.setCookie(this.form.emailOrUsername, this.form.loginPwd, 1/24/60);
                // self.setCookie(this.form.emailOrUsername, this.form.loginPwd, 7); // 这样就是7天过期时间
              }
              // 若没被勾选就及时清空Cookie，因为这个cookie有可能是上一次的未过期的cookie，所以要及时清除掉
              else {
                self.clearCookie();
              }
              // 当然，无论用户是否勾选了cookie，路由该跳转还是要跳转的
              this.$router.push({
                name: "Home",
              });
            }*/
            this.$router.push({
                path: "/about",
            });
        },
        // 设置cookie
        setCookie(username, password, exdays) {
            var exdate = new Date(); // 获取当前登录的时间
            exdate.setTime(exdate.getTime() + 24 * 60 * 60 * 1000 * exdays); // 将当前登录的时间加上七天，就是cookie过期的时间，也就是保存的天数
            // 字符串拼接cookie,因为cookie存储的形式是name=value的形式
            window.document.cookie = "emailOrUsername" + "=" + username + ";path=/;expires=" + exdate.toGMTString();
            window.document.cookie = "password" + "=" + password + ";path=/;expires=" + exdate.toGMTString();
            window.document.cookie = "isRemember" + "=" + this.isRemember + ";path=/;expires=" + exdate.toGMTString();
        },
        // 第2步，若cookie中有用户名和密码的话，就通过两次切割取出来存到form表单中以供使用，若是没有就没有
        getCookie: function () {
            if (document.cookie.length > 0) {
                var arr = document.cookie.split("; "); //因为是数组所以要切割。打印看一下就知道了
                // console.log(arr,"切割");
                for (var i = 0; i < arr.length; i++) {
                    var arr2 = arr[i].split("="); // 再次切割
                    // console.log(arr2,"切割2");
                    // // 判断查找相对应的值
                    if (arr2[0] === "emailOrUsername") {
                        this.form.emailOrUsername = arr2[1]; // 转存一份保存用户名和密码
                    } else if (arr2[0] === "password") {
                        this.form.password = arr2[1];//可解密
                    } else if (arr2[0] === "isRemember") {
                        this.isRemember = Boolean(arr2[1]);
                    }
                }
            }
        },

        // 清除cookie
        /*clearCookie: fu![image](/img/bVcOHhz)*/
        /*this.setCookie("", "", -1); */// 清空并设置天数为负1天
    },
}
</script>
