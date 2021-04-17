<template>
    <div class="line-view-wrap page-wrap">
        <div class="line-view-content container-fluid">
            <header class="line-view-header">
                <production-summary-panel
                    class="col-12 col-lg-4"
                    :produced="oeeSummary.summary.produced"
                    :expected="oeeSummary.summary.expected"
                    :oee="oeeSummary.summary.oee"
                    :products="products"></production-summary-panel>

                <div class="col-12 col-lg-5">
                    <div class="station-picker-wrap">
                        <button class="station-picker" @click.prevent="showStationSelectionForm">
                            <i class="material-icons">
                                label
                            </i>
                            {{ filter.stationName }}
                        </button>

                        <div class="date-wrap">
                            <div class="date-picker">
                                <i class="material-icons">insert_invitation</i>
                                <v-date-picker :max-date='new Date()'
                                               mode='single' v-model='filter.selectedDate'
                                               :masks="{ input: 'WWW, DD MMMM' }"
                                               :input-props="{ class: 'date-picker-input border-0 w-full bg-transparent px-2 text-white h5' }"
                                               @input="changeSelectedDate"/>
                            </div>

                            <div class="date-nav">
                                <button class="btn control-btn" @click="changeSelectedDate('PREV')">
                                    <i class="material-icons">
                                        navigate_before
                                    </i>
                                </button>
                                <button class="btn control-btn" @click="changeSelectedDate('NEXT')"
                                        :disabled="filter.selectedDate >= new Date().setHours(0,0,0,0)">
                                    <i class="material-icons">
                                        navigate_next
                                    </i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <oee-summary-panel
                        :hourly-data="oeeSummary.hourly"
                        :summary-data="oeeSummary.summary"/>
                </div>

                <div class="clock-wrap">
                    <img class="inovace-logo" src="../../assets/images/inovace-logo.png" alt="Inovace Technologies">
                    <p class="clock">{{ currentTime }}</p>
                </div>
            </header>

            <line-view-graph
                class="line-view-graph-wrap mt-4"
                :linedata="linedata.logs"
                @downtime-clicked="openDowntimeReasonsSelectionModal"/>
        </div>
        <footer class="line-view-footer">
            <div class="controls container-fluid">
                <button @click="isOperatorSelectionModalShown = true">
                    <i class="icon material-icons">account_circle</i>
                    Operator
                </button>
                <button @click="isDowntimeSummaryModalShown = true">
                    <i class="icon material-icons">timer</i>
                    Downtime
                </button>
                <button @click="isScrapInputModalShown = true">
                    <i class="icon material-icons">delete</i>
                    Scrap
                    <title>Delete Downtime Reason</title>
                </button>
            </div>
        </footer>

        <modal v-if="isStationSelectionFormShown"
               class="station-selector-modal"
               @close="isStationSelectionFormShown = false">
            <template v-slot:header>
                <h4 class="text-uppercase">Select Station</h4>
            </template>
            <template v-slot:content>
                <ul class="stations-list">
                    <li v-for="station in stations"
                        :key="station.id"
                        @click.prevent="changeSelectedStation(station)">
                        {{ station.name }}
                        <i class="material-icons" v-if="station.id === filter.stationId">done</i>
                    </li>
                </ul>
            </template>
            <template v-slot:footer>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-outline-danger" @click.prevent="isStationSelectionFormShown = false">Close
                    </button>
                </div>
            </template>
        </modal>

        <modal v-if="isDowntimeReasonsModalShown"
               class="reason-selector-modal"
               @close="isDowntimeReasonsModalShown = false"
        >
            <template v-slot:header>
                <h4 class="text-uppercase">Select Downtime Reason</h4>
            </template>
            <template v-slot:content>
                <ul class="reasons-list">
                    <li v-for="reason in downtimeReasons"
                        :key="reason.id"
                        @click.prevent="assignDowntimeReason(reason)">
                        {{ reason.name }}
                    </li>
                </ul>
            </template>
        </modal>

        <operator-selection-modal
            v-if="isOperatorSelectionModalShown"
            @close="operatorSelectionModalClosed()"
            :station-id="filter.stationId"
            :station-name="filter.stationName">
        </operator-selection-modal>

        <downtime-summary-modal
            v-if="isDowntimeSummaryModalShown"
            @close="downtimeSummaryModalClosed()"
            :stationId="filter.stationId"
            :date="filter.selectedDate"></downtime-summary-modal>

        <scrap-input-model
            v-if="isScrapInputModalShown"
           @close="scrapInputModalClosed()"
           :stationId="filter.stationId"
           :date="filter.selectedDate"></scrap-input-model>
    </div>
