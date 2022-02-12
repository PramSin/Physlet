<template>
    <el-container>
        <el-aside>
            <el-card style="margin-top: 50px">
                <div slot="header">筛选器</div>
                <el-select v-model="rank_method" placeholder="排序方式">
                    <el-option
                        v-for="rank_list in rank"
                        :key="rank_list.method"
                        :label="rank_list.label"
                        :value="rank_list.method"></el-option>
                </el-select>
                <el-select multiple v-model="rank_tag" placeholder="按标签筛选" style="margin-top: 20px">
                    <el-option
                        v-for="tag in tag_list"
                        :key="tag.tag"
                        :label="tag.label"
                        :value="tag.tag"></el-option>
                </el-select>
            </el-card>
        </el-aside>
        <el-main>
            <h2>我们的主页信息啥的，可以展示现有的实验模拟</h2>
            <h3>模拟展示（点击查看详情）</h3>
            <el-card>
                <el-table
                    @row-click="jump_to_simulation"
                    stripe
                    :data="All_simulation_list"
                    style="width: 100%">
                    <el-table-column
                        prop="name"
                        label="名称"
                        width="180">
                    </el-table-column>

                    <el-table-column
                        :formatter="dateFormat"
                        prop="created_at"
                        label="创建时间">
                    </el-table-column>
                    <el-table-column
                        prop="likes"
                        label="赞"
                        width="50">
                    </el-table-column>

                </el-table>
            </el-card>
            <el-card style="border-radius: 15px">
                <div slot="header">
                    <div>
                        <i class="el-icon-data-analysis" style="margin-right: 5px; font-size: 15px"></i>
                        <el-tag size="small">标签一</el-tag>
                        <el-tag size="small">标签二</el-tag>
                        <el-tag size="small">标签三</el-tag>
                    </div>
                    <h3 style="margin-right: 3px; width: 80%">作者名字/{{ this.All_simulation_list[1].name }}</h3>
                </div>
                <span>这里是简介</span>
                <br/>
                <span
                    style="font-size: small; color: gray">创建时间 {{
                        this.All_simulation_list[1].created_at.slice(0, 10)
                    }}</span>
                <div style="float: right">
                    <span>likes {{ this.All_simulation_list[1].likes }}</span>
                </div>

            </el-card>

        </el-main>
        <el-aside>
            <el-card style="margin-top: 50px" v-if="!is_authorized">
                <div slot="header" style="text-align: center">
                    <el-avatar :size="125"></el-avatar>
                    <div style="text-align:center; font-size: 20px">name</div>
                </div>
                <div style="text-align: center">
                    我的模拟们
                </div>
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
            rank_method: "",
            rank_tag: "",
            loading_simulations: false,
            All_simulation_list: [],
            tag_list: [
                {
                    tag: "热学",
                    label: "rexue",
                },
                {
                    tag: "电学",
                    label: "dianxue",
                }
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
        is_authorized() {
            this.$api
                .get('/physlet_api/checkLogin')
                .then(response => {
                        return response.data.code === 200;
                    }
                )
        },
        jump_to_simulation(row) {
            this.$router.push({path: "/demo", query: {version: row.version_id}});
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
            .get('/physlet_api/getSims')
            .then(response => {
                this.loading_simulations = true
                let data = response.data.data;
                for (let syn = 0; syn < data.length; syn++) {
                    let simulation_list = {};
                    simulation_list.simulation_id = data[syn].sid
                    simulation_list.simulation_name = data[syn].sname
                    simulation_list.creator_name = data[syn].cname
                    simulation_list.creator_id = data[syn].cid
                    simulation_list.synopsis = data[syn].synopsis
                    simulation_list.likes = data[syn].likes
                    simulation_list.uname = data[syn].uname
                    simulation_list.url = data[syn].url
                    simulation_list.create_time = data[syn].create_time
                    this.All_simulation_list.push(simulation_list)
                }
               this.loading_simulations = false
            })
    }
}


//这里需要展示头像、名字等
</script>

<style scoped>

</style>
