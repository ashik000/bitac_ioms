<template>
    <nav class="navbar navbar-expand-lg navbar-light bg-theme">
            <div class="report-type-picker">
                <div class="btn-group">
                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" @click.prevent="doNothing">
                        Reports
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#"
                           @click.prevent="$emit('reportMenuItemSelected', 'oeeReport')">OEE</a>
                        <a class="dropdown-item" href="#"ReportMenuItem
                           @click.prevent="$emit('reportMenuItemSelected', 'downtimeReport')">Downtime</a>
<!--                        <a class="dropdown-item" href="#"-->
<!--                           @click.prevent="$emit('reportMenuItemSelected', 'qualityReport')">Quality</a>-->
<!--                        <a class="dropdown-item" href="#"-->
<!--                           @click.prevent="$emit('reportMenuItemSelected', 'timeUsageReport')">Time Usage</a>-->
<!--                        <a class="dropdown-item" href="#"-->
<!--                           @click.prevent="$emit('reportMenuItemSelected', 'quantitiesReport')">Quantities</a>-->
<!--                        <a class="dropdown-item" href="#"-->
<!--                           @click.prevent="$emit('reportMenuItemSelected', 'cycleTimeReport')">Cycle Time</a>-->
<!--                        <a class="dropdown-item" href="#"-->
<!--                           @click.prevent="$emit('reportMenuItemSelected', 'scrapReasonsReport')">Scrap Reasons</a>-->
                    </div>
                </div>
            </div>
    </nav>
</template>

<script>
    export default {
        name: "ReportMenuItem",
        methods: {
            doNothing: function () {
                return false;
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "#/app.scss";

    .navbar {
        /*background: #222056;*/
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
                    color: #D71921;
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

