<template>
    <div style="width: 100%">
        <el-header class="header">
            <el-row>
                <el-col :span="12" style="text-align:right;">
                    <span class="saving-state" v-if="active_article_id"
                          :style="{color: status.saving ? '#E6A23C' : '#67C23A'}">
                        <i :class="status.saving ? 'el-icon-loading' : 'el-icon-circle-check'"></i>
                        {{ status.saving ? '保存中' : '已保存' }}
                    </span>
                    <span class="saving-state" v-if="active_article_id && status.updater_name && status.updated_at"
                          style="color: #888">
                        {{ status.updater_name }} @ {{ $dayjs(status.updated_at).format('MM月DD日 HH:mm:ss') }}
                    </span>
                    <el-dropdown @command="handleDropdownCommand">
                        <el-button type="primary" size="medium" plain>
                            {{ user.name || 'N/A' }}<i class="el-icon-arrow-down el-icon--right"></i>
                        </el-button>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item command="change_password">修改密码</el-dropdown-item>
                            <el-dropdown-item command="exit" divided
                                              :loading="loggingOut">登出
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </el-col>
            </el-row>
        </el-header>
        <el-container>
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
                <el-main>
                    <WorkbenchEditor></WorkbenchEditor>
                </el-main>
                <el-footer class="footer">
                    <span class="footer-item">沪ICP备18033591号-1</span>
                    <span class="footer-item">Copyright @ Arrakis 2020</span>
                </el-footer>
            </el-container>
        </el-container>
    </div>
</template>


<script>
export default {
    name: "Me",
    data() {
        return {
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
                    this.$router.push({name: 'change_password'});
                    break;
                case 'exit':
                    this.logout();
                    break;
            }
        },
        logout() {
            this.loggingOut = true;
            this.$http
                .post('/workbench_api/logout')
                .then(response => {
                    if (response.data.code !== 0) {
                        this.$notify.error({
                            title: '错误',
                            message: response.data.message
                        });
                    } else {
                        this.$store.commit('setActiveArticleId', null);
                        this.$notify({
                            type: 'success',
                            title: '已登出',
                        });
                        this.$router.push({name: 'login'});
                    }
                }).catch().then(() => {
                this.loggingOut = false;
            });
        },
        loadTree() {
            this.loadingTree = true;
            this.$store.dispatch('loadTree')
                .catch()
                .then(() => {
                    this.loadingTree = false;
                });
        },
        treeAppendFolder(node, data) {
            this.$prompt(null, '请输入文件夹名称', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
            }).then(({value}) => {
                this.loadingTree = true;
                this.$http
                    .post('/workbench_api/addFolder', {
                        parent_id: data.entity_id,
                        name: value,
                    })
                    .then(response => {
                        if (response.data && response.data.code === 0) {
                            if (!data.children) {
                                this.$set(data, 'children', []);
                            }
                            data.children.push(response.data.folder);
                        } else if (response.data && response.data.message) {
                            this.$notify.error(response.data.message);
                        }
                    })
                    .catch()
                    .then(() => {
                        this.loadingTree = false;
                    });
            }).catch();
        },
        treeAppend(node, data) {
            this.$prompt(null, '请输入文件名称', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
            }).then(({value}) => {
                this.loadingTree = true;
                this.$http
                    .post('/workbench_api/addArticle', {
                        parent_id: data.entity_id,
                        name: value,
                    })
                    .then(response => {
                        if (response.data && response.data.code === 0) {
                            if (!data.children) {
                                this.$set(data, 'children', []);
                            }
                            data.children.push(response.data.article);
                        } else if (response.data && response.data.message) {
                            this.$notify.error(response.data.message);
                        }
                    })
                    .catch()
                    .then(() => {
                        this.loadingTree = false;
                    });
            }).catch();
        },
        treeRename(node, data) {
            this.$prompt('', '请输入新的名称', {
                inputValue: data.name,
                confirmButtonText: '确认',
                cancelButtonText: '取消',
            }).then(({value}) => {
                let uri = this.isFolder(node) ? 'renameFolder' : 'renameArticle';

                this.loadingTree = true;
                this.$http
                    .post('/workbench_api/' + uri, {
                        id: data.entity_id,
                        name: value,
                    })
                    .then(response => {
                        if (response.data && response.data.code === 0) {
                            const parent = node.parent;
                            const children = parent.data.children || parent.data;
                            const index = children.findIndex(d => d.id === data.id);
                            children.splice(index, 1, this.isFolder(node)
                                ? response.data.folder : response.data.article);
                        } else if (response.data && response.data.message) {
                            this.$notify.error(response.data.message);
                        }
                    })
                    .catch()
                    .then(() => {
                        this.loadingTree = false;
                    });
            }).catch();
        },
        treeRemove(node, data) {
            if (!node.isLeaf) {
                this.$alert('包含文件或文件夹，请清空后再试', '不能删除');
            } else {
                this.$confirm('此操作将删除该文件, 是否继续?', null, {
                    confirmButtonText: '确认删除',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    let uri = this.isFolder(node) ? 'deleteFolder' : 'deleteArticle';

                    this.loadingTree = true;
                    this.$http
                        .post('/workbench_api/' + uri, {
                            id: data.entity_id,
                        })
                        .then(response => {
                            if (response.data && response.data.code === 0) {
                                const parent = node.parent;
                                const children = parent.data.children || parent.data;
                                const index = children.findIndex(d => d.id === data.id);
                                children.splice(index, 1);

                                if (this.active_article_id === data.id) {
                                    this.active_article_id = null;
                                }
                            } else if (response.data && response.data.message) {
                                this.$notify.error(response.data.message);
                            }
                        })
                        .catch()
                        .then(() => {
                            this.loadingTree = false;
                        });
                }).catch();
            }
        },
        nodeClick(data, node) {
            if (this.isArticle(node)) {
                if (this.status.saving) {
                    this.$confirm('文档正在保存，现在切换可能丢失进度', '请确认', {
                        confirmButtonText: '继续切换',
                        cancelButtonText: '取消',
                        type: 'warning',
                    }).then(() => {
                        this.active_article_id = data.entity_id;
                    }).catch();
                } else {
                    this.active_article_id = data.entity_id;
                }
            }
        },
    },
    created() {
        if (this.user.name && this.$store.getters.opened_article_id) {
            this.active_article_id = parseInt(this.$store.getters.opened_article_id);
            this.$message('自动为您打开之前编辑中的文档');
        }

        this.loadTree();
    },
};
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
