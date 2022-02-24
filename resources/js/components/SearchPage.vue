<template>
    <el-container>
        <el-aside></el-aside>
        <el-main>
            <h2>搜索 (共{{ total_search_amount }}个结果)</h2>
            <h3>模拟展示（点击查看详情）</h3>
            <el-card style="border-radius: 15px"
                     v-loading="loading_simulations"
                     v-if="!loading_simulations"
                     v-for="simulation in search_simulation_list"
                     :key="simulation.simulation_id">
                <div slot="header">
                    <div>
                        <i class="el-icon-data-analysis" style="margin-right: 5px; font-size: 15px"></i>
                        <el-tag size="small">{{ simulation.catagory_name }}</el-tag>
                    </div>
                    <h3 style="margin-right: 3px; width: 80%" v-if="!loading_simulations">{{ simulation.user_name }}
                        /
                        {{ simulation.simulation_name }}</h3>
                </div>
                <span v-if="!loading_simulations">{{ simulation.synopsis }}</span>
                <br/>
                <span
                    style="font-size: small; color: gray"
                    v-if="!loading_simulations">创建时间 {{ simulation.create_time.slice(0, 10) }}</span>
                <div style="float: right">
                    <span v-if="!loading_simulations">likes {{ simulation.likes }}</span>
                </div>
            </el-card>
            <el-pagination
                v-if="!loading_simulations"
                style="display:table; margin:0 auto; "
                @current-change="current_change"
                :current-page="current_page"
                layout="prev, pager, next"
                :hide-on-single-page="true"
                :pager-count="11"
                :page-size="10"
                :total="total_search_amount">
            </el-pagination>
            <elcard v-if="!loading_users"
                    v-for="user in search_user_list"
                    :key="user.user_id"
                    @click="jump_to_user_page(user)"
                    style="cursor: pointer">
                <div slot="header">
                    <el-avatar :src="user.avatar_url"></el-avatar>
                    <h3>{{ user.user_name }}</h3>
                </div>
                <div>
                    <span>共 {{ user.sims }} 个模拟</span>
                    <span style="float: right"><i class="fa-regular fa-thumbs-up"></i> {{ user.likes }}</span>
                </div>
            </elcard>
        </el-main>
        <el-aside>
            <el-card v-if="authorized">
                <div slot="header" style="text-align: center">
                    <el-avatar :size="125" :src="avatar_url" v-loading="loading_avatar"></el-avatar>
                    <div style="text-align:center; font-size: 20px" v-if="!loading_avatar">{{ this.display_username }}
                    </div>
                </div>
                <el-link style="display:flex; justify-content:center" :underline="false" v-if="!loading_avatar"
                         @click="jump_to_my_page">
                    共上传了{{ this.number_of_simulations }}个模拟, 点击查看
                </el-link>
            </el-card>
            <el-card style="margin-top: 50px" v-else>
                <div style="text-align: center; font-size: large">
                    登录查看我的模拟
                </div>
            </el-card>
        </el-aside>
    </el-container>


</template>

<script>
import SimulationCard from "./SimulationCard";

export default {
    name: "SearchPage",
    components: {
        SimulationCard,
    },
    data() {
        return {
            current_page: 1,
            total_search_amount: 0,
            authorized: false,
            loading_simulations: true,
            loading_avatar: true,
            avatar_url: "",
            display_username: "",
            number_of_simulations: 0,
            search_simulation_list: [],
            search_keywords: "",
            tag_list: [],
            search_user_list: [],
            loading_users: true,
        }
    },

    methods: {
        jump_to_user_page(user) {
            this.$router.push({path: "/user_page", query: {id: user.user_id}})
        },
        current_change(current) {
            this.search_simulation_list.splice(0, this.search_simulation_list.length)
            this.loading_simulations = true
            this.$api
                .post('/physlet_api/search', {key: this.search_keywords, opt: current - 1})
                .then(response => {
                    let data = response.data.data;
                    for (let syn = 0; syn < data.length; syn++) {
                        let simulation_list = {};
                        simulation_list.simulation_id = data[syn].sid
                        simulation_list.simulation_name = data[syn].sname
                        simulation_list.catagory_name = data[syn].cname
                        simulation_list.catagory_id = data[syn].cid
                        simulation_list.synopsis = data[syn].synopsis
                        simulation_list.likes = data[syn].likes
                        simulation_list.user_name = data[syn].uname
                        simulation_list.url = data[syn].url
                        simulation_list.create_time = data[syn].create_time
                        this.search_simulation_list.push(simulation_list)
                    }
                    this.loading_simulations = false
                })
        },
        jump_to_my_page() {
            this.$router.push({path: "/me"})
        },
        jump_to_simulation(simulation) {
            this.$router.push({path: "/demo", query: {sid: simulation.simulation_id}});
        },
    },
    mounted() {
        this.search_keywords = this.$route.query.key
        if (this.$route.query.type !== "2") {
            this.$api
                .post('/physlet_api/search', {key: this.search_keywords})
                .then(response => {
                    let data = response.data.data;
                    for (let syn = 0; syn < data.length; syn++) {
                        let simulation_list = {};
                        simulation_list.simulation_id = data[syn].sid
                        simulation_list.simulation_name = data[syn].sname
                        simulation_list.catagory_name = data[syn].cname
                        simulation_list.catagory_id = data[syn].cid
                        simulation_list.synopsis = data[syn].synopsis
                        simulation_list.likes = data[syn].likes
                        simulation_list.user_name = data[syn].uname
                        simulation_list.url = data[syn].url
                        simulation_list.create_time = data[syn].create_time
                        this.search_simulation_list.push(simulation_list)
                    }
                    this.total_search_amount = response.data.number
                    this.loading_simulations = false
                })
        } else {
            this.$api
                .post('/physlet_api/searchUser', {key: this.search_keywords})
                .then(response => {
                    let data = response.data.data;
                    for (let syn = 0; syn < data.length; syn++) {
                        let user_list = {};
                        user_list.user_id = data[syn].uid
                        user_list.user_name = data[syn].uname
                        user_list.avatar_url = data[syn].avatar
                        user_list.sims = data[syn].likes
                        this.search_user_list.push(user_list)
                    }
                    this.total_search_amount = response.data.number
                    this.loading_users = false
                })
        }
        this.$api
            .get('/physlet_api/checkLogin')
            .then(response => {
                    if (response.data.code === 200) {
                        this.authorized = true
                        this.$api
                            .get('/physlet_api/myInfo')
                            .then(response => {
                                this.avatar_url = response.data.data.avatar
                                this.display_username = response.data.data.uname
                                this.number_of_simulations = response.data.data.sims
                                this.loading_avatar = false
                            })
                    }
                }
            )
    }
}


//这里需要展示头像、名字等
</script>

<style scoped>

</style>
