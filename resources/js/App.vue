<template>
    <el-container style="padding: 0; margin: 0">
        <template>
            <!--   电脑客户端情况  -->
            <el-header>
                <el-menu :default-active="current_path()" :router="jumpto" class="el-menu-demo" mode="horizontal">
                    <el-menu-item index="/portal">
                        主页
                    </el-menu-item>
                   <el-tooltip class="item" effect="dark" content="请先登录才能上传模拟" placement="bottom" v-if="!is_authorized">
                       <el-menu-item index="/me" :disabled="!is_authorized">
                           我的模拟
                       </el-menu-item>
                   </el-tooltip>
                    <el-menu-item index="/about">
                        关于我们
                    </el-menu-item>
                    <el-menu-item style="margin: 0px" index="/search">
                        <template>
                            <el-input placeholder="搜索" v-model="search" clearable
                                      style="padding-bottom: 7px"></el-input>
                        </template>
                    </el-menu-item>
                    <el-submenu index="user_information" style="float: right" v-if="is_authorized">
                        <template slot="title">
                            <el-avatar style="margin-right: 0px"></el-avatar>
                        </template>
                        <el-menu-item index="/change_password">修改密码</el-menu-item>
                        <el-menu-item @click="logout">登出</el-menu-item>
                    </el-submenu>
                    <el-menu-item index="unauthorized" style="float: right" v-else>
                        <el-link type="primary" :underline=false style="font-size: large">请登录</el-link>
                    </el-menu-item>
                </el-menu>
            </el-header>
            <el-container>
                <el-aside style="width: 150px"></el-aside>
                <el-main>
                    <router-view></router-view>
                </el-main>
                <el-aside style="width: 150px"></el-aside>
            </el-container>
            <el-footer style="margin: 0px"></el-footer>
        </template>
    </el-container>
</template>

<script>
export default {
    name: 'App',
    data() {
        return {
            jumpto: true,
            search: '',
            display_username: '',
            window_width: window.innerWidth,
        }
    },

    mounted() {
        this.$api
            .get('/physlet_api/myInfo')
            .then(response => {
                this.display_username = response.data.data.username
            })
    },
    methods: {
        to_login() {
            if (this.display_username !== '') {
                this.$router.push({path: '/login'})
            } else {
                this.$router.push({path: '/me'})
            }

        },
        handleCommand(command) {
            switch (command) {
                case 'change_password':
                    this.$router.push({path: "/changepsw"});
                    break;
                case 'exit':
                    this.logout();
                    break;
                case 'to_homepage':
                    this.$router.push({path: "/me"})
            }
        },
        current_path() {
            return this.$route.path
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
                        location.reload()
                        this.$message('注销成功！');
                    }
                })
                .catch()
                .then(() => {
                });

        },
    },
    computed: {
        //todo 加载的动画
        //todo async
        //todo 手机界面适应
        top_blank_width() {
            return this.window_width - 605
        },
        is_authorized() {
            this.$api
                .get('/physlet_api/checkLogin')
                .then(response => {
                        return response.data.code === 200;
                    }
                )
        },
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
