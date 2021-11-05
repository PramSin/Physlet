<template>
    <div>
    <iframe style="height:100vmin; width:100%;" :src="demo_src" ref="iframe"></iframe>
    </div>
</template>

<script>
export default {
    name: "Demo",

    data(){
        return {
            demo_src: 'https://www.openstreetmap.org/export/embed.html?bbox=-0.004017949104309083%2C51.47612752641776%2C0.00030577182769775396%2C51.478569861898606&layer=mapnik',
        }
    },
    mounted() {
        console.log(this.$route.query)
        let params = this.$route.query
        this.$api
            .get('/physlet_api/getPackage', {params})
            .then(response => {
                let data = response.data.data;
                this.demo_src = data


            })
    }
}
</script>

<style scoped>

</style>
