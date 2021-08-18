<template>
    <div class="card">
        <div class="card-header">
            Oee Report
        </div>

        <ul class="partition-picker list-group list-group-flush">
            <li class="list-group-item" @click.prevent="reportTypeChanged('station')" :class="{ active: selectedType === 'station' }">
                <a href="#" class="nav-link">
                    Station Wise Report
                </a>
            </li>
            <li class="list-group-item" @click.prevent="reportTypeChanged('product')" :class="{ active: selectedType === 'product' }">
                <a href="#" class="nav-link">
                    Product Wise Report
                </a>
            </li>
            <li class="list-group-item" @click.prevent="reportTypeChanged('shift')" :class="{ active: selectedType === 'shift' }">
                <a href="#" class="nav-link">
                    Shift Wise Report
                </a>
            </li>
            <li class="list-group-item" @click.prevent="reportTypeChanged('operator')" :class="{ active: selectedType === 'operator' }">
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
