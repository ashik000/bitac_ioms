<template>
    <div v-bind:class="{'wrapper':(showNav)}">
        <nav v-if="showNav" class="main-navbar px-0">
            <div class="container-fluid">
                <router-link to="/lineview" class="nav-item" active-class="active">
                    <a class="navbar-brand" href="/lineview">
                        <img src="storage/images/walton-logo.png" style="height: 40px" alt="Walton">
<!--                        <img v-if="process.env.MIX_APP_NAME == 'BOF_LARAVEL'" src="../../assets/images/inovace-logo.png" style="height: 40px" alt="Walton">-->
                    </a>
                </router-link>
                <button class="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <router-link to="/lineview" class="nav-item" active-class="active" tag="li">
                            <a href="/lineview" class="nav-link">
                                <span class="sr-only">(current)</span>
                                LINEVIEW
                            </a>
                        </router-link>

                        <router-link v-if="authorized" to="/reports" class="nav-item" active-class="active" tag="li">
                            <!--<a href="/reports" class="nav-link">-->
                                <!--REPORTS-->
                            <!--</a>-->
                            <report-menu-item @reportMenuItemSelected="reportSelected"></report-menu-item>
                        </router-link>

                        <router-link v-if="authorized" to="/settings/downtime_reasons" class="nav-item" active-class="active" tag="li">
                            <a href="/settings/downtime_reasons" class="nav-link">
                                SETTINGS
                            </a>
                        </router-link>
                    </ul>
                </div>

                <div class="pull-right">
                    <a href="#" @click="logout" class="nav-link">
                        LOGOUT
                    </a>
                </div>
            </div>
        </nav>

        <main :class="{'main':(showNav)}">
            <router-view></router-view>
        </main>
    </div>
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

<style lang="scss" scoped>
    .wrapper {
        width: 100vw;
        height: 100vh;

        display: flex;

        .main {
            flex-grow: 1;
        }
    }
</style>
