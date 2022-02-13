import Vue from 'vue'
import App from './App.vue'
import VueRouter from 'vue-router'
import router from './router'
import ElementUI from 'element-ui';
import Vuex from 'vuex'
import 'element-ui/lib/theme-chalk/index.css';
import axios from './api/physlet';
import store from "./store";
import VueFullscreen from 'vue-fullscreen'

Vue.config.productionTip = false
Vue.use(VueRouter)
Vue.use(ElementUI)
Vue.use(Vuex)
Vue.use(VueFullscreen)

Vue.prototype.$api = axios
Vue.prototype.$api.interceptors.response.use(
    (request) => {
        return request
    },
    (error) => {
        if (error.response.status === 401) {
            router.push({path:'/login'});
            return error
        }
    }
)
// console.log = function() {}
new Vue({
    render: h => h(App),
    router,
    store,
}).$mount('#app')
