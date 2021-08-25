<template>
    <div class="card h-100">
        <div class="card-header">
            {{ reportName }}
        </div>

        <ul class=" list-group list-group-flush">
            <li class="list-group-item" @click.prevent="reportTypeChanged('station')" :class="{ active: reportType === 'station' }">
                <a href="#" class="nav-link">
                    Station Wise Report
                </a>
            </li>
            <li class="list-group-item" @click.prevent="reportTypeChanged('product')" :class="{ active: reportType === 'product' }">
                <a href="#" class="nav-link">
                    Product Wise Report
                </a>
            </li>
            <li class="list-group-item" @click.prevent="reportTypeChanged('shift')" :class="{ active: reportType === 'shift' }">
                <a href="#" class="nav-link">
                    Shift Wise Report
                </a>
            </li>
            <li class="list-group-item" @click.prevent="reportTypeChanged('operator')" :class="{ active: reportType === 'operator' }">
                <a href="#" class="nav-link">
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
