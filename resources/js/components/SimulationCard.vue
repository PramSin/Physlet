<template>
    <el-card :body-style="{ padding: '0px' }"
             shadow="hover">
        <el-skeleton :loading="false" animated>
            <template slot="template">

                <el-skeleton-item
                    variant="image"
                    style="height: 200px"/>

                <div style="padding: 14px">
                    <el-skeleton-item variant="h3" style="width: 50%;"/>

                    <div
                        style="display: flex; align-items: center; margin-top: 16px; height: 16px;">
                        <el-skeleton-item variant="text" style="margin-right: 16px;"/>
                        <el-skeleton-item variant="text" style="width: 30%;"/>
                    </div>
                </div>

            </template>
            <template>
                <el-image
                    class="image"/>
                <div style="padding: 14px;">
                    <span>好吃的汉堡</span>

                    <div class="bottom card-header">
                        <span class="time">现在时间</span>
                        <el-button type="text" class="button">操作按钮</el-button>
                    </div>
                </div>

            </template>
        </el-skeleton>
    </el-card>
</template>

<script>
export default {
    name: "SimulationCard"
}
</script>

<style scoped>
.image {
    width: 100%;
    height: 200px;
    display: block;
    align-items: center;
}
</style>
