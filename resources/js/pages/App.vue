<template>
    <span  v-bind:class="{'top-nav':(showNav)}">
    <nav v-if="showNav">
    <div class="px-3 py-2 d-1">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center justify-content-start justify-content-lg-start main-header-flex-container">
                <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
                    <li>
                        <router-link to="/lineview">
                            <img src="/storage/images/walton-logo.png" alt="walton-logo" style="height: 40px;">
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/dashboard" class="nav-link text-white" active-class="active">
                            Dashboard
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/lineview" class="nav-link text-white" active-class="active">
                            Lineview
                        </router-link>
                    </li>
                    <li>
                        <a class="nav-link text-white dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Reports
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="/reports/oee-report">OEE</a></li>
                            <li><a class="dropdown-item" href="/reports/downtime-report">Downtime</a></li>
                            <li><a class="dropdown-item" href="#">Quality</a></li>
                            <li><a class="dropdown-item" href="#">Time Usage</a></li>
                            <li><a class="dropdown-item" href="#">Quantities</a></li>
                            <li><a class="dropdown-item" href="#">Cycle Time</a></li>
                            <li><a class="dropdown-item" href="#">Scrap Reasons</a></li>
                        </ul>
                    </li>
                    <li>
                        <router-link v-if="authorized" to="/settings/stations" class="nav-link text-white" active-class="active">
                            Settings
                        </router-link>
                    </li>
                </ul>
                <div class="logout">
                    <a href="javascript:;" @click="logout">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"></path>
                            <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"></path>
                        </svg>
                        Logout</a>
                </div>
            </div>
        </div>
    </div>
    </nav>

    <main :class="{'main':(showNav)}">
        <router-view></router-view>
    </main>
    </span>
</template>
<script>
import ReportMenuItem from '../components/ReportMenuItem';
import loginService from '../services/LoginService';

export default {
    name: 'App',
    data: () => ({}),
    components: {
        ReportMenuItem
    },
    methods:{
        reportSelected(eventData){
            this.$router.push({name:eventData});
        },
        logout() {
            loginService.logout(null, response => {
                this.$store.commit('addAuthProperties', {
                    token: null,
                    refreshToken: null,
                    tokenExpiration: null,
                    role: null
                });
                this.$router.push({
                    name : 'login'
                })
            }, error => {

            });
        }
    },
    computed :{
        showNav(){
            return this.$route.path !== '/login';
        },
        authorized() {
            return this.$store.getters.isAdmin || this.$store.getters.isManager;
        }
    }
}
</script>
