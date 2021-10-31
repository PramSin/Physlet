
import VueRouter from 'vue-router'
import Home from "../components/Home";
import About from "../components/About";
import Login from "../components/Login";
import Me from "../components/Me";
import Register from "../components/Register";
import Changepsw from  "../components/Changepsw";


export default new VueRouter({
    routes:[
        {
            path:'/about',
            component:About
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
        }
    ]
});
