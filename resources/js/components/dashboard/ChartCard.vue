<template>
    <div class="col-md-3 mb-2">
        <div class="card shadow rounded-2 mx-2">
            <div v-if="summaryData" :class="getBackGroundColor" class="card-body">
                <div class="flex-container">
                    <h6 class="" style="color: white">{{this.summaryData.stationName}}</h6>
                    <h6 class="" style="color: white">{{this.summaryData.oee}}%</h6>
                </div>
                <div class="flex-container">
                    <div class="progress-bar-wrapper">
                        <div class="progress-bar">
                            <span class="progress-bar-fill" style="width: 70%;"></span>
                        </div>
                    </div>
                    <h6 class="" style="color: white">{{this.summaryData.performance}}%</h6>
                </div>
                <div class="flex-container">
                    <speed-chart :title="title" :dataset="summaryData"></speed-chart>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-center card-body" style="background-color: #34403A; color: #FFFFFF; height: 18rem" v-else>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row" style="font-size: 1.2rem">
                            <span>
                                Stations: <span style="font-size: 1.6rem; font-weight: 600;">{{this.stationC ? this.stationC : 'N/A'}}</span>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <img src="storage/images/walton-logo.png" alt="walton-logo" style="height: 40px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ChartCard",
    props: {
        summaryData: {
            type: Object,
            required: false
        },
        stationC: {
            type: Number,
            required: false
        },
    },
    components: {
    },
    data: () => ({
      title: "Speed Chart",
    }),
    methods: {

    },
    computed: {
        getBackGroundColor() {
            switch (this.summaryData.color ) {
                case 'red':
                    return 'bg-danger';
                case 'green':
                    return 'bg-success';
            }
            return 'bg-dark';
        }
    },
    mounted() {
      console.log(this.stationC);
    }
}
</script>
