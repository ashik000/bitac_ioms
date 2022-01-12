<template>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12 d-flex justify-content-start flex-wrap">
                <chart-card></chart-card>
            </div>
        </div>
    </div>
</template>

<script>
import ChartCard from "../components/dashboard/ChartCard";
export default {
    name: "Dashboard",
    components: {
        ChartCard,
    },
    data: () => ({
    }),
    methods: {
        fetchAllSummaryOeeData() {

        }
    },
    mounted() {

    }
}
</script>
