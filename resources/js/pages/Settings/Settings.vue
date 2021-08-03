<template>
    <span>
        <nav>
            <div class="px-3 pt-2 border-bottom mb-3 d-2">
                <div class="container d-flex flex-wrap justify-content-left">

                    <ul class="nav col-12 col-lg-auto my-2 justify-content-left my-md-0 text-small">
                        <li>
                            <router-link to="/settings/stations" active-class="active" class="nav-link text-white">
                                Stations
                            </router-link>
                        </li>
                        <li>
                            <router-link to="/settings/products" active-class="active" class="nav-link text-white">
                                Products
                            </router-link>
                        </li>
                        <li>
                            <router-link to="/settings/shifts" active-class="active" class="nav-link text-white">
                                Shifts
                            </router-link>
                        </li>
                        <li>
                            <router-link to="/settings/operators" active-class="active" class="nav-link text-white">
                                Operators
                            </router-link>
                        </li>
                        <li>
                            <router-link to="/settings/downtime_reasons" active-class="active" class="nav-link text-white">
                                Downtime Reasons
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
    data: () => ({})
}
</script>
