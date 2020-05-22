/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// node packages
import { Form, HasError, AlertError } from "vform" // vueform from github
import moment from 'moment'
import VueProgressBar from 'vue-progressbar'
import Swal from "sweetalert2";

// components
import VueRouter from 'vue-router'
import Dashboard from './components/Dashboard.vue'
import Profile from './components/Profile.vue'
import Users from './components/Users.vue'
import Developer from './components/Developer.vue'
import PageNotFound from './components/PageNotFound.vue'
import Invoice from './components/Invoice.vue'

//gate
import Gate from './Gate'
Vue.prototype.$gate = new Gate(window.user) // prototyping to use the gate anywhere in application; for window.user refer to the master.blade bottom

window.Form = Form
window.Swal = Swal

Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);

Vue.use(VueRouter)

let routes = [{
        path: '/dashboard',
        component: Dashboard
    },
    {
        path: '/profile',
        component: Profile
    },
    {
        path: '/users',
        component: Users
    },
    {
        path: '/developer',
        component: Developer
    },
    {
        path: '/invoice',
        component: Invoice
    },
    {
        path: '*',
        component: PageNotFound
    }
]

const router = new VueRouter({
    mode: 'history',
    routes
})

// vue progress bar
const options = {
    color: "#bffaf3",
    failedColor: "#874b4b",
    thickness: "5px",
    transition: {
        speed: "0.2s",
        opacity: "0.6s",
        termination: 300
    },
    autoRevert: true,
    location: "top",
    inverse: false
};
Vue.use(VueProgressBar, options);

// pagination
Vue.component('pagination', require('laravel-vue-pagination'));

// sweet alert toast
const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    onOpen: toast => {
        toast.addEventListener("mouseenter", Swal.stopTimer);
        toast.addEventListener("mouseleave", Swal.resumeTimer);
    }
});
window.Toast = Toast;

Vue.filter('capitalize', (value) => { // global filter
    if (!value) return ''
    value = value.toString()
    return value.charAt(0).toUpperCase() + value.slice(1)
})

Vue.filter("dateFormat", (date) => {
    return moment(date).format("MMMM Do YYYY");
});

// passport components
Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue').default
);

Vue.component(
    'page-not-found',
    require('./components/PageNotFound.vue').default
);

let VueGlobal = new Vue()
window.VueGlobal = VueGlobal

const app = new Vue({
    el: '#app',
    router,
    data: {
        search: ''
    },
    methods: {
        search_it: _.debounce( () =>
            {
                VueGlobal.$emit('searching') // custom event
            },
            1000
        )
    }
}).$mount('#app');
