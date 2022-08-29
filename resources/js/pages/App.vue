<template>
    <span  v-bind:class="{'top-nav':(showNav)}">
    <nav v-if="showNav">
    <div class="px-3 d-1">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center justify-content-start justify-content-lg-start main-header-flex-container">
                <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small top_menu" v-bind:class="active" v-on:click.prevent>
                    <li class="padding_y_ten_px">
                        <router-link to="/lineview">
                            <img src="/storage/images/walton-logo.png" alt="walton-logo" style="height: 40px;">
                        </router-link>
                    </li>
                    <li class="dashboard padding_y_ten_px ms-5" v-on:click="makeActive('dashboard')">
                        <router-link to="/dashboard" class="nav-link text-white" active-class="active">
                            <b-icon icon="bar-chart" style="border: 1px solid #ff0000; padding: 2px;"></b-icon>
                            Dashboard
                        </router-link>
                    </li>
                    <li class="lineview padding_y_ten_px" v-on:click="makeActive('lineview')">
                        <router-link to="/lineview" class="nav-link text-white">
                            <b-icon icon="graph-up" style="border: 1px solid #ffffff; padding: 2px;"></b-icon>
                            Lineview
                        </router-link>
                    </li>
<!--                    <li class="scada padding_y_ten_px" v-on:click="makeActive('scada')">-->
<!--                        <router-link to="/scada" class="nav-link text-white" active-class="active">-->
<!--                            <b-icon icon="graph-up" style="border: 1px solid #ffffff; padding: 2px;"></b-icon>-->
<!--                            SCADA View-->
<!--                        </router-link>-->
<!--                    </li>-->
                    <li class="reports padding_y_ten_px" v-on:click="makeActive('reports')">
                        <a class="nav-link text-white dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <b-icon icon="file-bar-graph" style="border: 1px solid #ffffff; padding: 2px;"></b-icon>
                            Reports
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><router-link class="dropdown-item" to="/reports/oee-report">OEE</router-link></li>
                            <li><router-link class="dropdown-item" to="/reports/downtime-report">Downtime</router-link></li>
                        </ul>
                    </li>
                    <li class="machining_report padding_y_ten_px" v-on:click="makeActive('machining_report')">
                        <router-link to="/machining_report" class="nav-link text-white" active-class="active">
                            <b-icon icon="file-bar-graph" style="border: 1px solid #ffffff; padding: 2px;"></b-icon>
                            Machining Report
                        </router-link>
                    </li>
                    <li class="settings padding_y_ten_px" v-on:click="makeActive('settings')">
                        <router-link to="/settings/stations" class="nav-link text-white" active-class="active">
                            <b-icon icon="gear" style="border: 1px solid #ffffff; padding: 2px;"></b-icon>
                            Settings
                        </router-link>
                    </li>
                </ul>

                <div class="logout">
                    <span class="nav-clock">{{ currentTime }}</span>
                    <a href="javascript:;" @click="logout">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"></path>
                            <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"></path>
                        </svg>
                        Logout
                    </a>
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
import toastrService from '../services/ToastrService'

export default {
    name: 'App',
    data: () => ({
        currentTime: '',
        active: 'dashboard'
    }),
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
                toastrService.showSuccessToast('Logout successful')
                this.$router.push({
                    name : 'login'
                })
            }, error => {

            });
        },
        makeActive: function(item) {
            this.active = item;
        }
    },
    mounted() {
        this.$data._clock = () => {
            this.currentTime = moment().format('LTS');
        };

        setInterval(this.$data._clock, 1000);

        switch (this.$router.currentRoute.path) {
            case '/dashboard':
                this.active = 'dashboard';
            break;
        }
    },
    computed :{
        showNav(){
            return this.$route.path !== '/login';
        },
        authorized() {
            return this.$store.getters.isAdmin || this.$store.getters.isManager;
        },
    },
    created() {

        switch (this.$router.currentRoute.path) {
            case '/dashboard':
                this.active = 'dashboard';
                break;
            case '/lineview':
                this.active = 'lineview';
                break;
            case '/reports/oee-report':
                this.active = 'reports';
                break;
            case '/reports/downtime-report':
                this.active = 'reports';
                break;
            case '/machining_report':
                this.active = 'machining_report';
                break;
            case '/settings/stations':
                this.active = 'settings';
                break;
            case '/settings/products':
                this.active = 'settings';
                break;
            case '/settings/shifts':
                this.active = 'settings';
                break;
            case '/settings/operators':
                this.active = 'settings';
                break;
            case '/settings/downtime_reasons':
                this.active = 'settings';
                break;
            default:
                this.active = 'dashboard';
        }
    }
}
</script>

<style lang="scss" scoped>
    ul.dashboard .dashboard,
    ul.lineview .lineview,
    ul.reports .reports,
    ul.machining_report .machining_report,
    ul.settings .settings{
        background-color: #035FA3;
        margin: 0 2px 0 2px;
    }
    .padding_y_ten_px {
        padding: 8px 0 8px 0;
    }
</style>
