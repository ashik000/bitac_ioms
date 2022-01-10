<template>
    <div class="dashboard-bg">
        <div class="gap-5"></div>
        <!--Dashboard body starts-->
        <div class="row main-body">
            <div class="col-md-12">
                <div class="row">
                    <!--Left column starts-->
                    <div class="col-md-4">
                        <div class="card card-style">
                            <div class="card-body text-center">
                                <img src="../assets/Artboard7.png" alt="" />
                                <h5 class="text-uppercase mb-2">
                                    <span>Machine</span>:
                                    <span class="fw-bolder">CM-1</span>
                                </h5>
                            </div>
                        </div>
                        <div class="card card-style mt-4">
                            <div class="card-body text-center">
                                <img src="../assets/Artboard9.png" alt="" />
                                <h6 class="text-uppercase mb-2">
                                    <span>Operator</span>:
                                    <span class="fw-bolder">Md. Hamid Ullah</span>
                                </h6>
                            </div>
                        </div>
                        <div class="card card-style mt-4">
                            <div class="card-body text-center">
                                <img src="../assets/Artboard8.png" alt="" />
                                <h6 class="text-uppercase mb-2">
                                    <span>Total Production: </span>:
                                    <span class="fw-bolder">{{machineStatus.counter_number}} PCS</span>
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
                                    <span>Machine Status:</span>
                                    <div class="round"></div>
                                    <span class="fw-bolder">{{machineStatus.power_status}}</span>
                                </h6>
                            </div>
                        </div>
                    </div>
                    <!--Left column ends-->

                    <!--Middle column starts-->
                    <div class="col-md-4 middle-column">
                        <div class="card card-1 shadow-sm height-420">
                            <div
                                class="card-header d-flex justify-content-between align-items-center"
                            >
                                <div><span class="heading-color">Operation MEM</span></div>
                                <div class="d-flex gap-4 align-items-center">
                                    <img src="../assets/Artboard11.png" alt="" />

                                    <span class="mt-1">{{machineStatus.machine_uptime}}</span>
                                </div>
                            </div>
                            <div
                                class="secondary-header d-flex justify-content-between align-items-center"
                            >
                                <div><span>MEM</span></div>
                                <div class="d-flex gap-5 align-items-center">
                                    <span>...1/gazipur</span>
                                    <span>113NAME.NC</span>
                                    <span>N5870</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
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

                        <div class="card shadow-sm font-12 mt-4 height-300">
                            <div class="card-header card-header-bg text-center fw-bold">
                                Time and Counter
                            </div>
                            <div class="card-body">
                                <table class="table borderless-table">
                                    <tbody class="text-start">
                                    <tr>
                                        <td>This Cycle</td>
                                        <td>:</td>
                                        <td>0:18:07</td>
                                    </tr>
                                    <tr>
                                        <td>This Cycle</td>
                                        <td>:</td>
                                        <td>0:18:07</td>
                                    </tr>
                                    <tr>
                                        <td>This Cycle</td>
                                        <td>:</td>
                                        <td>0:18:07</td>
                                    </tr>
                                    <tr>
                                        <td>This Cycle</td>
                                        <td>:</td>
                                        <td>0:18:07</td>
                                    </tr>
                                    <tr>
                                        <td>This Cycle</td>
                                        <td>:</td>
                                        <td>0:18:07</td>
                                    </tr>
                                    <tr>
                                        <td>This Cycle</td>
                                        <td>:</td>
                                        <td>0:18:07</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--Middle column ends-->

                    <!--Right column starts-->
                    <div class="col-md-4 right-column">
                        <div class="card shadow-sm font-12 card-3 height-420">
                            <div
                                class="card-header card-header-bg text-center fw-bold d-flex"
                            >
                                <span>Positions</span>
                                <span class="ml-120">{{machineStatus.program_name}}</span>
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
                                                <span>-12.742</span>
                                                <input class="width-65" type="range" min="1" max="100" value="" />
                                                <span>6%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mt-4 height-85">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="d-flex gap-5 align-items-center width-100">
                                            <h5 class="mt-1">Y</h5>
                                            <div class="d-flex gap-4 align-items-center slide-range width-90">
                                                <span>-12.742</span>
                                                <input class="width-65" type="range" min="1" max="100" value="" />
                                                <span>6%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mt-4 height-85">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="d-flex gap-5 align-items-center width-100">
                                            <h5 class="mt-1">Z</h5>
                                            <div class="d-flex gap-4 align-items-center slide-range width-90">
                                                <span>-12.742</span>
                                                <input class="width-65" type="range" min="1" max="100" value="" />
                                                <span>6%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card shadow-sm card-4 mt-4 font-12 height-300">
                            <div class="card-header card-header-bg text-center fw-bold">
                                Main Spindle
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <img src="../assets/Artboard10.png" alt="" />
                                        <div><span class="font-14 fw-bold">Overrides</span></div>
                                        <table class="table borderless-table">
                                            <tbody class="text-start">
                                            <tr>
                                                <td>This Cycle</td>
                                                <td>:</td>
                                                <td>0:18:07</td>
                                            </tr>
                                            <tr>
                                                <td>This Cycle</td>
                                                <td>:</td>
                                                <td>0:18:07</td>
                                            </tr>
                                            <tr>
                                                <td>This Cycle</td>
                                                <td>:</td>
                                                <td>0:18:07</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-8">
                                        <table class="table borderless-table">
                                            <tbody class="text-start">
                                            <tr>
                                                <td>Spindle Speed</td>
                                                <td>:</td>
                                                <td>{{machineStatus.spindle_speed}}</td>
                                                <td>RPM</td>
                                            </tr>
                                            <tr>
                                                <td>Spindle Power</td>
                                                <td>:</td>
                                                <td>0.4</td>
                                                <td>KW</td>
                                            </tr>
                                            <tr>
                                                <td>Surface Speed</td>
                                                <td>:</td>
                                                <td>113</td>
                                                <td>Mpm</td>
                                            </tr>
                                            <tr>
                                                <td>Chip Load</td>
                                                <td>:</td>
                                                <td>0,053</td>
                                                <td>MMPT</td>
                                            </tr>
                                            <tr>
                                                <td>Feed Rate</td>
                                                <td>:</td>
                                                <td>{{machineStatus.feed_rate}}</td>
                                                <td>MMPH</td>
                                            </tr>
                                            <tr>
                                                <td>Active Feed</td>
                                                <td>:</td>
                                                <td>960.0000</td>
                                                <td>MMPH</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <hr />
                                <div class="d-flex gap-5 align-items-center slide-range width-100">
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

    data(){
        return{
            machineStatus: [],
            // station_id: '',
            // spindle_speed: '',
            // feed_rate: '',
            // machine_uptime: '',
            // alarm_code: '',
            // alarm_info: '',
            // program_number: '',
            // program_name: '',
            // cycle_time: '',
            // counter_number: '',
            // power_status: '',
            // produced_at: '',
        }
    },

    methods:{
        getData()
        {
            axios.get('/getMachineStatus')
                .then((response)=>{
                    console.log(response)
                    this.machineStatus = response.data
                })
        }
    },

    created: function (){
        this.getData()
        setInterval(() => {this.getData()}, 5000)
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
    background: #00dd20;
    border-radius: 50%;
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
    border-style: none;
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
.dashboard-bg{
    background: #f4faff;
    height: 100vh;
}
.width-100{
    width: 100%;
}
.width-90{
    width: 90%!important;
}
.width-65{
    width: 65%!important;
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
/* slider style ends */
</style>
