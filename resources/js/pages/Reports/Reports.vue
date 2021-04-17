<template>
    <div>
        <report-picker
            :report="report"
            :partition="partition"
            :range="range"
            @report-selected="updateFilterReportType"
            @partition-selected="updatePartitionType"
            @range-selected="updateDateRange"
        />

        <nav class="entity-picker-wrap">
            <div class="container-fluid">
                <ul class="navbar-nav d-flex flex-row justify-content-center align-items-center">
                    <li class="nav-item" style="color: #dddddd;">
                        Station
                    </li>
                    <li class="nav-item b-1" style="width: 220px; text-align: center;">
                        <a class="nav-link p-3 dropdown-toggle" href="#"
                           @click.prevent="showStationPicker = true">
                            {{ stationName || 'All' }}
                        </a>
                    </li>
                    <li class="nav-item" style="color: #dddddd;">
                        Product
                    </li>
                    <li class="nav-item b-1" style="width: 220px; text-align: center;">
                        <a class="nav-link p-3 dropdown-toggle" href="#"
                           @click.prevent="showProductPicker = true">
                            {{ productName || 'All' }}
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="report-page">
            <div class="chart-wrapper">
                <oee-chart :title="title" :dataset="dataset" v-if="report === 'oee'"/>
                <downtime-chart :title="title" :dataset="downtimeDataset" v-if="report === 'downtime'"/>
            </div>
        </div>

        <modal v-if="showStationPicker" @close="showStationPicker = false">
            <template v-slot:header>
                <h2>Station Picker</h2>
            </template>
            <template v-slot:content>
                <ul class="picker-list">
                    <li v-for="station in stations"
                        :key="station.id"
                        :class="{ selected: station.id === stationId }"
                        @click.prevent="updateStation(station)">
                        {{ station.name }}
                    </li>
                </ul>
            </template>
            <template v-slot:footer>
                <div class="d-flex justify-content-center align-items-center w-100">
                    <button class="btn btn-outline btn-danger" @click="updateStation(null)">
                        Clear
                    </button>
                </div>
            </template>
        </modal>
        <modal v-if="showProductPicker" @close="showProductPicker = false">
            <template v-slot:header>
                <h2>Product Picker</h2>
            </template>
            <template v-slot:content>
                <ul class="picker-list">
                    <li v-for="product in products"
                        :key="product.id"
                        :class="{ selected: product.id === productId }"
                        @click.prevent="updateProduct(product)">
                        {{ product.name }}
                    </li>
                </ul>
            </template>
            <template v-slot:footer>
                <div class="d-flex justify-content-center align-items-center w-100">
                        <button class="btn btn-outline btn-danger" @click="updateProduct(null)">
                            Clear
                        </button>
                </div>
            </template>
        </modal>
    </div>
