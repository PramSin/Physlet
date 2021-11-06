<template>
    <el-container style="padding: 0; margin: 0">
        <el-aside width=15% style="background-color: rgb(238, 241, 246);">
            <el-header class="list-group-item">
                <el-link :underline='false' @click="to_login"
                         style="font-size: 20px;margin-top: 15px;vertical-align: top;">{{ username }}
                </el-link>
                <el-dropdown v-if="is_authorized" @command='handleCommand' style="margin-top: 20px">
                    <i class="el-icon-setting" style=" margin-right: 10px; font-size: 20px"></i>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item command="to_homepage">查看</el-dropdown-item>
                        <el-dropdown-item command="change_password">修改密码</el-dropdown-item>
                        <el-dropdown-item command="exit">登出</el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </el-header>
            <router-link class="list-group-item" active-class="active" to="/home">Home</router-link>
            <router-link class="list-group-item" active-class="active" to="/me">Me</router-link>
            <router-link class="list-group-item" active-class="active" to="/about">About</router-link>
        </el-aside>

        <el-container>
            <el-main>
                <router-view></router-view>
            </el-main>
            <el-footer style="margin: 0px"></el-footer>
        </el-container>
    </el-container>
</template>

<script>
export default {
    name: 'App',
    data() {
        return {
            display_username: '',
        }
    },
    mounted() {
        this.$api
            .get('/physlet_api/userInfo')
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
                        this.$router.replace({path: '/home'}).catch(() => {
                        })
                        /*console.log('2222222222222222')*/
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
        is_authorized() {
            return this.display_username !== '';
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
</style>