</template>

<script>
    import LineViewService from "../services/LineViewService";
    import StationsService from "../services/StationsService";
    import DowntimeReasonsService from '../services/DowntimeReasons'
    import {LineViewDataTransformer} from "../transformers/LineViewDataTransformer";

    import OEESummaryPanel from "../components/lineview/OEESummaryPanel";
    import ProductionSummaryPanel from "../components/lineview/ProductionSummaryPanel";
    import LineViewGraph from "../components/lineview/LineViewGraph";
    import Modal from "../components/Modal";
    import DowntimeSummaryModal from "../components/lineview/downtimesummary/DowntimeSummaryModal";
    import ScrapInputModel from "../components/lineview/scrapinput/ScrapInputModal";
    import OperatorSelectionModal from "../components/lineview/operatorselection/OperatorSelectionModal";

    export default {
        name: "LineView",
        components: {
            OperatorSelectionModal,
            Modal,
            DowntimeSummaryModal,
            LineViewGraph,
            ProductionSummaryPanel,
            'oee-summary-panel': OEESummaryPanel,
            ScrapInputModel,
        },
        data: () => ({
            currentTime: '',
            isInitialized: false,
            isStationSelectionFormShown: false,
            isDowntimeReasonsModalShown: false,
            isDowntimeSummaryModalShown: false,
            isOperatorSelectionModalShown: false,
            isScrapInputModalShown: false,
            selectedDowntime: null,
            filter: {
                stationId: 1,
                stationName: '',
                selectedDate: new Date(),
            },
            downtimeReasons: [],
            stations: [],
            products: [],
            oeeSummary: {
                hourly: {
                    labels: [],
                    performance: [],
                    availability: [],
                    quality: [],
                    oee: [],
                },
                summary: {
                    produced: 0,
                    expected: 0,
                    performance: 0,
                    availability: 0,
                    quality: 0,
                }
            },
            linedata: {
                logs: [],
            }
        }),
        methods: {
            showStationSelectionForm(show = false) {
                this.isStationSelectionFormShown = show;
            },
            operatorSelectionModalClosed(){
                this.isOperatorSelectionModalShown = false;
            },
            downtimeSummaryModalClosed() {
                this.isDowntimeSummaryModalShown = false;
                this.fetchData();
            },
            scrapInputModalClosed() {
                this.isScrapInputModalShown = false;
            },
            changeSelectedDate(type) {
                switch (type) {
                    case 'PREV':
                        this.filter.selectedDate = moment(this.filter.selectedDate).subtract({days: 1}).toDate();
                        break;
                    case 'NEXT':
                        this.filter.selectedDate = moment(this.filter.selectedDate).add({days: 1}).toDate();
                        break;
                    default:
                        this.filter.selectedDate = moment(type).toDate();
                        break;
                }

                this.fetchData();

                this.isStationSelectionFormShown = false;
            },
            changeSelectedStation(station) {
                this.filter.stationId = station.id;
                this.filter.stationName = station.name;
                this.isStationSelectionFormShown = false;

                this.fetchData();
            },
            openDowntimeReasonsSelectionModal(bar) {
                if (bar.type === 'downtime') {
                    this.isDowntimeReasonsModalShown = true;
                    this.selectedDowntime = bar;
                    DowntimeReasonsService.fetchAll([], reasons => {
                        this.reasons = reasons;
                    })
                }
            },
            assignDowntimeReason(reason) {
                DowntimeReasonsService.assignDowntime({
                    downtimeId: this.selectedDowntime.id,
                    downtimeReasonId: reason.id,
                }, data => {
                    this.isDowntimeReasonsModalShown = false;
                });
            },
            fetchData() {
                LineViewService.fetchLineViewData({
                        stationId: this.filter.stationId,
                        date: moment(this.filter.selectedDate).format('YYYY-MM-DD')
                    },
                    (data) => {
                        const {products, logs, hourlyMetric, summaryMetric} = LineViewDataTransformer(data);

                        this.$set(this, 'products', products);
                        this.$set(this.linedata, 'logs', logs);
                        this.$set(this.oeeSummary, 'hourly', hourlyMetric);
                        this.$set(this.oeeSummary, 'summary', summaryMetric);

                        this.isInitialized = true;
                    }
                );
            }
        },
        computed: {
            formattedSelectedDate() {
                return moment(this.filter.selectedDate).format('dddd, DD MMMM, YYYY');
            }
        },
        mounted() {
            const vm = this;
            this.$data._clock = () => {
                vm.currentTime = moment().format('LTS');
            };
            setInterval(this.$data._clock, 1000);

            this.fetchData();
            this.$data._updateData = () => {
                if (moment().diff(vm.filter.selectedDate, 'day') === 0) {
                    vm.fetchData();
                }
            };
            setInterval(this.$data._updateData, 10000);

            StationsService.fetchAll({}, (data) => {
                this.$set(this, 'stations', data);
                this.filter.stationId = this.stations[0].id;
                this.filter.stationName = this.stations[0].name;
            });

            DowntimeReasonsService.fetchAll({}, (data) => {
                this.$set(this, 'downtimeReasons', data);
            });
        },
        destroyed() {
            clearInterval(this.$data._clock);
            clearInterval(this.$data._updateData);
        }
    }
