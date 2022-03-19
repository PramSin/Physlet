<template>
    <el-container>
        <el-aside></el-aside>
        <el-main>
            <h3>修改个人信息</h3>
            <el-form :model="edit_userinfo" style="width: 60%" :rules="rules" ref="edit_userinfo">
                <el-form-item label="用户名">
                    <el-input v-model="edit_userinfo.username"></el-input>
                </el-form-item>
                <el-form-item label="手机号" prop="phone_number">
                    <el-input v-model="edit_userinfo.phone_number"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="submit_userinfo('edit_userinfo')">修改</el-button>
                </el-form-item>
            </el-form>
            <h3> 上传头像 </h3>
            <el-upload
                class="avatar-uploader"
                action=""
                :show-file-list="false"
                :auto-upload="false"
                :on-change="upload_avatar">
                <img v-if="upload_avatar_url" :src="upload_avatar_url" class="avatar">
                <i v-else class="el-icon-plus avatar-uploader-icon"></i>
            </el-upload>
            <el-button size="small" type="success" @click="submit_avatar">点击上传头像</el-button>
        </el-main>
        <el-aside>
            <el-card>
                <div style="text-align: center">
                    <el-avatar :size="125" :src="avatar_url" v-loading="loading_avatar"></el-avatar>
                    <div style="text-align:center; font-size: 20px; margin-top: 20px">欢迎, {{ this.display_username }}
                    </div>
                </div>
                <div style="text-align: center" v-if="!loading_avatar">
                </div>
            </el-card>
        </el-aside>
    </el-container>
</template>

<script>
export default {
    name: "UserInfo",
    data() {
        const phone_check = (rule, value, callback) => {
            if (!value) {
                callback();
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
            edit_userinfo: {
                username: "",
                phone_number: "",
            },
            avatar_url: "",
            upload_avatar_url: "",
            loading_avatar: true,
            display_username: "",
            rules: {
                phone_number: [
                    {required: false, validator: phone_check, trigger: 'blur'}
                ],
            },
        }
    },
    methods: {
        submit_userinfo(form) {
            this.$refs[form].validate((valid) => {
                if (valid) {
                    this.$api
                        .post('/physlet_api/changeInfo', this.edit_userinfo)
                        .then(response => {
                            if (response.data.code === 200) {
                                this.$message({
                                    message: '修改信息成功!',
                                    type: 'success'
                                });
                                this.location.reload()
                            }
                        })
                        .catch()
                        .then(() => {
                        });
                } else {
                    this.$message.error('请确认表单填写正确后再试');
                    return false;
                }
            });
        },
        upload_avatar(file) {
            this.files = file;
            this.upload_avatar_url = URL.createObjectURL(this.files.raw);
        },
        submit_avatar() {
            const formData = new FormData();
            formData.append("image", this.files.raw)
            const headers = {'Content-Type': 'multipart/form-data;boundary=","'};
            this.$api.post('/physlet_api/uploadAvatar',
                formData,
                {headers},
            ).then((res) => {
                res.data.files; // binary representation of the file
                res.status; // HTTP status
                this.$message({
                    message: "设置头像成功!",
                    type: "success"
                })
                location.reload()
            });
        },
    },
    mounted() {
        this.$api
            .get('/physlet_api/checkLogin')
            .then(response => {
                    if (response.data.code === 200) {
                        this.$api
                            .get('/physlet_api/myInfo')
                            .then(response => {
                                this.avatar_url = response.data.data.avatar
                                this.display_username = response.data.data.uname
                                this.number_of_simulations = response.data.data.sims
                                this.loading_avatar = false
                            })
                    }
                    else {
                        this.$router.replace({path: "/login"})
                    }
                }
            )
    },
}
</script>

<style scoped>
.avatar-uploader .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.avatar-uploader .el-upload:hover {
    border-color: #409EFF;
}

.avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
}

.avatar {
    width: 178px;
    height: 178px;
    display: block;
}
</style>
