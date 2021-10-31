<template>
  <div>
    <div class="row">
      <el-container>
        <el-header style="text-align: right; font-size: 12px">
          <el-dropdown @command='handleCommand'>
            <i class="el-icon-setting" style="margin-right: 15px"></i>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item command="to_homepage">查看</el-dropdown-item>
              <el-dropdown-item command="change_password">修改密码</el-dropdown-item>
              <el-dropdown-item command="exit">登出</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
          <span>{{username}}</span>
        </el-header>
      </el-container>
      <div class="col-xs-offset-2 col-xs-8">
        <div class="page-header"><h2>实验中心（不是）</h2></div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-2 col-xs-offset-2">
        <div class="list-group">
          <!-- 原始html中我们使用a标签实现页面的跳转 -->
          <!-- <a class="list-group-item active" href="./about.html">About</a> -->
          <!-- <a class="list-group-item" href="./home.html">Home</a> -->

          <!-- Vue中借助router-link标签实现路由的切换 -->
          <router-link class="list-group-item" active-class="active" to="/about">About</router-link>
          <router-link class="list-group-item" active-class="active" to="/home">Home</router-link>
          <router-link class="list-group-item" active-class="active" to="/me">Me</router-link>
          <router-link class="list-group-item" active-class="active" to="/login">Login</router-link>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="panel">
          <div class="panel-body">
            <!-- 指定组件的呈现位置 -->
            <router-view></router-view>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: 'App',

  methods: {
    handleCommand(command) {
      switch (command) {
        case 'change_password':
            this.$message('sdklafjhkd');
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
              this.$router.replace({path:'@/components/home'})
              /*console.log('2222222222222222')*/
            }
          })
          .catch()
          .then(() => {
          });

    },
  },
  computed: {
    username:function () {
      if (localStorage.getItem('username')) {
        return localStorage.getItem('username')
      }
      else {
        return '请登录'
      }
    }
  }


}

</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
