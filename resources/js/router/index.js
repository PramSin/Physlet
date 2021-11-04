
import VueRouter from 'vue-router'
import Home from "../components/Home";
import About from "../components/About";
import Login from "../components/Login";
import Me from "../components/Me";
import Register from "../components/Register";
import Changepsw from  "../components/Changepsw";
import Demo from "../components/Demo"
import PageNotFound from "../components/PageNotFound";


export default new VueRouter({//TODO 在不存在页面时跳转404
    mode: 'history',
    routes:[
        {
            path:'/',
            component:Home
        },
        {
            path:'/about',
            component:About
        },
        {
            path:'/demo',
            component:Demo
        },
        {
            path:'/home',
            component:Home
        },
        {
            path:'/login',
            component:Login
        },
        {
            path:'/me',
            component:Me
        },
        {
            path:'/register',
            component:Register
        },
        {
            path:'/changepsw',
            component:Changepsw
        },
        { path: "*", component: PageNotFound }
    ]
});
