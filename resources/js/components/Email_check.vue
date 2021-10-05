<template>
    <el-form
        ref="form"
        :model="form"
        label-width="80px"
        label-position="top"
        @submit.native.prevent
    >
        <el-form-item label="邮箱">
            <div class="userError">
                <el-input
                    v-model.trim="checkForm.email"
                    clearable
                    :disabled="true"
                ></el-input>
            </div>
        </el-form-item>
        <el-form-item label="邮箱验证码" prop="feedback">
            <el-input
                v-model.trim="checkForm.feedback"
                clearable
            ></el-input>
        </el-form-item>
        <br/>
        <el-button type="text">未收到邮件？</el-button>
        <br/>
        <el-button native-type="submit" @click="to_loginPage">验证</el-button>
    </el-form>

</template>

<script>
export default {
    name: "Email_check",
    data() {
        return {
            checkForm: {
                email: '服务器传回来的email',
                feedback: ''
            }
        }
    },
    methods: {
        to_loginPage() {
            this.$router.push({
                path: "/login",
            });
        }
    }

}
</script>

<style scoped>

</style>
