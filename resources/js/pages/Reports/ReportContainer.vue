<template>
    <div class="justify-content-center align-items-center ps-4">
        <div>
            <div>
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
                console.log('report container emit ' + reportType);
            }
        }
    }
</script>
