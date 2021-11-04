<template>
    <div style="width: 100%">
        <h3>我的模拟</h3>
        <el-table
            stripe
            :data="Simulation_list"
            style="width: 100%">
            <el-table-column
                prop="id"
                label="id"
                width="180">
            </el-table-column>
            <el-table-column
                prop="version"
                label="名称/版本"
                width="180">
            </el-table-column>
            <el-table-column
                prop="access"
                label="可见性">
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
            <el-table-column
                prop="shares"
                label="分享">
            </el-table-column>
        </el-table>
        <br/>
        <h3>上传模拟</h3>
        <el-select style="margin-top: 15px" v-model="value" placeholder="请选择">
            <el-option
                v-for="item in synopsis_list"
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
            console.log(this.Images)
            formData.append('file', this.Images.raw);
            const headers = {'Content-Type': 'multipart/form-data;boundary=","'};
            this.axios.post('/physlet_api/uploadSimulation',
                formData,
                {headers},
            ).then((res) => {
                res.data.files; // binary representation of the file
                res.status; // HTTP status
            });
        },


        isFolder(node) {
            return (node.key && node.key.startsWith('F_'));
        },
        isShareFolder(node) {
            return (node.key && node.key === 'ShareFolder');
        },
        isArticle(node) {
            return (node.key && node.key.startsWith('A_'));
        },
        canEdit(node) {
            return node.data.editable === undefined || node.data.editable !== false;
        },
        canAudit(node) {
            return node.data.auditable === undefined || node.data.auditable !== false;
        },
        getNodeIcon(node) {
            if (this.isArticle(node) && this.canEdit(node)) {
                return 'el-icon-document';
            } else if (this.isArticle(node) && !this.canEdit(node)) {
                return 'el-icon-view';
            } else if (this.isShareFolder(node)) {
                return 'el-icon-share';
            } else if (this.isFolder(node) && node.expanded) {
                return 'el-icon-folder-opened';
            } else if (this.isFolder(node) && !node.expanded) {
                return 'el-icon-folder';
            } else {
                return 'el-icon-folder';
            }
        },
        getTooltipContent(node) {
            return node.data.name
                + (node.data.owner ? '@' + node.data.owner : '')
                + (this.canEdit(node) ? '' : '[只读]');
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
        logout() {
            this.$http
                .get('/physlet_api/logout')
                .then(response => {
                    if (response.data.code !== 200) {
                        this.$notify.error({
                            title: '登出错误',
                            message: response.data.message,
                        });
                    } else {
                        localStorage.clear()
                        console.log('111111111')
                        this.$message('注销成功！');
                        this.$router.replace({path: '/home'})
                        /*console.log('2222222222222222')*/
                    }
                })
                .catch()
                .then(() => {
                });

        },
    },
    loadTree() {
        this.loadingTree = true;
        this.$store.dispatch('loadTree')
            .catch()
            .then(() => {
                this.loadingTree = false;
            });
    },
    mounted() {
        this.$refs.upload.clearFiles()
        if (localStorage.getItem('is_authorized') !== 'true') {
            this.$router.replace({
                path: "/login",
            });
        }
        this.axios
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

        this.axios
            .get('/physlet_api/getSimulations')
            .then(response => {
                let data = response.data.data;
                console.log(data)
                for (let syn = 0; syn < data.length; syn++) {
                    let simulation_list = {};
                    simulation_list.id = data[syn].id
                    simulation_list.version = data[syn].version
                    simulation_list.access = data[syn].access
                    simulation_list.likes = data[syn].likes
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
