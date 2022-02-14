<template>
    <el-container>
        <el-aside></el-aside>
        <el-main>
            <h3> 上传头像 </h3>
            <el-upload
                class="avatar-uploader"
                action=""
                :show-file-list ="false"
                :on-change="upload_avatar">
                <img v-if="upload_avatar_url" :src="upload_avatar_url" class="avatar">
                <i v-else class="el-icon-plus avatar-uploader-icon"></i>
            </el-upload>
            <el-button size="small" type="success" @click="submit_avatar">点击上传</el-button>
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
        upload_avatar(file) {
            this.files = file;
            console.log(this.files)
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
/*                location.reload()*/
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
                                console.log(this.avatar_url)
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
