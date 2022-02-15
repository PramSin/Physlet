<template>
    <div style="height: 100%;width: 100%">
        <div>
            <h3>展示区</h3>
            <div style="background-color: white;">
                <el-button size="small" type="primary" @click="toggle">全屏</el-button>
                <el-button type="text" :icon="if_like()" :disabled="like" @click="submit_like">点赞</el-button>
            </div>
        </div>
        <div style="margin: 15px 0;"></div>
        <fullscreen :fullscreen="fullscreen" :teleport="teleport" :page-only="pageOnly"
                    :fullscreen-class="fullscreen_class" style="height: 100%;width: 100%">
            <iframe :src="demo_src" allow="fullscreen" style="height: 100%;width: 100%"
                    ref="demo_iframe"></iframe>
        </fullscreen>
        <div style="margin: 10px 0;"></div>
        <h3>评论区</h3>
        <div style="margin_top: 20px;"></div>
        <div v-for="(parent_comment, index) in parent_comments_list">
            <el-divider content-position="left">{{ parent_comment.user_id }}</el-divider>
            <div>{{ parent_comment.comment_content }}</div>
            <div style="float:right; width: 90%" v-for="comment in comments_list"
                 v-if="parent_comment.coid === comment.pid">{{ comment.comment_content }}
            </div>
            <el-divider content-position="right">
                <div><span>{{ parent_comment.create_time }}</span>
                    <el-divider direction="vertical"></el-divider>
                    <el-button type="text" @click="reply_comment(parent_comment)">回复</el-button>
                </div>
            </el-divider>
        </div>
        <el-input
            type="textarea"
            placeholder="请输入评论"
            style="width: 50%"
            :autosize="{ minRows: 2 }"
            v-model="comment_to_post">
        </el-input>
        <div style="margin: 20px"></div>
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
            comments_list: [],
            parent_comments_list: [],
            like: false,
            disable_rate: false,
            current_simulation_id: "",
            rate_to_post: null,
            colors: ['#99A9BF', '#F7BA2A', '#FF9900'],
            demo_src: '',
            // https://www.openstreetmap.org/export/embed.html?bbox=-0.004017949104309083%2C51.47612752641776%2C0.00030577182769775396%2C51.478569861898606&layer=mapnik
        }
    },
    methods: {
        submit_like() {
            this.$api
                .post("/physlet_api/like", {sid: this.current_simulation_id})
                .then(response => {
                    if (response.data.code === 200) {
                        this.like = true
                    } else window.alert(response.data.message)
                })
        },
        if_like() {
            if (this.like === true) {
                return "el-icon-caret-top"
            } else return "el-icon-arrow-up"
        },
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
        post_comment() {
            this.$api
                .post("/physlet_api/sendCom", {sid: this.current_simulation_id, content: this.comment_to_post})
                .then(response => {
                    if (response.data.code === 200) {
                        this.$message({
                            message: "评论成功!",
                            type: "success"
                        })
                        location.reload()
                    } else window.alert(response.data.message)
                })
        },
        reply_comment(parent_comment) {
            this.$prompt("请输入评论", "回复", {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                inputValidator: this.reply_validator,
                inputErrorMessage: "评论不能为空"
            })
                .then(({content}) => {
                    this.$api
                        .post("/physlet_api/sendCom", {sid: this.current_simulation_id, content: content, pid:parent_comment.coid})
                        .then(response => {
                            if (response.data.code === 200) {
                                this.$message({
                                    message: "评论成功!",
                                    type: "success"
                                })
                                location.reload()
                            } else window.alert(response.data.message)
                        })
                })
        },
        reply_validator(comment) {
            return comment !== "";
        },
        },
        beforeMount() {
            let params = this.$route.query
            this.current_simulation_id = params.sid
            this.$api
                .post('/physlet_api/getSim', params)
                .then(response => {
                    let data = response.data.data;
                    this.demo_src = data
                })
        },
        mounted() {
            if (this.demo_src === '') {
                this.demo_src = 'https://www.openstreetmap.org/export/embed.html?bbox=-0.004017949104309083%2C51.47612752641776%2C0.00030577182769775396%2C51.478569861898606&layer=mapnik'
            }
            this.$api
                .post("/physlet_api/checkLike", {sid: this.current_simulation_id})
                .then(response => {
                    if (response.data.code === 200) {
                        this.like = true
                    }
                })
            this.$api
                .post("/physlet_api/getComs", {sid: this.current_simulation_id})
                .then(response => {
                    let data = response.data.data;
                    for (let syn = 0; syn < data.length; syn++) {
                        if (data[syn].pid === 0) {
                            let parent_comment = {}
                            parent_comment.user_id = data[syn].uid
                            parent_comment.comment_id = data[syn].coid
                            parent_comment.comment_content = data[syn].content
                            parent_comment.create_time = data[syn].create_time
                            this.parent_comments_list.push(parent_comment)
                        } else {
                            let comment = {}
                            comment.user_id = data[syn].uid
                            comment.parent_comment_id = data[syn].pid
                            comment.comment_content = data[syn].content
                            this.comments_list.push(comment)
                        }
                    }
                    console.log(response.data.data)
                })
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
