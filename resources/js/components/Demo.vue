<template>
    <div style="height: 100%;width: 100%">
        <div>
            <h2>{{simulation_user_name}}/{{ simulation_name }}</h2>
        </div>
        <el-divider></el-divider>
        <div>
            <h3>简介</h3>
            <h4>{{synopsis}}</h4>
            <h4>点赞数: {{likes}}</h4>
            <el-button type="text" :icon="if_like()" :disabled="like" @click="submit_like" size="large">点赞</el-button>
        </div>
        <el-divider></el-divider>
        <div>
            <h3>展示区</h3>
            <div style="background-color: white;">
                <el-button size="small" type="primary" @click="toggle" v-if="simulation_exists">全屏</el-button>
            </div>
        </div>
        <div style="margin: 15px 0;"></div>
        <fullscreen :fullscreen="fullscreen" :teleport="teleport" :page-only="pageOnly"
                    :fullscreen-class="fullscreen_class" style="height: 100%;width: 100%"
                    v-if="simulation_exists">
            <iframe :src="simulation_url" allow="fullscreen" style="height: 100%;width: 100%"
                    ref="demo_iframe"></iframe>
        </fullscreen>
        <h2 v-if="!simulation_exists">对不起, 不存在预览!</h2>
        <div style="margin: 10px 0;"></div>
        <el-divider></el-divider>
        <h3>评论区</h3>
        <div v-for="(parent_comment, index) in parent_comments_list" style="margin-top: 30px">
            <el-divider content-position="left">
                <div>
                    <el-avatar size="small" :src="parent_comment.avatar_url" style="vertical-align:middle;"></el-avatar>
                    <span style="vertical-align:middle; margin-left: 5px">{{ parent_comment.user_name }}</span>
                </div>
            </el-divider>
            <div>{{ parent_comment.comment_content }}</div>
            <div style="margin-left: 10%" v-for="comment in comments_list"
                 v-if="parent_comment.comment_id === comment.parent_comment_id">
                <el-avatar size="small" :src="comment.avatar_url"
                           style="vertical-align:middle; margin: 10px"></el-avatar>
                <span style="vertical-align:middle; margin-left: 10px">{{ comment.comment_content }}</span>
                <el-button type="text" @click="delete_comment(comment)" size="small"
                           v-if="user_id === comment.user_id" style="margin-left: 20px">删除
                </el-button>
            </div>
            <el-divider content-position="right">
                <div style="height: 32px"><span>{{ parent_comment.create_time }}</span>
                    <el-divider direction="vertical"></el-divider>
                    <el-button type="text" @click="reply_comment(parent_comment)" size="small">回复</el-button>
                    <el-button type="text" @click="delete_comment(parent_comment)" size="small"
                               v-if="user_id === parent_comment.user_id">删除
                    </el-button>
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
            likes: 0,
            simulation_name: "",
            synopsis: "",
            simulation_user_name: "",
            show_cancel_rate: false,
            loading_demo: false,
            user_id: "",
            simulation_exists: true,
            fullscreen_class: "background-color: #66afe9",
            fullscreen: false,
            teleport: true,
            pageOnly: false,
            comment_to_post: '',
            comments_list: [],
            parent_comments_list: [],
            like: true,
            disable_rate: false,
            current_simulation_id: "",
            rate_to_post: null,
            colors: ['#99A9BF', '#F7BA2A', '#FF9900'],
            simulation_url: "",
        }
    },
    methods: {
        delete_comment(comment) {
            this.$api
                .post("/physlet_api/deleteCom", {coid: comment.comment_id})
                .then(response => {
                    if (response.data.code === 200) {
                        this.$message({
                            message: "删除评论成功!",
                            type: "success"
                        })
                        location.reload()
                    } else window.alert(response.data.message)
                })
        },
        submit_like() {
            this.$api
                .post("/physlet_api/like", {sid: this.current_simulation_id})
                .then(response => {
                    if (response.data.code === 200) {
                        this.like = true
                        location.reload()
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
                .then(({value}) => {
                    console.log(value)
                    this.$api
                        .post("/physlet_api/sendCom", {
                            sid: this.current_simulation_id,
                            content: value,
                            pid: parent_comment.comment_id
                        })
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
                if (response.data.code === 404) {
                    this.simulation_exists = false
                    this.synopsis = response.data.data.synopsis
                    this.simulation_name = response.data.data.sname
                    this.simulation_user_name = response.data.data.uname
                    this.likes = response.data.data.likes
                }
                else if (response.data.code === 200) {
                    this.simulation_url = response.data.data.url
                    this.synopsis = response.data.data.synopsis
                    this.simulation_name = response.data.data.sname
                    this.simulation_user_name = response.data.data.uname
                    this.likes = response.data.data.likes
                }
            })
        this.$api
            .get("/physlet_api/myInfo")
            .then(response => {
                if (response.data.code === 200) {
                    this.user_id = response.data.data.uid;
                } else window.alert(response.data.message)
            })
    },
    mounted() {
        this.$api
            .post("/physlet_api/checkLike", {sid: this.current_simulation_id})
            .then(response => {
                if (response.data.code === 200) {
                    console.log(response.data.data)
                    this.like = response.data.data
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
                        parent_comment.comment_avatar_url = data[syn].avatar
                        parent_comment.user_name = data[syn].uname
                        parent_comment.comment_id = data[syn].coid
                        parent_comment.comment_content = data[syn].content
                        parent_comment.create_time = data[syn].create_time
                        this.parent_comments_list.push(parent_comment)
                    } else {
                        let comment = {}
                        comment.user_id = data[syn].uid
                        comment.comment_avatar_url = data[syn].avatar
                        comment.user_name = data[syn].uname
                        comment.parent_comment_id = data[syn].pid
                        comment.comment_id = data[syn].coid
                        comment.comment_content = data[syn].content
                        this.comments_list.push(comment)
                    }
                }
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
