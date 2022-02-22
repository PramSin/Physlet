<template>
    <el-container>
        <el-aside></el-aside>
        <el-main>
            <h3>我的模拟 (共{{my_simulation_amount}}个)</h3>
            <el-card style="border-radius: 15px" v-loading="loading_my_simulations"
                     v-for="simulation in my_simulation_list"
                     :key="simulation.simulation_id"
                     @click.native="jump_to_my_simulation(simulation)">
                <div slot="header">
                    <div>
                        <i class="el-icon-data-analysis" style="margin-right: 5px; font-size: 15px"></i>
                        <el-tag size="small">{{ simulation.catagory_name }}</el-tag>
                        <div style="float: right">
                            <el-button size="small" @click="open_edit_form(simulation)">编辑</el-button>
                            <el-popconfirm title="确定删除? " @confirm="delete_simulation(simulation)">
                                <el-button slot="reference" size="small">删除</el-button>
                            </el-popconfirm>
                        </div>
                    </div>
                    <el-button type="text" v-if="!loading_my_simulations" @click.stop="jump_to_my_simulation(simulation)" style="font-size: 23px;margin-left: 0">{{ simulation.simulation_name }}</el-button>
                </div>
                <span v-if="!loading_my_simulations">{{ simulation.synopsis }}</span>
                <span v-if="!loading_my_simulations"
                      style="float: right; font-size: small">{{ simulation_access(simulation) }}</span>
                <br/>
                <span
                    style="font-size: small; color: gray" v-if="!loading_my_simulations">创建时间 {{
                        simulation.create_time.slice(0, 10)
                    }}</span>
                <div style="float: right">
                    <span v-if="!loading_my_simulations">likes {{ simulation.likes }}</span>
                </div>
            </el-card>
            <el-pagination
                style="display:table; margin:0 auto; "
                @current-change="current_page"
                layout="prev, pager, next"
                :hide-on-single-page="true"
                :pager-count="11"
                :page-size="10"
                :total="my_simulation_amount">
            </el-pagination>
            <el-dialog title="编辑模拟" :visible.sync="edit_form_visibility" :center="true">
                <el-form :inline="false" :model="edit_form">
                    <el-form-item label="名称" style="width: 35%">
                        <el-input
                            placeholder=""
                            v-model="edit_form.sname"
                            clearable>
                        </el-input>
                    </el-form-item>
                    <el-form-item label="类别">
                        <el-select v-model="edit_form.cid" placeholder="类别">
                            <el-option
                                v-for="category in edit_category_list"
                                :key="category.edit_category_id"
                                :label="category.edit_category_name"
                                :value="category.edit_category_id"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="简介">
                        <el-input
                            type="textarea"
                            autosize
                            placeholder=""
                            v-model="edit_form.synopsis">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="可见性">
                        <el-select v-model="edit_form.access" placeholder="可见性">
                            <el-option value="0" label="私人"></el-option>
                            <el-option value="1" label="公共"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="submit_edit">提交</el-button>
                    </el-form-item>
                </el-form>
            </el-dialog>
        </el-main>
        <el-aside>
            <h3 style="margin-top: 40px">上传模拟</h3>
            <el-form label-width="100px" style="width: 90%">
                <el-form-item label="名称" required>
                    <el-input
                        placeholder="请填写模拟名称"
                        v-model="simulation_name"
                        clearable
                    ></el-input>
                </el-form-item>
                <br/>
                <el-form-item label="类别" required>
                    <el-select style="margin-top: 15px" v-model="upload_category" placeholder="请选择模拟类别">
                        <el-option
                            v-for="item of upload_category_list"
                            :key="item.upload_category_id"
                            :value="item.upload_category_id"
                            :label="item.upload_category_name">
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
                        <div slot="tip" class="el-upload__tip">必须为zip格式，不超过20mb</div>
                    </el-upload>
                </el-form-item>
                <el-form-item>
                    <el-button size="small" type="success" @click="submitFile">点击上传</el-button>
                </el-form-item>
            </el-form>
        </el-aside>
    </el-container>
