<template>
    <div>
    <iframe style="height:100vmin; width:100%;" :src="demosrc" ref="iframe"></iframe>
    </div>
</template>

<script>
export default {
    name: "Demo",

    data(){
        return {
            demosrc: 'https://www.openstreetmap.org/export/embed.html?bbox=-0.004017949104309083%2C51.47612752641776%2C0.00030577182769775396%2C51.478569861898606&layer=mapnik',
        }
    }
}
</script>

<style scoped>

</style>
