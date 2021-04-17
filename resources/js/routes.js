import Vue from 'vue';
import Router from 'vue-router';

import LineView from "./pages/LineView";
import Settings from "./pages/Settings/Settings";
import DowntimeReasons from "./pages/Settings/DowntimeReasons";
import Products from "./pages/Settings/Products";
import Stations from "./pages/Settings/Stations";
import Shifts from "./pages/Settings/Shifts";
import Users from "./pages/Settings/Users";
import Operators from "./pages/Settings/Operators";
import OeeReport from "./pages/Reports/OeeReport";
import DowntimeReport from "./pages/Reports/DowntimeReport";
import LoginPage from "./pages/login/Login";

import Reports from "./pages/Reports/Reports";
import ReportBase from "./pages/Reports/ReportBase";
import {store} from './store/index'

Vue.use(Router);

const routes = [
    {
        path: '/',
        redirect: {
            path: '/lineview'
        }
    },
    {
        path: '/login',
        name: 'login',
        component: LoginPage,
        meta: {
            requiresAuth: false
        },
    },
    {
        path: '/lineview',
        name: 'lineview',
        component: LineView,
        meta: {
            requiresAuth: true
        },
    },
    {
        path: '/settings',
        component: Settings,
        meta: {
            requiresAuth: true
        },
        children: [
            {
                path: 'downtime_reasons',
                component: DowntimeReasons
            },
            {
                path: 'products',
                component: Products
            },
            {
                path: 'stations',
                component: Stations
            },
            {
                path: 'shifts',
                component: Shifts
            },
            {
                path: 'operators',
                component: Operators
            },
            {
                path: 'users',
                component: Users
            }
        ]
    },
    {
        path: '/reports',
        component: ReportBase,
        meta: {
            requiresAuth: true
        },
        children: [
            {
                name: 'oeeReport',
                path: 'oee-report',
                component: OeeReport
            },
            {
                name: 'downtimeReport',
                path: 'downtime-report',
                component: DowntimeReport
            }
        ]
    }
];

const router = new Router({
    mode: 'history',
    routes: routes
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (!store.getters.isLoggedIn) {
            console.log("Not logged in");
            next({
                path: '/login',
                query: { redirect: to.fullPath }
            })
        } else {
            next()
        }
    } else {
        next() // make sure to always call next()!
    }
});

export default router;
