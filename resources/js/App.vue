<template>
        <el-container style="padding: 0px; margin: 0px; height: 100vmin">
            <el-aside width="200px" style="background-color: rgb(238, 241, 246);">
                <router-link class="list-group-item" active-class="active" to="/about">About</router-link>
                <router-link class="list-group-item" active-class="active" to="/home">Home</router-link>
                <router-link class="list-group-item" active-class="active" to="/me">Me</router-link>
                <router-link class="list-group-item" active-class="active" to="/login">Login</router-link>
            </el-aside>

            <el-container>
                <el-header style="text-align: right; font-size: 20px">
<!--                    <h2>实验中心（不是）</h2>-->
                    <el-dropdown @command='handleCommand'>
                        <i class="el-icon-setting" style="margin-right: 15px; font-size: 20px"></i>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item command="to_homepage">查看</el-dropdown-item>
                            <el-dropdown-item command="change_password">修改密码</el-dropdown-item>
                            <el-dropdown-item command="exit">登出</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                    <span>{{ username }}</span>
                </el-header>
                <el-main>
                    <router-view></router-view>
                </el-main>
                <el-footer style="margin: 0px">1111111</el-footer>
            </el-container>
        </el-container>
</template>

<script>

export default {
    name: 'App',

    methods: {
        handleCommand(command) {
            if (localStorage.getItem('is_authorized') === false) {
                this.$router.push({path: "/login"})
            } else {

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
            }
        },
        logout() {
            this.$http
                .get('/physlet_api/logout')
                .then(response => {
                    if (response.data.code !== 200) {
                        this.$notify.error({
                            title: '登出错误',
                            message: response.data.message,
                        });
                    } else {
                        localStorage.clear()
                        console.log('111111111')
                        this.$message('注销成功！');
                        this.$router.replace({path: '@/components/home'})
                        /*console.log('2222222222222222')*/
                    }
                })
                .catch()
                .then(() => {
                });

        },
    },
    computed: {
        username: function () {
            if (localStorage.getItem('username')) {
                return localStorage.getItem('username')
            } else {
                return '请登录'
            }
        }
    }
}

</script>

<style>
html,
body {
    padding: 0px;
    margin: 0px;
    height: 100%;

}

#App {
    font-family: Avenir, Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
    margin: 10px;
    height: 100%;

}

.el-header {
    background-color: #B3C0D1;
    color: #333;
    line-height: 60px;
}

.el-aside {
    color: #333;
}

.el-container.is-vertical {
    overflow: auto;
}
.background-wrapper {
    overflow: hidden;
}

.background {
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, .8);
    background-position: center;
    background-size: cover;
    background-repeat: no-repeat;
    filter: blur(5px) brightness(50%);
    transform: scale(1.1);
}
</style>
