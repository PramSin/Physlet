<template>
    <el-container>
        <el-aside>
            <el-card>
                <div slot="header">筛选器</div>
                <!--                <el-select v-model="rank_method" placeholder="排序方式">
                                    <el-option
                                        v-for="rank_list in rank"
                                        :key="rank_list.method"
                                        :label="rank_list.label"
                                        :value="rank_list.method"></el-option>
                                </el-select>-->
                <el-select v-model="rank_tag" placeholder="按标签筛选" style="margin-top: 20px">
                    <el-option
                        v-for="category in rank_category"
                        :key="category.rank_category_id"
                        :label="category.rank_category_name"
                        :value="category.rank_category_id"></el-option>
                </el-select>
                <el-button style="margin-left: 15px" type="primary" icon="el-icon-check" @click="submit_rank" circle></el-button>
            </el-card>
        </el-aside>
        <el-main>
            <h2>我们的主页信息啥的，可以展示现有的实验模拟</h2>
            <h3>模拟展示（点击查看详情）</h3>
            <div>
                <el-card style="border-radius: 15px" v-loading="loading_simulations"
                         v-for="simulation in All_simulation_list"
                         @click.native="jump_to_simulation(simulation)">
                    <div slot="header">
                        <div>
                            <i class="el-icon-data-analysis" style="margin-right: 5px; font-size: 15px"></i>
                            <el-tag size="small">{{ simulation.catagory_name }}</el-tag>
                        </div>
                        <el-button type="text" @click.stop="jump_to_user_page(simulation)" style="font-size: 23px">{{ simulation.user_name }}</el-button>
                        <el-button type="text" @click.stop="jump_to_simulation(simulation)" style="font-size: 23px;margin-left: 0">/ {{ simulation.simulation_name }}</el-button>
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
            </div>
            <el-pagination
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
import moment from 'moment'
import SimulationCard from "./SimulationCard";

export default {
    name: "Portal",
    components: {
        SimulationCard,
    },
    data() {
        return {
            current_page: 1,
            total_simulation_amount: 0,
            clientHeight: "",
            authorized: false,
            rank_method: "",
            rank_tag: "",
            loading_simulations: true,
            loading_avatar: true,
            avatar_url: "",
            rank_category: [],
            display_username: "",
            loading_rank_category: true,
            number_of_simulations: 0,
            All_simulation_list: [],
            tag_list: [
            ],
            rank: [{
                method: "time_up",
                label: "按时间升序"
            }, {
                method: "time_down",
                label: "按时间降序"
            }, {
                method: "like_up",
                label: "按喜欢升序"
            }, {
                method: "like_down",
                label: "按喜欢降序"
            }

            ],
        }
    },

    methods: {
        jump_to_user_page(simulation) {
            this.$router.push({path: "/user_page", query: {id: simulation.user_id}})
        },
        submit_rank() {
            this.All_simulation_list.splice(0, this.All_simulation_list.length)
            this.loading_simulations = true
            this.$api
                .post('/physlet_api/filter', {cid: this.rank_tag})
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
                        this.All_simulation_list.push(simulation_list)
                    }
                    this.loading_simulations = false
                })
        },
        current_change(current) {
            this.All_simulation_list.splice(0, this.All_simulation_list.length)
            this.loading_simulations = true
            if (this.rank_tag !== "") {
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
                            simulation_list.user_id = data[syn].uid
                            simulation_list.user_name = data[syn].uname
                            simulation_list.url = data[syn].url
                            simulation_list.create_time = data[syn].create_time
                            this.All_simulation_list.push(simulation_list)
                        }
                        this.loading_simulations = false
                    })
            }
            else {
                this.$api
                    .post('/physlet_api/getSims', {opt: current - 1})
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
                            simulation_list.user_id = data[syn].uid
                            simulation_list.user_name = data[syn].uname
                            simulation_list.url = data[syn].url
                            simulation_list.create_time = data[syn].create_time
                            this.All_simulation_list.push(simulation_list)
                        }
                        this.loading_simulations = false
                    })
            }
        },
        fetch_simulation() {
            window.alert(11111)
        },
        jump_to_my_page() {
            this.$router.push({path: "/me"})
        },
        jump_to_simulation(simulation) {
            this.$router.push({path: "/demo", query: {sid: simulation.simulation_id}})
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
            .post('/physlet_api/getSims')
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
                        simulation_list.user_id = data[syn].uid
                        simulation_list.user_name = data[syn].uname
                        simulation_list.url = data[syn].url
                        simulation_list.create_time = data[syn].create_time
                        this.All_simulation_list.push(simulation_list)
                    }
                    this.total_simulation_amount = response.data.number
                    this.loading_simulations = false
                }
            })
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
        this.$api
            .get('/physlet_api/getCats')
            .then(response => {
                let data = response.data.data;
                for (let syn = 0; syn < data.length; syn++) {
                    let category = {};
                    category.rank_category_name = data[syn].cname
                    category.rank_category_id = data[syn].cid
                    this.rank_category.push(category)
                }
            })
    }
}


//这里需要展示头像、名字等
</script>

<style scoped>

</style>
