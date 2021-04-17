<template>
    <div class="justify-content-center align-items-center" style="margin-top:20px; display: flex; flex-direction: row; ">
        <div style="width: 86%">
            <nav class="navbar navbar-expand-lg navbar-light bg-theme nav-fill">
                <ul class="partition-picker">
                    <li class="nav-item" @click.prevent="reportTypeChanged('station')" :class="{ active: selectedType === 'station' }">
                        <a href="#" class="nav-link">
                            Station Wise Report
                        </a>
                    </li>
                    <li class="nav-item" @click.prevent="reportTypeChanged('product')" :class="{ active: selectedType === 'product' }">
                        <a href="#" class="nav-link">
                            Product Wise Report
                        </a>
                    </li>
                    <li class="nav-item" @click.prevent="reportTypeChanged('shift')" :class="{ active: selectedType === 'shift' }">
                        <a href="#" class="nav-link">
                            Shift Wise Report
                        </a>
                    </li>
                    <li class="nav-item" @click.prevent="reportTypeChanged('operator')" :class="{ active: selectedType === 'operator' }">
                        <a href="#" class="nav-link">
                            Operator Wise Report
                        </a>
                    </li>
                </ul>
            </nav>
            <div style="background-color:#0F0D26">
                <slot name="reportContainer"></slot>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "ReportContainer",
        data: () => ({
            selectedType: 'station'
        }),
        methods: {
            reportTypeChanged(reportType) {
                this.selectedType = reportType;
                this.$emit('reportTypeChanged', reportType);
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "#/app.scss";

    .navbar {
        background-color: #cccccc;
        padding: 0px!important;
        .nav-item {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            color: #dddddd;
            transition: background-color 100ms linear;
            &:hover{
                background-color: #d6d6d6;
            }

            .nav-link {
                color: #666666;
            }

            &.active {
                padding-bottom: 0.05rem;
                border-bottom: 0.35rem solid #0B4DA2;
                background-color: #d6d6d6;
                .nav-link {
                    color: #222222;
                }
            }
        }
    }

    .partition-picker {
        @extend .navbar-nav, .flex-grow-1, .justify-content-center;
    }

</style>

