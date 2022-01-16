<template>

    <div class="p-2">
        <div class="chart-wrap">
            <canvas width="100%" height="200px" ref="chartElement"/>
        </div>
    </div>
</template>

<script>
import Chart from 'chart.js';
import {clone} from "../../utils";
export default {
    name: "SpeedChart"

}
</script>

<style scoped>

</style>
