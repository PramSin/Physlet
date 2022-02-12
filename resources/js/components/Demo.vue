<template>
    <div style="height: 100%;width: 100%">
        <div class="row">
            <h3 class="col-md-6">展示区</h3>
            <div class="col-md-6" style="background-color: white">
                <el-button class="pull-right" type="primary" @click="toggle">
                    全屏
                </el-button>
            </div>
        </div>
        <div style="margin: 15px 0;"></div>
        <fullscreen :fullscreen="fullscreen" :teleport="teleport" :page-only="pageOnly"
                    :fullscreen-class="fullscreen_class" style="height: 100%;width: 100%">
            <iframe :src="demo_src" allow="fullscreen" style="height: 100%;width: 100%"
                    ref="demo_iframe"></iframe>
        </fullscreen>
        <div style="margin: 50px 0;"></div>
        <h4>评分</h4>
        <el-rate
            :value="rate_to_post"
            :colors="colors"
            :disabled='disable_rate'
            @change="post_rate">
        </el-rate>
        <div style="margin: 10px 0;"></div>
        <el-link type="info"
                 v-if="show_cancel_rate"
                 :underline="false"
                 @click="cancel_rate"
        >取消评分
        </el-link>
        <h3>评论区</h3>
        <div style="margin: 20px 0;"></div>
        <div v-for="comment in comments">
            <div>{{ comment.user_id }}</div>
            <div>{{ comment.content }}</div>
        </div>
        <el-input
            type="textarea"
            placeholder="请输入评论"
            style="width: 50%"
            :autosize="{ minRows: 2 }"
            v-model="comment_to_post">
        </el-input>
        <div style="margin: 20px 0;"></div>
        <el-button type="primary" native-type="submit" @click="post_comment">评论</el-button>
    </div>
</template>

<script>
export default {
    name: "Demo",

    data() {
        return {
            show_cancel_rate: false,
            loading_demo: false,
            fullscreen_class: "background-color: #66afe9",
            fullscreen: false,
            teleport: true,
            pageOnly: false,
            comment_to_post: '',
            comments: [],
            disable_rate: false,
            rate_to_post: null,
            colors: ['#99A9BF', '#F7BA2A', '#FF9900'],
            demo_src: '',
            // https://www.openstreetmap.org/export/embed.html?bbox=-0.004017949104309083%2C51.47612752641776%2C0.00030577182769775396%2C51.478569861898606&layer=mapnik
        }
    },
    methods: {
        cancel_rate() {
            let params = this.$route.query
            params.rate = '0'
            this.$api
                .get('/physlet_api/postRate', {params})
                .then(() => {
                        this.disable_rate = false
                        this.rate_to_post = null
                        this.show_cancel_rate = false
                    }
                )
        },
        toggle() {
            this.fullscreen = !this.fullscreen
        },
        post_rate(value) {
            let params = this.$route.query
            params.rate = '' + value
            this.$api
                .get('/physlet_api/postRate', {params})
                .then(() => {
                        this.disable_rate = true
                        this.show_cancel_rate = true
                    }
                )
        },
        post_comment() {//todo 发表评论区
        },
    },
    beforeMount() {
        let params = this.$route.query
        this.$api
            .get('/physlet_api/getPackage', {params})
            .then(response => {
                let data = response.data.data;
                this.demo_src = data
            })
            .then()
    },
    mounted() {
        if (this.demo_src === '') {
            this.demo_src = 'https://www.openstreetmap.org/export/embed.html?bbox=-0.004017949104309083%2C51.47612752641776%2C0.00030577182769775396%2C51.478569861898606&layer=mapnik'
        }
    }
}
</script>

<style scoped>
.fullscreen-wrapper {
    width: 100%;
    height: 100%;
    background: #333;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

</style>
