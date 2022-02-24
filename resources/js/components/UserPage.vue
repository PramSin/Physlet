<template>
    <el-container>
        <el-aside></el-aside>
        <el-main>
            <h2>他/她的模拟</h2>
            <h3>模拟展示（点击查看详情）</h3>
            <div>
                <el-card style="border-radius: 15px" v-loading="loading_simulations"
                         v-for="simulation in user_simulation_list"
                         :key="simulation.simulation_id">
                    <div slot="header">
                        <div>
                            <i class="el-icon-data-analysis" style="margin-right: 5px; font-size: 15px"></i>
                            <el-tag size="small">{{ simulation.catagory_name }}</el-tag>
                        </div>
                        <el-button type="text" v-if="!loading_simulations" @click.stop="jump_to_simulation(simulation)"
                                   style="font-size: 23px;margin-left: 0">{{ simulation.simulation_name }}
                        </el-button>
                        <br/>
                        <span v-if="!loading_simulations">{{ simulation.synopsis }}</span>
                    </div>
                    <span style="font-size: small; color: gray" v-if="!loading_simulations">
                       创建时间 {{ simulation.create_time.slice(0, 10) }}</span>
                    <div style="float: right">
                        <span v-if="!loading_simulations">likes {{ simulation.likes }}</span>
                    </div>
                </el-card>
            </div>
            <el-pagination>
                style="display:table; margin:0 auto; "
                @current-change="current_change"
                :current-page="current_page"
                layout="prev, pager, next"
                :hide-on-single-page="true"
                :pager-count="11"
                :page-size="10"
                :total="total_simulation_amount">
            </el-pagination>
        </el-main>
        <el-aside>
            <el-card>
                <div slot="header" style="text-align: center">
                    <el-avatar :size="125" :src="avatar_url" v-loading="loading_avatar"></el-avatar>
                    <div style="text-align:center; font-size: 20px" v-if="!loading_avatar">{{
                            this.display_username
                        }}
                    </div>
                </div>
                <span v-if="!loading_avatar" style="display:flex; justify-content:center">
                    共上传了{{ this.total_simulation_amount }}个模拟
                </span>
                <el-button style="display:table; margin: 20px auto 0;" icon="el-icon-star-on" v-if="authorized"
                           :disabled="followed">
                    {{ this.follow_word }}
                </el-button>
            </el-card>
        </el-aside>
    </el-container>


</template>

<script>
import moment from 'moment'
import SimulationCard from "./SimulationCard";

export default {
    name: "Portal",
    components: {
        SimulationCard,
    },
    data() {
        return {
            follow_word: "关注",
            current_page: 1,
            followed: true,
            total_simulation_amount: 0,
            clientHeight: "",
            authorized: false,
            rank_method: "",
            rank_tag: "",
            loading_simulations: true,
            loading_avatar: true,
            avatar_url: "",
            display_username: "",
            loading_rank_category: true,
            number_of_simulations: 0,
            user_simulation_list: [],
        }
    },

    methods: {
        current_change(current) {
            this.user_simulation_list.splice(0, this.user_simulation_list.length)
            this.loading_simulations = true
            this.$api
                .post('/physlet_api/filter', {cid: this.rank_tag, opt: current - 1})
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
                        this.user_simulation_list.push(simulation_list)
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
        dateFormat(row, column) {
            let date = row[column.property];
            if (date === undefined) {
                return "";
            }
            return moment(date).format("YYYY-MM-DD");
        },
    },
    mounted() {
        this.$api
            .post('/physlet_api/getHisSims', {uid: this.$route.query.id})
            .then(response => {
                if (response.data.code === 200) {
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
                        this.user_simulation_list.push(simulation_list)
                    }
                    this.total_simulation_amount = response.data.number
                    console.log(this.total_simulation_amount)
                    this.loading_simulations = false
                }
            })
        this.$api
            .get('/physlet_api/checkLogin')
            .then(response => {
                    if (response.data.code === 200) {
                        this.authorized = true
                    }
                }
            )
        this.$api
            .post('/physlet_api/userInfo', {uid: this.$route.query.id})
            .then(response => {
                console.log(response)
                this.avatar_url = response.data.data.avatar
                this.display_username = response.data.data.uname
                this.number_of_simulations = response.data.data.sims
                this.number_of_likes = response.data.data.likes
                this.loading_avatar = false
            })
    }
}


//这里需要展示头像、名字等
</script>

<style scoped>

</style>
