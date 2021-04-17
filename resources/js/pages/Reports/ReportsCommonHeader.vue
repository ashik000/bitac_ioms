<template>

    <div>
        <nav class="navbar navbar-expand-lg navbar-light bg-theme">
            <div class="container-fluid">
                <div class="report-type-picker">
                    {{ reportName }}
                </div>

                <ul class="partition-picker" v-if="showPartition">
                    <li class="nav-item" :class="{ active: partition === 'hourly' }"
                        @click.prevent="partitionChanged('hourly')">
                        <a href="#" class="nav-link">
                            Hourly
                        </a>
                    </li>
                    <li class="nav-item" :class="{ active: partition === 'daily' }"
                        @click.prevent="partitionChanged('daily')">
                        <a href="#" class="nav-link">
                            Daily
                        </a>
                    </li>
                    <li class="nav-item" :class="{ active: partition === 'weekly' }"
                        @click.prevent="partitionChanged('weekly')">
                        <a href="#" class="nav-link">
                            Weekly
                        </a>
                    </li>
                    <li class="nav-item" :class="{ active: partition === 'monthly' }"
                        @click.prevent="partitionChanged('monthly')">
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
    </div>
</template>

<script>
    import moment from 'moment';

    export default {
        name: "ReportsCommonHeader",
        props: {
            reportName: {
                type: String,
                default: 'OEE Report',
            },
            showPartition: {
                type: Boolean,
                default: true
            }
        },
        data: () => ({
            selectedRange: {
                tag: 'today',
                title: 'Today'
            },
            partition: 'hourly',
            range: {
                start: moment().startOf('day').toDate(),
                end: moment().endOf('day').toDate()
            },
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
                this.$emit('rangeSelected', range);
            },
            partitionChanged(p){
                this.partition = p;
                return this.$emit('partitionSelected', p);
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "#/app.scss";

    .navbar {
        background: #222056;

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

            &:hover {
                .nav-link {
                    color: #999999;
                }
            }
        }
    }

    .report-type-picker {
        color: #337ab7;

        &:hover {
            color: #999999;
        }

        &.show {
            color: #dddddd;
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

