
import VueRouter from 'vue-router'
import About from "../components/About";
import Login from "../components/Login";
import Me from "../components/Me";
import Register from "../components/Register";
import ChangePassword from "../components/ChangePassword";
import Demo from "../components/Demo"
import PageNotFound from "../components/PageNotFound";
import EditSimulation from "../components/EditSimulation";
import Portal from "../components/Portal";
import UserInfo from "../components/UserInfo";
import SearchPage from "../components/SearchPage";


export default new VueRouter({
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
            component:EditSimulation
        },
        {
            path:'/user_info',
            component:UserInfo
        },
        {
            path: "/search_page",
            component:SearchPage
        },
        { path: "*", component: PageNotFound }
    ]
});
