<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-light bg-theme">
            <div class="container-fluid">
                <ul class="navbar-nav mr-auto">
                    <router-link to="/settings/downtime_reasons" active-class="active" class="nav-item" tag="li">
                        <a href="/settings/downtime_reasons" class="nav-link">
                            <span class="sr-only">(current)</span>
                            Downtime Reasons
                        </a>
                    </router-link>
                    <router-link to="/settings/products" active-class="active" class="nav-item" tag="li">
                        <a class="nav-link">
                            Products
                        </a>
                    </router-link>

                    <router-link to="/settings/stations" active-class="active" class="nav-item" tag="li">
                        <a class="nav-link">
                            Stations
                        </a>
                    </router-link>

                    <router-link to="/settings/shifts" active-class="active" class="nav-item" tag="li">
                        <a class="nav-link">
                            Shifts
                        </a>
                    </router-link>

                    <router-link to="/settings/operators" active-class="active" class="nav-item" tag="li">
                        <a class="nav-link">
                            Operators
                        </a>
                    </router-link>
                </ul>
            </div>
        </nav>

        <div class="container-fluid py-4">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Settings',
        data: () => ({})
    }
</script>

<style scoped lang="scss">
    .navbar {
        background: #222056;
        padding: 0;

        .nav-item {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;

            .nav-link {
                border: none;
                color: #646389;
                font-size: 16px;
                line-height: 2.5rem;
            }

            &.active {
                .nav-link {
                    color: #dddddd;
                }
                padding-bottom: 0.25rem;
                border-bottom: 0.25rem solid #dddddd;
            }
        }
    }
</style>