</template>
<script>
    import ReportPicker from "../../components/reports/ReportPicker";
    import OEEChart from "../../components/reports/OEEChart";
    import DowntimeChart from "../../components/reports/DowntimeChart";

    import reportService from '../../services/Reports';
    import stationService from '../../services/StationsService';
    import productService from '../../services/Products';

    export default {
        name: "Reports",
        components: {
            ReportPicker,
            'oee-chart': OEEChart,
            'downtime-chart': DowntimeChart
        },
        data: () => ({
            title: '',
            report: 'oee',
            partition: 'hourly',
            range: {
                start: moment().startOf('day').toDate(),
                end: moment().endOf('day').toDate()
            },
            stationName: null,
            stationId: null,
            productName: null,
            productId: null,
            dataset: {
                labels: [],
                performance: [],
                availability: [],
                quality: [],
                oee: [],
            },
            downtimeDataset: {
                labels: [],
                downtimes: []
            },
            showStationPicker: false,
            showProductPicker: false,
            stations: [],
            products: []
        }),
        methods: {
            updateFilterReportType(report) {
                this.report = report;

                switch (this.report) {
                    case "oee":
                        this.fetchOEEData();
                        break;
                    case "downtime":
                        this.fetchDowntimeData();
                }
            },
            updatePartitionType(partition) {
                this.partition = partition;

                switch (this.report) {
                    case "oee":
                        this.fetchOEEData();
                        break;
                    // case "downtime":
                    //     this.fetchDowntimeData();
                }
            },
            updateDateRange(range) {
                this.range.start = range.start;
                this.range.end = range.end;

                switch (this.report) {
                    case "oee":
                        this.fetchOEEData();
                        break;
                    case "downtime":
                        this.fetchDowntimeData();
                }
            },
            updateStation(station) {
                if (station) {
                    this.stationName = station.name;
                    this.stationId = station.id;
                } else {
                    this.stationName = null;
                    this.stationId = null;
                }

                switch (this.report) {
                    case "oee":
                        this.fetchOEEData();
                        break;
                    case "downtime":
                        this.fetchDowntimeData();
                }

                this.showStationPicker = false;
            },
            updateProduct(product) {
                if (product) {
                    this.productName = product.name;
                    this.productId = product.id;
                } else {
                    this.productName = null;
                    this.productId = null;
                }

                switch (this.report) {
                    case "oee":
                        this.fetchOEEData();
                        break;
                    case "downtime":
                        this.fetchDowntimeData();
                }

                this.showProductPicker = false;
            },
            fetchOEEData() {
                let data = {
                    stationId: this.stationId,
                    productId: this.productId,
                    start: moment(this.range.start).format('YYYY-MM-DD'),
                    endTime: moment(this.range.end).format('YYYY-MM-DD'),
                    type: this.partition
                };

                reportService.fetchReports(data, response => {
                    this.title = response.title;
                    this.dataset.labels = response.dataset.labels;
                    this.dataset.performance = response.dataset.performance;
                    this.dataset.availability = response.dataset.availability;
                    this.dataset.quality = response.dataset.quality;
                    this.dataset.oee = response.dataset.oee;
                });
            },
            fetchDowntimeData() {
                let data = {
                    stationId: this.stationId,
                    productId: this.productId,
                    start: moment(this.range.start).format('YYYY-MM-DD'),
                    endTime: moment(this.range.end).format('YYYY-MM-DD'),
                };

                reportService.fetchDowntimeData(data, response => {
                    this.title = response.title;
                    this.$set(this.downtimeDataset, 'labels', response.dataset.labels);
                    this.$set(this.downtimeDataset, 'downtimes', response.dataset.duration);
                });
            }
        },
        mounted() {
            stationService.fetchAll([], response => {
                this.stations = response;
            });

            productService.fetchAll([], response => {
                this.products = response;
            });
        }
    }
</script>

<style scoped lang="scss">
    @import "#/app.scss";

    .report-page {
        @extend .container-fluid, .px-0;

        .entity-picker-wrap {
            @extend .navbar, .navbar-expand-lg, .navbar-light;

            background: #222056;

            .navbar-nav {
                display: flex !important;
                flex-direction: row !important;
                justify-content: center;
                align-self: center;
            }
        }

        .chart-wrapper {
            @extend .w-100, .mx-auto;
        }

        .report-filter-header {
            color: #ffffff;
            background: #222056;
            margin: 2rem 0 0;

            .nav-tabs {
                border: none;

                .nav-item {
                    .nav-link {
                        border: none;
                        color: #ffffff;
                        font-size: 16px;
                        line-height: 2.5rem;
                    }
                }
            }
        }

        .report-filter-table {
            @extend .table-responsive;
            background: #ffffff;

            .table {
                thead {
                    background: #dddddd;
                }
            }
        }
    }

    .picker-list {
        padding: 0;
        list-style: none;

        li {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: space-between;

            font-size: 14px;
            padding: 1rem;
            cursor: pointer;

            border-bottom: 1px solid #0f0f0f;

            &.selected {
                background: #dddddd;
            }
        }
    }
</style>
