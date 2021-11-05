import Vue from 'vue'
import App from './App.vue'
import VueRouter from 'vue-router'
import router from './router'
import ElementUI from 'element-ui';
import Vuex from 'vuex'
import 'element-ui/lib/theme-chalk/index.css';
import axios from './api/physlet';
import store from "./store";

Vue.config.productionTip = false
Vue.use(VueRouter)
Vue.use(ElementUI)
Vue.use(Vuex)

Vue.prototype.$api = axios
Vue.prototype.$api.interceptors.response.use(
    (request) => {
        console.log(request)
        return request
    },
    (error) => {
        console.log(error.response.status)
        if (error.response.status === 401) {
            router.push({path:'/login'});
            console.log(error)
            return error
        }
    }
)

new Vue({
    render: h => h(App),
    router,
    store,
}).$mount('#app')
