<template>
  <div>
    <h2 >我们的主页信息啥的，可以展示现有的实验模拟</h2>
    <h3 v-if = "this.$store.state.authorized">登录了显示信息</h3>
  </div>


</template>

<script>
export default {
  name: "Home",



  mounted() {
  }
}



//这里需要展示头像、名字等
</script>

<style scoped>

</style>
