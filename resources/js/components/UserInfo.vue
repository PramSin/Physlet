<template>
    <el-container>
        <el-aside></el-aside>
        <el-main>
            <h3> 上传头像 </h3>
            <el-upload
                class="avatar-uploader"
                action="/physlet_api/uploadAvatar"
                :show-file-list ="false"
                :on-success="successfully_upload_avatar">
                <img v-if="upload_avatar_url" :src="upload_avatar_url" class="avatar">
                <i v-else class="el-icon-plus avatar-uploader-icon"></i>
            </el-upload>
        </el-main>
        <el-aside>
            <el-card style="margin-top: 50px">
                <div style="text-align: center">
                    <el-avatar :size="125" :src="avatar_url" v-loading="loading_avatar"></el-avatar>
                    <div style="text-align:center; font-size: 20px; margin-top: 20px">欢迎, {{this.display_username}}</div>
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
        return {
            avatar_url: "",
            upload_avatar_url: "",
            loading_avatar: true,
            display_username: "",
        }
    },
    methods: {
        successfully_upload_avatar () {
            this.upload_avatar_url = URL.createObjectURL(file.raw)
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
