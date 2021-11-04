<template>
    <div>
        <h2>我们的主页信息啥的，可以展示现有的实验模拟</h2>
        <h3>模拟展示</h3>
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
                prop="version"
                label="版本"
                width="180">
            </el-table-column>
            <el-table-column
                prop="likes"
                label="赞">
            </el-table-column>
            <el-table-column
                :formatter="dateFormat"
                prop="created_at"
                label="创建时间">
            </el-table-column>

        </el-table>
    </div>


</template>

<script>
import moment from 'moment'

export default {
    name: "Home",
    data() {
        return {
            All_simulation_list: [],
        }
    },

    methods: {
        jump_to_simulation(row) {
            this.$router.push({path: "/demo",query:{version: row.version}});

        },
        dateFormat(row, column) {
            let date = row[column.property];
            if (date === undefined) {
                return "";
            }
            return moment(date).format("YYYY-MM-DD HH:mm:ss");
        }
        ,
    },

    mounted() {
        this.axios
            .get('/physlet_api/getSimulations')
            .then(response => {
                let data = response.data.data;
                for (let syn = 0; syn < data.length; syn++) {
                    let simulation_list = {};
                    simulation_list.name = data[syn].name
                    simulation_list.likes = data[syn].likes
                    simulation_list.version = data[syn].version
                    simulation_list.created_at = data[syn].created_at
                    this.All_simulation_list.push(simulation_list)
                }
// TODO 模拟缩略图

            })
    }
}


//这里需要展示头像、名字等
</script>

<style scoped>

</style>
