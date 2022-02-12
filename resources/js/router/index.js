
import VueRouter from 'vue-router'
import About from "../components/About";
import Login from "../components/Login";
import Me from "../components/Me";
import Register from "../components/Register";
import ChangePassword from "../components/ChangePassword";
import Demo from "../components/Demo"
import PageNotFound from "../components/PageNotFound";
import Edit_simulation from "../components/Edit_simulation";
import Portal from "../components/Portal";


export default new VueRouter({//TODO 在不存在页面时跳转404
    mode: "history",
    routes:[
        {
            path:'/',
            component:Portal
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
            path:'/portal',
            component:Portal
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
            path:'/change_password',
            component:ChangePassword
        },
        {
            path:'/edit_simulation',
            component:Edit_simulation
        },
        { path: "*", component: PageNotFound }
    ]
});
