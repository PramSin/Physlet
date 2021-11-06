<template>
    <div>
        <h3>展示区</h3>
        <iframe style="height:100vmin; width:100%;" :src="demo_src" ref="iframe"></iframe>
        <div style="margin: 20px 0;"></div>
        <h4>评分</h4>
        <el-rate
            :value="rate_to_post"
            :colors="colors"
            :disabled='disable_rate'
            @change="post_rate">
        </el-rate>
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
            comment_to_post: '',
            comments: [],
            disable_rate: false,
            rate_to_post: null,
            colors: ['#99A9BF', '#F7BA2A', '#FF9900'],
            demo_src: 'https://www.openstreetmap.org/export/embed.html?bbox=-0.004017949104309083%2C51.47612752641776%2C0.00030577182769775396%2C51.478569861898606&layer=mapnik',
        }
    },
    methods: {
        post_rate(value) {
            if (value >= 3) {

            }
            this.disable_rate = true
        },
        post_comment() {//todo 发表评论区
        },
    },
    mounted() {
        /*        console.log(this.$route.query)*/
        let params = this.$route.query
        this.$api
            .get('/physlet_api/getPackage', {params})
            .then(response => {
                let data = response.data.data;
                this.demo_src = data
            })


    }
}
</script>

<style scoped>

</style>