</template>


<script>
import moment from 'moment'

export default {
    name: "Me",
    data() {
        return {
            my_simulation_amount: 0,
            loading_my_simulations: true,
            my_simulation_list: [],
            simulation_to_edit: [],
            upload_category_list: [],
            edit_category_list: [],
            edit_form: {
                sid: "",
                sname: "",
                cid: "",
                synopsis: "",
                access: "",
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
            upload_category: "",
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
        current_page(current) {
            this.my_simulation_list.splice(0, this.my_simulation_list.length)
            this.loading_my_simulations = true
            this.$api
                .post('/physlet_api/getMySims', {opt: current - 1})
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
                        simulation_list.access = data[syn].access
                        simulation_list.create_time = data[syn].create_time
                        this.my_simulation_list.push(simulation_list)
                    }
                    this.loading_my_simulations = false
                })
        },
        simulation_access(simulation) {
            if (simulation.access === 1) {
                return "公共"
            } else return "私人"
        },
        open_edit_form(simulation) {
            console.log(simulation)
            this.edit_form_visibility = true
            this.edit_form.sid = simulation.simulation_id
            this.edit_form.cid = simulation.catagory_id
            this.edit_form.synopsis = simulation.synopsis
            this.edit_form.access = simulation.access
            this.edit_form.sname = simulation.simulation_name
        },
        delete_simulation(simulation) {
            this.$api
                .post('/physlet_api/deleteSim', {sid: simulation.simulation_id})
                .then(response => {
                    if (response.data.code === 200) {
                        this.$message({
                            message: "删除成功!",
                            type: "success"
                        })
                    } else {
                        window.alert(response.data.message)
                    }
                })
        },
        submit_edit() {
            this.$api
                .post('/physlet_api/editSim', this.edit_form)
                .then(response => {
                    if (response.data.code === 200) {
                        this.$message({
                            message: "修改成功!",
                            type: "success"
                        })
                        this.edit_form_visibility = false
                        location.reload()
                    } else {
                        window.alert(response.data.message)
                    }
                })
        },
        jump_to_my_simulation(simulation) {
            this.$router.push({path: "/demo", query: {sid: simulation.simulation_id}});
        },
        edit_Simulation(row) {
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
        beforeRemove(file) {
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
            formData.append('sname', this.simulation_name)
            formData.append('cid', this.upload_category)
            formData.append('synopsis', this.synopsis)
            formData.append('access', this.access)
            formData.append('file', this.files.raw)
            const headers = {'Content-Type': 'multipart/form-data;boundary=","'}
            this.$api.post('/physlet_api/uploadSim',
                formData,
                {headers},
            ).then((res) => {
                res.data.files; // binary representation of the file
                res.status; // HTTP status
                location.reload()
            });
        },
    },
    mounted() {
        this.$api
            .post('/physlet_api/getMySims')
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
                    simulation_list.access = data[syn].access
                    simulation_list.create_time = data[syn].create_time
                    this.my_simulation_list.push(simulation_list)
                }
                this.my_simulation_amount = response.data.number
                this.loading_my_simulations = false
            })
        this.$refs.upload.clearFiles()
        this.$api
            .get('/physlet_api/myInfo')
            .then(response => {
                this.username = response.data.data.username
            })
        this.$api
            .get('/physlet_api/getCats')
            .then(response => {
                let data = response.data.data;
                for (let syn = 0; syn < data.length; syn++) {
                    let category = {}
                    let edit_category = {}
                    category.upload_category_name = data[syn].cname
                    edit_category.edit_category_name = data[syn].cname
                    category.upload_category_id = data[syn].cid
                    edit_category.edit_category_id = data[syn].cid
                    this.upload_category_list.push(category)
                    this.edit_category_list.push(edit_category)
                }
            })
    }

}

</script>

<style>
</style>
