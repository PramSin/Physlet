<template>
    <el-container>
        <el-aside></el-aside>
        <el-main>
            <h3>我的模拟（点击展示）</h3>
            <el-table
                stripe
                :data="Simulation_list"
                style="width: 100%">
                <el-table-column
                    prop="version_id"
                    v-if=false
                    label="version_id">
                </el-table-column>
                <el-table-column
                    prop="id"
                    v-if=false
                    label="id">
                </el-table-column>
                <el-table-column
                    prop="category_id"
                    v-if=false
                    label="category_id">
                </el-table-column>
                <el-table-column
                    prop="name"
                    label="名称"
                    width="180"
                >
                </el-table-column>
                <el-table-column
                    prop="category.name"
                    label="类别">
                </el-table-column>
                <el-table-column
                    prop="access"
                    label="可见性">
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
                <el-table-column
                    prop="shares"
                    label="分享">
                </el-table-column>
                <el-table-column
                    fixed="right"
                    label="操作"
                    width="100">
                    <template slot-scope="scope">
                        <el-button type="text" size="small" @click="edit_form_visibility = true">编辑</el-button>
                        <el-button type="text" size="small" @click="jump_to_My_simulation">查看</el-button>
                    </template>

                </el-table-column>
            </el-table>
            <el-dialog title="编辑模拟" :visible.sync="edit_form_visibility">
                <el-form :inline="true" :model="edit_form">
                    <el-form-item label="类别">
                        <el-select v-model="edit_form.category" placeholder="类别">
                            <el-option value="热学" label="热学"></el-option>
                            <el-option value="电学" label="电学"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="可见性">
                        <el-select v-model="edit_form.accessibility" placeholder="可见性">
                            <el-option value="0" label="私人"></el-option>
                            <el-option value="1" label="公共"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="submit_edit">查询</el-button>
                    </el-form-item>
                </el-form>
            </el-dialog>
            <br/>
            <h3>上传模拟</h3>
            <el-form label-width="100px" style="width: 40%">
                <el-form-item label="名称" required>
                    <el-input
                        placeholder="请填写模拟名称"
                        v-model="simulation_name"
                        clearable
                    ></el-input>
                </el-form-item>
                <br/>
                <el-form-item label="类别" required>
                    <el-select style="margin-top: 15px" v-model="category" placeholder="请选择模拟类别">
                        <el-option
                            v-for="(item, index) of category_list"
                            :value="item.value"
                            :label="item.label"
                            :key="index">
                        </el-option>
                    </el-select>
                </el-form-item>
                <br/>
                <el-form-item label="简介" required>
                    <el-input
                        placeholder="请填写简介"
                        v-model="synopsis"
                        clearable
                    ></el-input>
                </el-form-item>
                <br/>
                <el-form-item label="可见性" required>
                    <el-select style="margin-top: 15px" v-model="access" placeholder="请选择可见性">
                        <el-option
                            v-for="(access_item, index) of access_list"
                            :value="access_item.value"
                            :label="access_item.label"
                            :key="index">
                        </el-option>
                    </el-select>
                </el-form-item>
                <br/>
                <el-form-item label="文件" required>
                    <el-upload ref="upload"
                               class="upload-demo"
                               action=""
                               :show-file-list="show_file"
                               :before-remove="beforeRemove"
                               :file-list="fileList"
                               :on-change="uploadFile"
                               :auto-upload="false"
                               style="margin-top: 15px">
                        <el-button slot="trigger" size="small" type="primary">选取文件</el-button>
                        <div slot="tip" class="el-upload__tip">必须为zip格式，不超过100mb</div>
                    </el-upload>
                </el-form-item>
                <el-form-item>
                    <el-button size="small" type="success" @click="submitFile">点击上传</el-button>
                </el-form-item>
            </el-form>
        </el-main>
        <el-aside></el-aside>
    </el-container>
</template>


<script>
import moment from 'moment'