</script>

<style scoped lang="scss">
    @import "#/app.scss";

    .line-view-wrap {
        width: 100vw;
        height: 100%;

        display: flex;
        flex-direction: column;

        padding: 2rem 0 0;

        .line-view-content {
            flex-grow: 1;

            display: flex;
            flex-direction: column;

            .line-view-header {
                @extend .row;

                .station-picker-wrap {
                    color: #ffffff;

                    .station-picker {
                        background: transparent;
                        border: none;
                        color: #ffffff;
                        font-size: 32px;
                        line-height: 32px;
                        padding: 0.25rem 0.5rem;

                        i {
                            font-size: 28px;
                        }
                    }

                    .label-btn {
                        position: relative;
                        background: transparent;
                        border: none;
                        color: #ffffff;
                        font-size: 18px;


                        i {
                            margin-right: 0.5rem;
                            vertical-align: text-top;
                        }
                    }

                    .date-wrap {
                        margin: 1rem 0 1rem;

                        display: flex;
                        flex-direction: row;
                        flex-wrap: nowrap;
                        justify-content: space-between;

                        .date-picker {
                            display: flex;
                            flex-direction: row;
                            flex-wrap: nowrap;
                            padding: 0 0.5rem 0.15rem 0.5rem;
                        }

                        .date-picker-input {
                            box-shadow: none !important;
                            border: none !important;
                            background: none !important;
                            color: #ffffff !important;
                            font-size: 18px !important;
                            width: 230px !important;
                            padding: 0 0.5rem !important;
                            cursor: pointer !important;
                        }

                        .btn {
                            color: #ffffff;
                            background: $primary-color-accent;
                            border-radius: 0.25rem;
                            padding: 0.15rem 1rem;
                            font-size: 16px;

                            i {
                                vertical-align: bottom;
                            }

                            &:hover,
                            &:focus,
                            &.active {
                                background: $primary-color-alt;
                            }
                        }
                    }
                }

                .clock-wrap {
                    @extend .d-none, .d-lg-flex, .col-lg-3;

                    display: flex;
                    flex-direction: column;
                    justify-content: flex-start;
                    align-items: center;

                    .clock {
                        margin: 1rem auto;
                        font-size: 32px;
                        color: #ffffff;
                        text-align: center;
                        font-family: monospace;
                    }

                    .inovace-logo {
                        max-width: 200px;
                    }
                }
            }

            > .line-view-graph-wrap {
                flex-grow: 1;
                margin-bottom: 50px;
                max-height: calc(100vh - 22rem);
            }
        }

        .line-view-footer {
            background: #2e2e2e;
            position: fixed;
            bottom: 0;
            width: 100%;
            z-index: 10;
            .controls {
                display: flex;
                flex-direction: row;
                width: 100%;

                button {
                    padding: 1rem;
                    margin: 0;

                    flex-grow: 1;

                    border: none;
                    background: #2e2e2e;

                    color: #ffffff;
                    font-size: 1.25rem;
                    line-height: 1.25rem;

                    transition: all 250ms ease-in-out;

                    cursor: pointer;

                    .icon {
                        vertical-align: text-bottom;
                    }

                    &:hover {
                        background: #222222;
                    }
                }
            }
        }
    }

    .station-selector-modal .stations-list, .reason-selector-modal .reasons-list {
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

            &:nth-of-type(odd) {
                background: #dddddd;
            }

            i {
                vertical-align: bottom;

            }
        }
    }
</style>
