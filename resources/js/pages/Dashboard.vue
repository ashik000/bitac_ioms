<template>
    <div class="dashboard-bg">
        <div class="mt-3"></div>
        <!--Dashboard body starts-->
        <div class="row main-body">
            <div class="col-md-12">
                <div class="row">
                    <!--Left column starts-->
                    <div class="col-md-4">
                        <div class="card card-style">
                            <div class="card-body text-center">
                                <img src="../assets/Artboard7.png" alt=""/>
                                <h5 class="text-uppercase mb-2">
                                    <span>Machine</span>:
                                    <span class="fw-bolder" style="color: #00dd20">CM-1</span>
                                </h5>
                            </div>
                        </div>
                        <div class="card card-style mt-4">
                            <div class="card-body text-center">
                                <img src="../assets/Artboard9.png" alt=""/>
                                <h6 class="text-uppercase mb-2">
                                    <span>Operator</span>:
                                    <span class="fw-bolder">{{ operatorName }}</span>
                                </h6>
                            </div>
                        </div>
                        <div class="card card-style mt-4">
                            <div class="card-body text-center">
                                <img src="../assets/Artboard8.png" alt=""/>
                                <h6 class="text-uppercase mb-2">
                                    <span>Total Production: </span>:
                                    <span><span class="fw-bolder">{{ machineStatus.production_counter2 }}</span> pcs</span>
                                </h6>
                            </div>
                        </div>
                        <div class="card card-style mt-4">
                            <div
                                class="card-body text-center d-flex justify-content-center align-items-center"
                            >
                                <h6
                                    class="text-uppercase mb-2 d-flex justify-content-center align-items-center gap-3"
                                >
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span>Machine Status:</span>
                                            <div class="round" v-bind:class="{roundGreen, roundRed}"></div>
                                            <span class="fw-bolder">{{ machineStatus.power_status }}</span><br>
                                        </div>

                                        <div v-if="isAlarm" class="col-md-12" style="margin-top: 10px">
                                            <span class="fw-bolder text-danger">Alarm:</span>
                                            <span class="fw-bolder text-danger">{{ machineStatus.alarm_info}}</span>
                                        </div>
                                    </div>
                                </h6>
                            </div>
                        </div>
                    </div>
                    <!--Left column ends-->

                    <!--Middle column starts-->
                    <div class="col-md-4 middle-column">
                        <div class="card card-style font-12  height-300">
                            <div class="card-header card-header-bg text-center fw-bold">
                                Time and Counter
                            </div>
                            <div class="card-body">
                                <table class="table borderless-table table-responsive">
                                    <tbody class="text-start">
                                    <tr>
                                        <td>This Cycle</td>
                                        <td>:</td>
                                        <td>{{ machineStatus.cycle_time }}</td>
                                    </tr>
                                    <tr>
                                        <td>Last Cycle</td>
                                        <td>:</td>
                                        <td>--</td>
                                    </tr>
                                    <tr>
                                        <td>Remaining</td>
                                        <td>:</td>
                                        <td>--</td>
                                    </tr>
                                    <tr>
                                        <td>M30 Counter #1</td>
                                        <td>:</td>
                                        <td>{{ machineStatus.production_counter1 }}</td>
                                    </tr>
                                    <tr>
                                        <td>M30 Counter #2</td>
                                        <td>:</td>
                                        <td>{{ machineStatus.production_counter2 }}</td>
                                    </tr>
                                    <tr>
                                        <td>Loops Remaining</td>
                                        <td>:</td>
                                        <td>--</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card card-style card-1 mt-4 height-420">
                            <div
                                class="card-header d-flex justify-content-between align-items-center"
                            >
                                <div><span class="heading-color">Operation MEM</span></div>
                                <div class="d-flex gap-4 align-items-center">
                                    <img src="../assets/Artboard11.png" alt=""/>

                                    <span class="mt-1 text-dark"
                                          style="font-family: 'Cascadia Code'">{{ machineStatus.machine_uptime }}</span>
                                </div>
                            </div>
                            <div
                                class="secondary-header d-flex justify-content-between align-items-center"
                            >
                                <div><span v-bind:class="{programStatus}">Program:</span></div>
                                <div class="d-flex align-items-center">
                                    <span class="ml-120" v-bind:class="{programStatus}">{{ machineStatus.program_name }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table borderless-table table-responsive">
                                    <tbody class="text-start">
                                    <tr>
                                        <td>N5830X11, 707Y-20,8</td>
                                    </tr>
                                    <tr>
                                        <td>N5830X11, 707Y-20,8</td>
                                    </tr>
                                    <tr>
                                        <td>N5830X11, 707Y-20,8</td>
                                    </tr>
                                    <tr>
                                        <td>N5830X11, 707Y-20,8</td>
                                    </tr>
                                    <tr>
                                        <td>N5830X11, 707Y-20,8</td>
                                    </tr>
                                    <tr>
                                        <td>N5830X11, 707Y-20,8</td>
                                    </tr>
                                    <tr>
                                        <td>N5830X11, 707Y-20,8</td>
                                    </tr>
                                    <tr>
                                        <td>N5830X11, 707Y-20,8</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--Middle column ends-->

                    <!--Right column starts-->
                    <div class="col-md-4 right-column">

                        <div class="card card-style card-4 font-12 height-300">
                            <div class="card-header card-header-bg text-center fw-bold">
                                Main Spindle
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div style="text-align: center">
                                            <img src="../assets/Artboard10.png" alt=""/>
                                            <div><span class="font-14 fw-bold">Overrides</span></div>
                                        </div>
                                        <table class="table borderless-table table-responsive">
                                            <tbody class="text-start">
                                            <tr>
                                                <td>Spindle</td>
                                                <td>:</td>
                                                <td>--</td>
                                            </tr>
                                            <tr>
                                                <td>Feed</td>
                                                <td>:</td>
                                                <td>--</td>
                                            </tr>
                                            <tr>
                                                <td>Rapid</td>
                                                <td>:</td>
                                                <td>--</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-8" style="border-left:1px dotted #606f7b">
                                        <table class="table borderless-table table-responsive">
                                            <thead></thead>
                                            <tbody class="text-start">
                                            <tr>
                                                <td>Spindle Speed</td>
                                                <td>:</td>
                                                <td>{{ parseFloat(machineStatus.spindle_speed).toFixed(2) }}</td>
                                                <td>RPM</td>
                                            </tr>
                                            <tr>
                                                <td>Spindle Power</td>
                                                <td>:</td>
                                                <td>--</td>
                                                <td>KW</td>
                                            </tr>
                                            <tr>
                                                <td>Surface Speed</td>
                                                <td>:</td>
                                                <td>--</td>
                                                <td>Mpm</td>
                                            </tr>
                                            <tr>
                                                <td>Chip Load</td>
                                                <td>:</td>
                                                <td>--</td>
                                                <td>MMPT</td>
                                            </tr>
                                            <tr>
                                                <td>total Feed Time</td>
                                                <td>:</td>
                                                <td>{{ machineStatus.feed_rate }}</td>
                                                <td>MMPH</td>
                                            </tr>
                                            <tr>
                                                <td>Active Feed</td>
                                                <td>:</td>
                                                <td>--</td>
                                                <td>MMPH</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="d-flex gap-5 align-items-center slide-range mt-3">
                                    <span>Spindle Load(%)</span>
                                    <input
                                        type="range"
                                        min="1"
                                        max="100"
                                        value=""
                                        style="width: 70%; height: 15px"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="card card-style font-12 mt-4 card-3 height-420">
                            <div
                                class="card-header card-header-bg text-center fw-bold d-flex"
                            >
                                <span>Positions</span>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between fw-bold">
                                    <span class="mm">(MM)</span>
                                    <span class="load">Load</span>
                                </div>
                                <div class="card mt-1 height-85">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="d-flex gap-5 align-items-center width-100">
                                            <h5 class="mt-1">X</h5>
                                            <div class="d-flex gap-4 align-items-center slide-range width-90">
                                                <span>--</span>
                                                <input class="width-65" type="range" min="1" max="100" value=""/>
                                                <span>-%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mt-4 height-85">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="d-flex gap-5 align-items-center width-100">
                                            <h5 class="mt-1">Y</h5>
                                            <div class="d-flex gap-4 align-items-center slide-range width-90">
                                                <span>--</span>
                                                <input class="width-65" type="range" min="1" max="100" value=""/>
                                                <span>-%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mt-4 height-85">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="d-flex gap-5 align-items-center width-100">
                                            <h5 class="mt-1">Z</h5>
                                            <div class="d-flex gap-4 align-items-center slide-range width-90">
                                                <span>--</span>
                                                <input class="width-65" type="range" min="1" max="100" value=""/>
                                                <span>-%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Right column ends-->
                </div>
            </div>
        </div>
        <!--Dashboard body ends-->
    </div>

</template>

<script>

export default {
    name: "Dashboard",

    data() {
        return {
            machineStatus: [],
            operatorName: '',
            roundGreen: false,
            roundRed: true,
            isAlarm: false,
            programStatus: false,
        }
    },

    methods: {
        getMachineData() {
            axios.get('getMachineStatus')
                .then((response) => {
                    this.programStatus = response.data.programStatus
                    this.machineStatus = response.data.machineStatus
                    console.log(this.programStatus)
                    if (this.machineStatus.power_status === 'STOPPED') {
                        this.roundGreen = false
                        this.roundRed = true
                    } else {
                        this.roundRed = false
                        this.roundGreen = true
                    }

                    if(this.machineStatus.alarm_info === 'NULL' || this.machineStatus.alarm_info === 'NO ACTIVE ALARMS')
                    {
                        this.isAlarm = false
                    }
                    else {
                        this.isAlarm = true
                    }
                })
        },

        getOperator() {
            let currentDate = new Date().toJSON().slice(0, 10).replace(/-/g, '/');
            axios.get('getOperatorName', {
                params: {
                    stationId: 7,
                    date: currentDate
                }
            })

                .then((response) => {
                    this.operatorName = response.data.operatorName
                })
        }
    },

    created: function () {
        this.getOperator()
        this.getMachineData()

        setInterval(() => {
            this.getOperator()
            this.getMachineData()
        }, 10000)
    },
}
</script>

<style>
.bitac-logo img {
    height: 34px;
    width: 105px;
}

.nav-bar {
    height: 55px;
    width: 100%;
    background-image: linear-gradient(to right, #00c595 5%, #0090fe 80%);
    padding: 12px 30px;
    color: white;
}

.main-nav {
    margin-left: 150px;
}

.main-nav a {
    text-decoration: none;
    font-size: 12px;
    color: white;
}

.time {
    font-size: 22px;
}

.main-body {
    margin: 25px;
}

.card-margin-top {
    margin-top: 25px;
}

.card-style {
    border: 1px solid #bbe7ff;
    border-radius: 0px;
    min-height: 181px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 2px 4px 0 rgba(0, 0, 0, 0.19);
}

.shadow-cus {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 2px 4px 0 rgba(0, 0, 0, 0.19);
}

.main-body .card-body img {
    height: 50px;
    width: 48px;
    margin-top: 10px;
    margin-bottom: 15px;
}

.round {
    display: inline-block;
    height: 20px;
    width: 20px;
    border-radius: 50%;
}

.roundGreen {
    background: #00dd20;
}

.roundRed {
    background: #FF0D0D;
}

.programStatus {
    font-weight: bolder;
    color: #FF0D0D;
}

.middle-column .card {
    border: none;
}

.right-column .card-3,
.right-column .card-4 {
    border: none;
}

.card-1 .card-header {
    background: white;
}

.card-1 .card-body {
    padding: 10px 25px 10px 25px;
    font-size: 14px;
}

.card-3.card-body {
    padding: 10px 25px 25px 25px;
}

.heading-color {
    color: #00c594;
}

.secondary-header {
    height: 40px;
    width: 100%;
    background: #2e25a5;
    color: white;
    font-size: 15px;
    padding: 0px 30px;
}

.card-header-bg {
    background: #10306c;
    color: white;
}

.font-12 {
    font-size: 12px;
}

.font-14 {
    font-size: 14px;
}

.borderless-table tr td {
    border-style: hidden !important;
}

.ml-120 {
    margin-left: 120px;
}

.mm {
    margin-left: 85px;
}

.load {
    margin-right: 68px;
}

.height-85 {
    height: 85px;
}

.height-420 {
    height: 420px;
}

.height-300 {
    height: 350px;
}

.dashboard-bg {
    background: #f4faff;
    /*height: 100vh;*/
    margin: 12px;
}

.width-100 {
    width: 100%;
}

.width-90 {
    width: 90% !important;
}

.width-65 {
    width: 65% !important;
}

/* slider style starts */
.slide-range input[type='range'] {
    -webkit-appearance: none;
    margin: 10px 0;
    /* width: 300px; */
}

.slide-range input[type='range']:focus {
    outline: none;
}

.slide-range input[type='range']::-webkit-slider-runnable-track {
    width: 250px;
    height: 20px;
    cursor: pointer;
    box-shadow: 0px 0px 0px #000000, 0px 0px 0px #0d0d0d;
    background-image: linear-gradient(
        to right,
        #00c594 20%,
        #ffa400,
        #ff0000 85%
    );
}

.slide-range input[type='range']::-webkit-slider-thumb {
    box-shadow: 0px 0px 0px #000000, 0px 0px 0px #0d0d0d;
    height: 30px;
    width: 8px;
    border-radius: 5px;
    background: #008dff;
    cursor: pointer;
    -webkit-appearance: none;
    margin-top: -5px;
}

table {
    font-size: 13px;
    font-family: "Cascadia Code";
}

/* slider style ends */
</style>
