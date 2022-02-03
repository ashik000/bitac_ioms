<template>
    <span>
        <nav>
            <div class="px-3 border-bottom mb-3 d-2">
                <div class="container d-flex flex-wrap justify-content-left">

                    <ul id="settings_menu" class="nav col-12 col-lg-auto my-2 justify-content-left my-md-0 text-small" v-bind:class="active2" v-on:click.prevent>
                        <li class="stations padding_y" v-on:click="makeActive2('stations')">
                            <router-link to="/settings/stations" active-class="active" class="nav-link text-white">
                                Stations
                            </router-link>
                        </li>
                        <li class="products padding_y" v-on:click="makeActive2('products')">
                            <router-link to="/settings/products" active-class="active" class="nav-link text-white">
                                Products
                            </router-link>
                        </li>
                        <li class="shifts padding_y" v-on:click="makeActive2('shifts')">
                            <router-link to="/settings/shifts" active-class="active" class="nav-link text-white">
                                Shifts
                            </router-link>
                        </li>
                        <li class="teams padding_y" v-on:click="makeActive2('teams')">
                            <router-link to="/settings/teams" active-class="active" class="nav-link text-white">
                                Teams
                            </router-link>
                        </li>
                        <li class="operators padding_y" v-on:click="makeActive2('operators')">
                            <router-link to="/settings/operators" active-class="active" class="nav-link text-white">
                                Operators
                            </router-link>
                        </li>
                        <li class="downtime_reasons padding_y" v-on:click="makeActive2('downtime_reasons')">
                            <router-link to="/settings/downtime_reasons" active-class="active" class="nav-link text-white">
                                Downtime Reasons
                            </router-link>
                        </li>
                        <li class="machining_report padding_y" v-on:click="makeActive2('machining_report')">
                            <router-link to="/settings/machining-report" active-class="active" class="nav-link text-white">
                                Machining Report
                            </router-link>
                        </li>
                    </ul>

                </div>
            </div>
        </nav>

        <div class="container-fluid wrapper">
            <router-view></router-view>
        </div>

    </span>
</template>

<script>
export default {
    name: 'Settings',
    data: () => ({
        active2: 'stations'
    }),
    methods:{
        makeActive2: function(item){
            this.active2 = item;
        }
    },
    created() {

        switch (this.$router.currentRoute.path) {
            case '/settings/stations':
                this.active = 'settings';
                this.active2 = 'stations';
                break;
            case '/settings/products':
                this.active = 'settings';
                this.active2 = 'products';
                break;
            case '/settings/shifts':
                this.active = 'settings';
                this.active2 = 'shifts';
                break;
            case '/settings/teams':
                this.active = 'settings';
                this.active2 = 'teams';
                break;
            case '/settings/operators':
                this.active = 'settings';
                this.active2 = 'operators';
                break;
            case '/settings/downtime_reasons':
                this.active = 'settings';
                this.active2 = 'downtime_reasons';
                break;
            case '/settings/machining-report':
                this.active = 'settings';
                this.active2 = 'machining-report';
                break;
            default:
                this.active = 'settings';
                this.active2 = 'stations';
        }
    }
}
</script>

<style lang="scss" scoped>
    #settings_menu.stations .stations,
    #settings_menu.products .products,
    #settings_menu.shifts .shifts,
    #settings_menu.teams .teams,
    #settings_menu.operators .operators,
    #settings_menu.downtime_reasons .downtime_reasons{
        background-color: #007eda;
        margin: 0 2px 0 2px;
        padding: 5px 0 5px 0;
    }
    .padding_y {
        padding: 5px 0 5px 0;
    }
</style>
