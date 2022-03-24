<template>
    <el-container>
        <el-main>
            <h2>相关介绍</h2>
            <h3>主页浏览数 {{ portal_views }}</h3>
            <h3>总浏览时间 {{ total_view_time }} 分钟</h3>
            <h3 v-if="authorized">用户浏览时间 {{ user_view_time }} 分钟</h3>
        </el-main>
    </el-container>
</template>

<script>
export default {
    name: "About",
    data() {
        return {
            authorized: false,
            portal_views: 0,
            total_view_time: 0,
            user_view_time: 0,
        }
    },
    mounted() {
        this.$api
            .get('/physlet_api/checkLogin')
            .then(response => {
                if (response.data.code === 200) {
                    this.authorized = true;
                    this.$api
                        .get("/physlet_api/getUserTime")
                        .then(response => {
                            this.user_view_time = 5 * response.data.data.time;
                        })
                }
            })
        this.$api
            .get("/physlet_api/mainViews")
            .then(response => {
                this.portal_views = response.data.data.counts;
            })
        this.$api
            .get("/physlet_api/getTime")
            .then(response => {
                this.total_view_time = 5 * response.data.data.time;
            })
    }
}
</script>
