import Vue from 'vue'
import App from './App.vue'
import VueRouter from 'vue-router'
import router from './router'
import ElementUI from 'element-ui';
import Vuex from 'vuex'
import 'element-ui/lib/theme-chalk/index.css';
import axios from 'axios';
import store from "./store";

Vue.config.productionTip = false
Vue.use(VueRouter)
Vue.use(ElementUI)
Vue.use(Vuex)

Vue.use({
    install (Vue) {
        Vue.prototype.$api = axios.create({
            baseURL: 'http://127.0.0.1:8000/'
        })
    }
})
new Vue({
  render: h => h(App),
  router,
  store,
}).$mount('#app')
