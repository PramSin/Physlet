<template>
    <el-container>
        <el-aside></el-aside>
        <el-main>1111111</el-main>
        <el-aside>
            <el-card style="margin-top: 50px">
                <div slot="header" style="text-align: center">
                    <el-avatar :size="125" :src="avatar_url" v-loading="loading_avatar"></el-avatar>
                    <div style="text-align:center; font-size: 20px">name</div>
                </div>
                <div style="text-align: center" v-if="!loading_avatar">
                </div>
            </el-card>
            <el-card style="margin-top: 50px" v-else>
                <div style="text-align: center; font-size: large">
                    登录查看我的模拟
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
            loading_avatar: true,
            display_username: "",
        }
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

</style>
