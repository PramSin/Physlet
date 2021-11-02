<template>
    <div style="width: 100%">
        <!--    <el-container>
              <el-aside width="200px">
                <el-tree
                    style="margin-top: 5px;"
                    :loading="loadingTree"
                    :data="tree"
                    :props="defaultProps"
                    :empty-text="loadingTree ? '文件树加载中' : '未获取到文件树'"
                    node-key="id"
                    default-expand-all
                    :indent="8"
                    :highlight-current="true"
                    @node-click="nodeClick"
                >
                            <span slot-scope="{node, data}"
                                  class="tree-node"
                                  :class="{'selected-node': isArticle(node)
                                   && active_article_id === data.entity_id}">
                                <span>
                                    <i :class="getNodeIcon(node)"></i>
                                    <el-tooltip
                                        :content="getTooltipContent(node)"
                                        placement="top"
                                        :open-delay="1000"
                                    >
                                        <span>
                                            {{ data.name }}
                                            <el-tag
                                                v-if="data.owner"
                                                type="info"
                                                size="mini"
                                            >{{ data.owner }}</el-tag>
                                        </span>
                                    </el-tooltip>
                                </span>
                                <span class="pull-right actions">
                                    <el-button-group v-if="canAudit(node)">
                                        <el-tooltip content="创建目录" placement="top" :open-delay="500" :hide-after="5000">
                                            <el-button
                                                v-if="isFolder(node)"
                                                type="text"
                                                size="small"
                                                @click.stop="() => treeAppendFolder(node, data)">
                                                <i class="el-icon-folder-add"></i>
                                            </el-button>
                                        </el-tooltip>
                                        <el-tooltip content="创建文档" placement="top" :open-delay="500" :hide-after="5000">
                                            <el-button
                                                v-if="isFolder(node)"
                                                type="text"
                                                size="small"
                                                @click.stop="() => treeAppend(node, data)">
                                                <i class="el-icon-document-add"></i>
                                            </el-button>
                                        </el-tooltip>
                                        <el-tooltip content="重命名" placement="top" :open-delay="500" :hide-after="5000">
                                            <el-button
                                                type="text"
                                                size="small"
                                                @click.stop="() => treeRename(node, data)">
                                                <i class="el-icon-edit"></i>
                                            </el-button>
                                        </el-tooltip>
                                        <el-tooltip content="删除" placement="top" :open-delay="500" :hide-after="5000">
                                            <el-button
                                                type="text"
                                                size="small"
                                                @click.stop="() => treeRemove(node, data)">
                                                <i class="el-icon-delete"></i>
                                            </el-button>
                                        </el-tooltip>
                                    </el-button-group>
                                </span>
                            </span>
                </el-tree>
              </el-aside>
              <el-container>
                <el-footer class="footer">
                  <span class="footer-item">沪ICP备18033591号-1</span>
                  <span class="footer-item">Copyright @ Arrakis 2020</span>
                </el-footer>
              </el-container>
            </el-container>-->
        <template>
            <el-select v-model="value" placeholder="请选择">
                <el-option
                    v-for="item in synopsis_list"
                    :value="item.value"
                    :label="item.label">
                </el-option>
            </el-select>
        </template>
        <el-upload ref="upload"
                   class="upload-demo"
                   action=""
                   :show-file-list="show_file"
                   :http-request="submitFile"
                   :before-remove="beforeRemove"
                   :file-list="fileList"
                   :on-change="uploadFile">
            <el-button size="small" type="primary">点击上传</el-button>
            <div slot="tip" class="el-upload__tip">不超过500kb</div>
        </el-upload>
    </div>
</template>


<script>
export default {
    name: "Me",
    data() {
        return {
            value: '',
            synopsis_list: [],
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
                console.log(this.synopsis_list)

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
