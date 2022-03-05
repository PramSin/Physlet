<template>
    <el-container style="padding: 0; margin: 0" v-if="!loading_topbar">
        <!--   电脑客户端情况  -->
        <el-header>
            <el-menu :default-active="current_path_name" @select="jump_to" class="el-menu-demo" mode="horizontal">
                <el-menu-item index="portal">
                    主页
                </el-menu-item>
                <el-tooltip class="item" effect="dark" content="请先登录才能上传模拟" placement="bottom" :disabled="authorized">
                    <el-menu-item index="me" :disabled="!authorized">
                        我的模拟
                    </el-menu-item>
                </el-tooltip>
                <el-menu-item index="about">
                    关于我们
                </el-menu-item>
                <el-menu-item index="search">
                    <template>
                        <el-input placeholder="搜索" v-model="search_keywords" clearable
                                  style="width: 400px">
                            <el-select v-model="search_type" slot="prepend" style="width: 110px" placeholder="请选择">
                                <el-option label="搜索模拟" value="1"></el-option>
                                <el-option label="搜索用户" value="2"></el-option>
                            </el-select>
                            <el-button slot="append" @click="submit_search">搜索</el-button>
                        </el-input>
                    </template>
                </el-menu-item>
                <el-menu-item style="float: right" index="message">
                    <el-badge :value="unread_message_number" class="item" :hidden="!message_unread">
                        <el-button size="medium" round icon="el-icon-message-solid" v-if="authorized">通知</el-button>
                    </el-badge>
                </el-menu-item>
                <el-submenu index="user_information" style="float: right" v-if="authorized">
                    <template slot="title">
                        <el-avatar style="margin-right: 0px" v-loading="loading_small_avatar"
                                   :src="small_avatar_url"></el-avatar>
                    </template>
                    <el-menu-item index="user_info">账户信息</el-menu-item>
                    <el-menu-item index="change_password">修改密码</el-menu-item>
                    <el-menu-item @click="logout" index="logout">登出</el-menu-item>
                </el-submenu>
                <el-menu-item index="login" style="float: right" v-else>
                    <el-link type="primary" :underline=false style="font-size: large">请登录</el-link>
                </el-menu-item>
            </el-menu>
        </el-header>
        <el-main>
            <el-container>
                <el-aside style="width: 150px"></el-aside>
                <el-main>
                    <router-view :key="$route.fullPath"></router-view>
                </el-main>
                <el-aside style="width: 150px"></el-aside>
            </el-container>
        </el-main>
        <el-footer>1111111</el-footer>
    </el-container>
</template>

<script>

export default {
    name: 'App',
    data() {
        return {
            componentKey: 0,
            jumpto: true,
            current_path_name: "",
            search_keywords: "",
            loading_small_avatar: true,
            small_avatar_url: "",
            authorized: false,
            search_type: "",
            message_list: [],
            unread_message_number: 0,
            message_unread: false,
            loading_topbar: false
        }
    },
    mounted() {
        this.$api
            .get('/physlet_api/checkLogin')
            .then(response => {
                    if (response.data.code === 200) {
                        this.authorized = true
                        this.$api
                            .get('/physlet_api/myInfo')
                            .then(response => {
                                this.small_avatar_url = response.data.data.avatar
                                this.loading_small_avatar = false
                            })
                        this.$api
                            .get("/physlet_api/messageList")
                            .then(response => {
                                let data = response.data.data
                                for (let syn = 0; syn < data.length; syn++) {
                                    let message = {};
                                    message.message_id = data[syn].mid;
                                    message.message_state = data[syn].state;
                                    if (message.message_state) this.unread_message_number++;
                                    message.message_class = data[syn].class;
                                    message.message_user_id = data[syn].uid;
                                    message.message_user_name = data[syn].uname;
                                    message.message_simulation_id = data[syn].sid;
                                    message.message_simulation_name = data[syn].sname;
                                    message.message_comment_id = data[syn].coid;
                                    message.message_comment_cotent = data[syn].content;
                                    message.message_create_time = data[syn].create_time;
                                    this.message_list.push(message);
                                }
                                if (this.unread_message_number !== 0) this.message_unread = true;
                            })
                    }
                }
            )
        this.current_path_name = this.$route.path.substring(1)
        if (this.$route.path === "/search_page") {
            this.search_keywords = this.$route.query.key;
            this.search_type = this.$route.query.type
        }
    },
    watch: {
        "$route"() {
            this.current_path_name = this.$route.path.substring(1)
        }
    },
    methods: {
        jump_to(index) {
            if (index === "me" || index === "portal" || index === "about" || index === "user_info" || index === "change_password" || index === "login") {
                this.componentKey = !this.componentKey
                this.$router.push({path: "/" + index}).catch(() => {
                })
            } else {
                this.current_path_name = ""
            }
        },
        submit_search() {
            this.componentKey = !this.componentKey
            this.$router.push({
                path: "/search_page",
                query: {key: this.search_keywords, type: this.search_type}
            }).catch(() => {
            })
        },
        to_login() {
            if (this.display_username !== '') {
                this.componentKey = !this.componentKey
                this.$router.push({path: '/login'}).catch(() => {
                })
            } else {
                this.componentKey = !this.componentKey
                this.$router.push({path: '/me'}).catch(() => {
                })
            }

        },
        logout() {
            this.$api
                .get('/physlet_api/logout')
                .then(response => {
                    if (response.data.code !== 200) {
                        this.$notify.error({
                            title: '登出错误',
                            message: response.data.message,
                        });
                    } else {
                        this.$message('注销成功！');
                        this.$router.replace({path: '/portal'});
                        this.authorized = false;
                    }
                })
                .catch()
                .then(() => {
                });

        },
    },
    computed: {
        ClientWidth() {
            return document.body.clientWidth <= 768
        },
        username() {
            if (this.display_username !== '') {
                return this.display_username
            } else {
                return '请登录'
            }
        }
    }
}

</script>

<style scoped>
html {
    max-width: 1980px;
    margin: 0 auto;
    height: 100%;
}

/*#app {*/
/*    height: inherit;*/
/*    width: inherit;*/
/*}*/

.el-container {
    padding: 0;
    margin: 0;
    height: 100%;
}

.fade-enter-active, .fade-leave-active {
    transition: opacity .3s;
}

.fade-enter, .fade-leave-to {
    opacity: 0;
}
</style>
<style lang="scss">
#app {
    min-height: 100vh;
    max-height: 100vh;
}

html, body {
    height: 100%;
}

* {
    &::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    &::-webkit-scrollbar-thumb {
        border-radius: 8px;
        background-color: hsla(225, 4%, 58%, 0.3);
        transition: background-color 0.3s;

        &:hover {
            background: #bbb;
        }
    }

    &::-webkit-scrollbar-track {
        background: #ededed;
    }
}

.el-link.el-link--default {
    text-decoration: none;
}
</style>
