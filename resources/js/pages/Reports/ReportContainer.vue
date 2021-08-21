<template>
    <div class="justify-content-center align-items-center" style="margin-top:20px; display: flex; flex-direction: row; ">
        <div style="width: 100%">
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
                console.log('report container emit ' + reportType);
            }
        }
    }
</script>
