<template>
    <div>
        <h2>编辑我的模拟</h2>
        <el-table
            stripe
            :data="simulation_to_edit"
            style="width: 100%">
            <el-table-column
                prop="simulation_name"
                label="名称">
            </el-table-column>
            <el-table-column
                prop="simulation_category"
                label="类别">
            </el-table-column>
            <el-table-column
                prop="simulation_access"
                label="可见性">
            </el-table-column>
        </el-table>

        <el-select style="margin-top: 15px" v-model="category_changed" placeholder="请选择类别">
            <el-option
                v-for="item of category_list"
                :value="item.value"
                :label="item.label">
            </el-option>
        </el-select>
        <el-select style="margin-top: 15px" v-model="access_changed" placeholder="请选择可见性">
            <el-option
                v-for="access_item of access_list"
                :value="access_item.value"
                :label="access_item.label">
            </el-option>
        </el-select>

        <el-button type="primary" native-type="submit" @click="save_change">修改</el-button>
    </div>
</template>

<script>
export default {
    name: "Edit_simulation",
    data() {
        return {
            access_list: [
                {
                    value: 1,
                    label: 'public'
                },
                {
                    value: 1,
                    label: 'private'
                },
            ],
            access_changed: this.$route.query.access,
            category_changed: this.$route.query.category,
            category_list: [],
            simulation_to_edit: [{
                simulation_name: this.$route.query.name,
                simulation_category: this.$route.query.category,
                simulation_access: this.$route.query.access
            }],
        }
    },
    methods: {
        save_change() {
            console.log(this.simulation_id)
            let params = {
                simulation_id: this.$route.query.id,
                category: this.category_changed,
                access: this.access_changed,
            }
            this.axios
                .post('/physlet_api/editSimulation', {params})
                .then(response => {
                    if (response.data.code === 200) {
                        window.alert("成功！")
                        this.$router.replace({path:'/me'})
                    }
                    else {
                        window.alert(response.data.message)
                    }
                })
        }
    },
    mounted() {
        this.axios
            .get('/physlet_api/getCategories')
            .then(response => {
                let data = response.data.data;
                for (let syn = 0; syn < data.length; syn++) {
                    let category = {};
                    category.label = data[syn].name
                    category.value = data[syn].id
                    this.category_list.push(category)
                }
            })
    }
}
</script>

<style scoped>

</style>
