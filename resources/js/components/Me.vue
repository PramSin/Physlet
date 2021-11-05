<template>
    <div style="width: 100%">
        <h3>我的模拟（点击展示）</h3>
        <el-table
            @row-click="jump_to_My_simulation"
            stripe
            :data="Simulation_list"
            style="width: 100%">
            <el-table-column
                prop="version_id"
                v-if=false
                label="version_id">
            </el-table-column>
            <el-table-column
                prop="category_id"
                v-if=false
                label="category_id">
            </el-table-column>
            <el-table-column
                prop="name"
                label="名称"
                width="180">
            </el-table-column>
            <el-table-column
                prop="category"
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
                    <el-button type="text" size="small" @click="edit_Simulation(scope.row)">编辑</el-button>
                </template>

            </el-table-column>
        </el-table>
        <br/>
        <h3>上传模拟</h3>
        <el-select style="margin-top: 15px" v-model="value" placeholder="请选择模拟类别">
            <el-option
                v-for="item of synopsis_list"
                :value="item.value"
                :label="item.label">
            </el-option>
        </el-select>
        <br/>
        <el-upload ref="upload"
                   class="upload-demo"
                   action=""
                   :show-file-list="show_file"
                   :http-request="submitFile"
                   :before-remove="beforeRemove"
                   :file-list="fileList"
                   :on-change="uploadFile"
                   style="margin-top: 15px">
            <el-button size="small" type="primary">点击上传</el-button>
            <div slot="tip" class="el-upload__tip">必须为zip格式，不超过100mb</div>
        </el-upload>
    </div>
</template>


<script>
import moment from 'moment'

export default {
    name: "Me",
    data() {
        return {
            value: '',
            synopsis_list: [],
            Simulation_list: [],
            fileList: [{
                name: 'food.jpeg',
                url: 'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100'
            }],
            show_file: false,
            username: localStorage.getItem('username'),
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
        active_article_id: {
            get() {
                return this.$store.state.active_article_id;
            },
            set(value) {
                this.$store.commit('setActiveArticleId', value);
            },
        },
    },
    methods: {
        jump_to_My_simulation(row) {
            this.$router.push({path: "/demo", query: {version: row.version_id}});
        },
        edit_Simulation(row) {
            /*            console.log(row)*/
            this.$router.push({
                path: "/edit_simulation", query: {
                    id: row.id,
                    category: row.category,
                    access: row.access,
                    name: row.name,
                }
            });

        },
        dateFormat(row, column) {
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
            this.Images = file;
            this.fileList = fileList;
            /*console.log(this.Images)*/
        },
        submitFile(event) {
            const formData = new FormData();
            // const name = localStorage.getItem('username')
            formData.append('name', localStorage.getItem('username'))
            formData.append('category', this.value)
            formData.append('synopsis', 'sadkfuha;sdifh')
            formData.append('access', '0')
            // const file = this.Images
            formData.append('file', this.Images.raw);
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
        if (localStorage.getItem('is_authorized') !== 'true') {
            this.$router.replace({
                path: "/login",
            });
        } else {
            this.$api
                .get('/physlet_api/getCategories')
                .then(response => {
                    let data = response.data.data;
                    for (let syn = 0; syn < data.length; syn++) {
                        let synopsis = {};
                        synopsis.label = data[syn].name
                        synopsis.value = data[syn].id
                        this.synopsis_list.push(synopsis)
                    }
                })
            this.$api
                .get('/physlet_api/getMySimulations')
                .then(response => {
                    let data = response.data.data;
                    for (let syn = 0; syn < data.length; syn++) {
                        let simulation_list = {};
                        simulation_list.version_id = data[syn].version.id
                        simulation_list.name = data[syn].version.name
                        simulation_list.access = (data[syn].access ? 'public' : 'private')
                        simulation_list.category = data[syn].category.name
                        /*                        console.log(data[syn].version.name)*/
                        simulation_list.likes = data[syn].likes
                        simulation_list.category_id = data[syn].category_id
                        simulation_list.created_at = data[syn].created_at
                        simulation_list.shares = data[syn].shares
                        this.Simulation_list.push(simulation_list)
                    }
                })
        }
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
