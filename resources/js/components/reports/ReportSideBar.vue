<template>
    <div class="card h-100 text-uppercase background-light-blue">
        <div class="card-header justify-content-center">
            {{ reportName }}
        </div>

        <ul class=" list-group list-group-flush">
            <li class="list-group-item background-light-blue" @click.prevent="reportTypeChanged('station')" :class="{ active: reportType === 'station' }">
                <a href="#" class="nav-link text-white">
                    Station Wise Report
                </a>
            </li>
            <li class="list-group-item background-light-blue" @click.prevent="reportTypeChanged('product')" :class="{ active: reportType === 'product' }">
                <a href="#" class="nav-link text-white">
                    Product Wise Report
                </a>
            </li>
            <li class="list-group-item background-light-blue" @click.prevent="reportTypeChanged('shift')" :class="{ active: reportType === 'shift' }">
                <a href="#" class="nav-link text-white">
                    Shift Wise Report
                </a>
            </li>
            <li class="list-group-item background-light-blue" @click.prevent="reportTypeChanged('operator')" :class="{ active: reportType === 'operator' }">
                <a href="#" class="nav-link text-white">
                    Operator Wise Report
                </a>
            </li>
        </ul>

    </div>
</template>

<script>
    export default {
        name: "ReportSideBar",
        // data: () => ({
        //     selectedType: 'station'
        // }),
        props: {
            reportType: {
                type: String,
                default: 'station'
            },
            reportName: {
                type: String,
                default: 'OEE Report',
            }
        },
        methods: {
            reportTypeChanged(reportType) {
                this.$emit('reportTypeChanged', reportType);
            }
        }
    }
</script>
