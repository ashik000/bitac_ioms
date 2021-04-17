<template>
    <nav class="navbar navbar-expand-lg navbar-light bg-theme">
        <div class="container-fluid">
            <div class="report-type-picker">
                <div class="btn-group">
                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        {{ reportTitle(report) }}
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#"
                           @click.prevent="$emit('report-selected', 'oee')">OEE</a>
                        <a class="dropdown-item" href="#"
                           @click.prevent="$emit('report-selected', 'quality')">Quality</a>
                        <a class="dropdown-item" href="#"
                           @click.prevent="$emit('report-selected', 'availability')">Availability</a>
                        <a class="dropdown-item" href="#"
                           @click.prevent="$emit('report-selected', 'downtime')">Downtime</a>
                    </div>
                </div>
            </div>

            <ul class="partition-picker">
                <li class="nav-item" :class="{ active: partition === 'hourly' }"
                    @click.prevent="$emit('partition-selected', 'hourly')">
                    <a href="#" class="nav-link">
                        Hourly
                    </a>
                </li>
                <li class="nav-item" :class="{ active: partition === 'daily' }"
                    @click.prevent="$emit('partition-selected', 'daily')">
                    <a href="#" class="nav-link">
                        Daily
                    </a>
                </li>
                <li class="nav-item" :class="{ active: partition === 'weekly' }"
                    @click.prevent="$emit('partition-selected', 'weekly')">
                    <a href="#" class="nav-link">
                        Weekly
                    </a>
                </li>
                <li class="nav-item" :class="{ active: partition === 'monthly' }"
                    @click.prevent="$emit('partition-selected', 'monthly')">
                    <a href="#" class="nav-link">
                        Monthly
                    </a>
                </li>
            </ul>

            <div class="date-range-picker-wrap">
                <span class="range-picker-label">
                    Date Range
                </span>
                <v-date-picker class="date-range-picker" v-show="selectedRange.tag === 'custom'"
                               :input-props="{ style: `
                                        background-color: #0f0e26;
                                        color: #dddddd;
                                        padding: 0.7rem;
                                        border-radius: 0.25rem;
                                        margin: 0.1rem 0;
                                        width: 200px;
                                        text-align: center;
                                        cursor: pointer;
                                    `
                               }"
                               mode='range'
                               :value='range'
                               @input="onDateRangeChanged"/>

                <div class="btn-group btn-group-lg" role="group" aria-label="">
                    <div class="btn-group date-shortcut" role="group">
                        <button id="date-shortcut" type="button"
                                class="btn dropdown-toggle"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                                style="background-color: #0f0e26;
                                        border: 1px solid #ffffff;
                                        color: #dddddd;
                                        padding: 0.45rem;
                                        border-radius: 0.25rem;
                                        margin: 0.1rem;
                                        cursor: pointer;"
                        >
                            {{ selectedRange.title }}
                        </button>
                        <div class="dropdown-menu" aria-labelledby="date-shortcut">
                            <a href="#" class="dropdown-item"
                               v-for="item in rangeSelections" @click="changeSelectedRange(item)">
                                {{ item.title }}
                            </a>

                            <a href="#" class="dropdown-item"
                               @click="changeSelectedRange({ tag: 'custom', title: 'Custom' })">
                                Custom
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
    import moment from 'moment';

    export default {
        name: "ReportPicker",
        props: {
            report: {
                type: String,
                default: 'oee',
            },
            partition: {
                type: String,
                default: 'hourly',
            },
            range: {
                type: Object,
            },
        },
        data: () => ({
            selectedRange: {
                tag: 'custom',
                title: 'Custom'
            }
        }),
        computed: {
            rangeSelections() {
                switch (this.partition) {
                    case 'hourly':
                        return [
                            {
                                tag: 'today',
                                title: 'Today'
                            },
                            {
                                tag: 'yesterday',
                                title: 'Yesterday'
                            },
                            {
                                tag: 'this_week',
                                title: 'This Week'
                            },
                            {
                                tag: 'last_week',
                                title: 'Last Week'
                            }
                        ];

                    case 'daily':
                        return [
                            {
                                tag: 'this_week',
                                title: 'This Week'
                            },
                            {
                                tag: 'last_week',
                                title: 'Last Week'
                            }
                        ]
                }
            }
        },
        methods: {
            reportTitle(report) {
                switch (report) {
                    case 'oee':
                        return 'OEE';
                    case 'quality':
                        return 'Quality';
                    case 'availability':
                        return 'Availability';
                    case 'downtime':
                        return 'Downtime';
                }
            },
            changeSelectedRange(item) {
                this.selectedRange = item;

                switch (item.tag) {
                    case 'today':
                        this.range.start = moment().startOf('day').toDate();
                        this.range.end = moment().endOf('day').toDate();
                        break;

                    case 'yesterday':
                        this.range.start = moment().subtract(1, "days").startOf('day').toDate();
                        this.range.end = moment().subtract(1, "days").endOf('day').toDate();
                        break;

                    case 'this_week':
                        this.range.start = moment().startOf('week').toDate();
                        this.range.end = moment().endOf('week').toDate();
                        break;

                    case 'last_week':
                        this.range.start = moment().startOf('week').subtract(7, "days").toDate();
                        this.range.end = moment().endOf('week').subtract(7, "days").toDate();
                        break;
                }

                this.onDateRangeChanged(this.range)
            },
            onDateRangeChanged(range) {
                this.$emit('range-selected', range);
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "#/app.scss";

    .navbar {
        background: #222056;
        padding: 0 !important;

        .nav-item {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;

            .nav-link {
                color: #337ab7;
            }

            &.active {
                padding-bottom: 0.25rem;
                border-bottom: 0.10rem solid #dddddd;

                .nav-link {
                    color: #dddddd;
                }
            }
        }
    }

    .report-type-picker {
        @extend .d-flex, .flex-row, .w-25;

        .btn-group {
            .btn {
                color: #337ab7;

                &:hover {
                    color: rgba(0, 0, 0, 0.7);
                }
            }

            &.show {
                .btn {
                    color: #dddddd;
                }
            }
        }
    }

    .partition-picker {
        @extend .navbar-nav, .flex-grow-1, .justify-content-center;
    }

    .date-range-picker-wrap {
        @extend .d-flex, .flex-row, .justify-content-end, .align-items-center, .w-25;

        .range-picker-label {
            color: #dddddd;
            padding: 0.5rem 1rem;
            margin: 0 0.5rem;
            white-space: nowrap;
        }

        .date-range-picker.vc-reset {
            flex: 1;

            .__date-picker-input {
            }
        }

        .date-shortcut {
            .dropdown-menu {
                left: auto;
                right: 0;
            }
        }
    }
</style>