export default {
    name: "Me",
    data() {
        return {
            edit_form: {
                category: "",
                accessibility: ""
            },
            edit_form_visibility: false,
            access_list: [
                {
                    value: 0,
                    label: '私人'
                },
                {
                    value: 1,
                    label: '公开'
                },
            ],
            simulation_name: '',
            synopsis: '',
            category: '',
            access: 0,
            category_list: [],
            Simulation_list: [],
            fileList: [{
                name: 'food.jpeg',
                url: 'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100'
            }],
            show_file: false,
            username: '',
            loading: false,
            loadingTree: false,

            defaultProps: {
                label: 'name',
            },

            loggingOut: false,
        };
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
        tree: {
            get() {
                return this.$store.state.tree;
            },
            set(tree) {
                this.$store.commit('setTree', tree);
            },
        },
        status() {
            return this.$store.state.status;
        },
    },
    methods: {
        jump_to_My_simulation(row) {
            this.$router.push({path: "/demo", query: {version: row.version_id}});
        },
        save_change() {
            // console.log(this.$route.query.id);
            let params = {
                //TODO 这里表单要变
                simulation: this.$route.query.id,
                category: this.category_changed,
                access: this.access_changed,
            }
            this.$api
                .post('/physlet_api/editSimulation', params)
                .then(response => {
                    if (response.data.code === 200) {
                        window.alert("成功！")
                        this.$router.replace({path:"/me"})
                    } else {
                        window.alert(response.data.message)
                    }
                })

        },
        edit_Simulation(row) {
            console.log(row);
            this.$router.push({
                path: "/edit_simulation", query: {
                    id: row.id,
                    category: row.category,
                    access: (row.access === '公开') ? 1 : 0,
                    name: row.name,
                }
            });

        },
        dateFormat(row, column) {//todo 加载动画
            let date = row[column.property];
            if (date === undefined) {
                return "";
            }
            return moment(date).format("YYYY-MM-DD HH:mm:ss");
        },
        beforeRemove(file, fileList) {
            return this.$confirm(`确定移除 ${file.name}？`);
        },
        uploadFile(file, fileList) {
            this.show_file = true;
            this.files = file;
            this.fileList = fileList;
            /*console.log(this.Images)*/
        },
        submitFile() {
            const formData = new FormData();
            formData.append('name', this.simulation_name)
            formData.append('category', this.category)
            formData.append('synopsis', this.synopsis)
            formData.append('access', this.access)
            // const file = this.Images
            formData.append('file', this.files.raw);
            const headers = {'Content-Type': 'multipart/form-data;boundary=","'};
            this.$api.post('/physlet_api/uploadSimulation',
                formData,
                {headers},
            ).then((res) => {
                res.data.files; // binary representation of the file
                res.status; // HTTP status
                location.reload()
            });
        },
        handleDropdownCommand(command) {
            switch (command) {
                case 'change_password':
                    this.$router.push({path: '/Changepsw'});
                    break;
                case 'exit':
                    this.logout();
                    break;
            }
        },
    },

    mounted() {
        this.$refs.upload.clearFiles()
        this.$api
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
        this.$api
            .get('/physlet_api/myInfo')
            .then(response => {
                this.username = response.data.data.username
            })
        this.$api
            .get('/physlet_api/getMySimulations')
            .then(response => {
                let data = response.data.data;
                for (let syn = 0; syn < data.length; syn++) {
                    let simulation_list = {};
                    simulation_list.version_id = data[syn].version.id
                    simulation_list.name = data[syn].version.name
                    simulation_list.access = (data[syn].access ? '公开' : '私人')
                    simulation_list.id = data[syn].id
                    simulation_list.category = data[syn].category
                    simulation_list.likes = data[syn].likes
                    simulation_list.category_id = data[syn].category_id
                    simulation_list.created_at = data[syn].created_at
                    simulation_list.shares = data[syn].shares
                    this.Simulation_list.push(simulation_list)
                }
            })
    }

}

</script>

<style>
.header {
    padding: 5px 15px;
    background-color: #f6f6f6;
    line-height: 50px;
}

.saving-state {
    font-size: .8rem;
    margin-right: 10px;
}

.el-main {
    padding: 5px 15px 0;
}

.el-container {
    padding: 0;
    margin: 0;
    height: calc(100% - 60px);
}

.footer {
    margin-bottom: -60px;
    padding: 0 15px;
    background-color: #f6f6f6;
    text-align: right;
    line-height: 60px;
    font-size: 12px;
}

.footer-item {
    margin-left: 5px;
    color: #888;
}

.selected-node {
    background-color: #fadab8;
}

.tree-node {
    width: 100%;
    height: 26px;
    line-height: 26px;
    font-size: 14px;
}

.tree-node .pull-right {
    float: right;
}

.tree-node .actions {
    transition: opacity .3s;
    opacity: 0;
}

.tree-node:hover .actions {
    opacity: 1;
}

</style>
