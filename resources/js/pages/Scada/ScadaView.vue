<template>
    <div class="container-fluid wrapper" style="top: 4em;">
        <div class="card-wrapper row">
            <div class="col-12 h-100">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <div>SCADA View</div>
                        <div class="d-flex">
                            <div class="input-group remove-width">
                                <select class="form-select" aria-label="Filter Select" v-model="filter" @change="getScadaData">
                                    <option value="" selected disabled>Select Filter</option>
                                    <option value="last_hour">Last hour</option>
                                    <option value="current_shift">Current shift</option>
                                    <option value="last_shift">Last shift</option>
                                    <option value="last_month">Last month</option>
                                    <option value="custom">Custom</option>
                                </select>
<!--                                <input type="text" class="form-control" placeholder="Search" aria-label="Stations search" aria-describedby="Stations search">-->
<!--                                <button class="btn transparent-search-button" type="button">-->
<!--                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="bi bi-search" viewBox="0 0 16 16">-->
<!--                                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>-->
<!--                                    </svg>-->
<!--                                </button>-->
                            </div>
                        </div>
                    </div>
                    <div class="card-body y-scroll">
                        <div class="scada-container mb-4">
                            <div class="d-block text-center"><h3 class="text-center btn btn-primary">Indoor Assembly Line</h3></div>

                            <div class="belt"></div>
                            <div class="connect-line" style="left: -9.6%;"></div>
                            <div class="connect-line lc3" style="left: 50%;left: 32.6%;border-left: 0;border-right: 3px solid #E8B900;"></div>

<!--                            <div class="straight-connect-line" style="left: 14%"></div>-->

                            <div class="connect-line lc2" style="left: 55.6%; border-left: 0px; border-right: 3px solid #E8B900;"></div>

                            <div class="connect-line lc" style="left: 83.6%; border-left: 0px; border-right: 3px solid #E8B900;"></div>

                            <div class="bubble" style="left: 1%;">
                                <router-link :to="generateStationId(scadaData[5] ? scadaData[5].station_id : 0)" style="text-decoration: none; color: white;">
                                    <div style="background-color: #6D6D6D;" class="rounded-top p-1">
                                        OEE-{{ scadaData[5] ? scadaData[5].oee : 0}}%
                                    </div>
                                </router-link>

                                <router-link :to="generateStationId(scadaData[5] ? scadaData[5].station_id : 0)" style="text-decoration: none; color: white;">
                                  <div style="background-color: #00C853;" class="rounded-bottom p-1">
                                    PER-{{ scadaData[5] ? scadaData[5].performance : 0}}/100
                                  </div>
                                </router-link>

                            </div>
                            <div class="prod-line inovace-sensor pro-1" style="left: 1%;"></div>
                            <div class="product ac-boxed" style="left: 4%; height: 80px; width: 80px;"></div>
<!--                            <div class="bubble" style="left: 12%;">-->
<!--                                <div style="background-color: gray;">-->
<!--                                    OEE-80%-->
<!--                                </div>-->
<!--                                <div style="background-color: red;">-->
<!--                                    PER-90/100-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="product computer" style="left: 10%; bottom: 46%; white-space: nowrap;">-->
<!--                                <div class="packaging-bubble bottom-arrow">Packaging</div>-->
<!--                            </div>-->

                            <div class="outdoor" style="background-color: rgba(229, 243, 255, 1); display: inline-block;position: absolute;bottom: 1.5rem;height: 279px;left: 11%;width: 100px;">
                                <div class="outdoor-bubbles">
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Quality 0%</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Reject 0</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Pass 0</div>

                                    <div class="computer" style="height: 30px;width: 30px;background-repeat: no-repeat;background-size: 100% 100%;"></div>
                                    <div>Outdoor <br> Leek Test-2</div>
                                </div>
                            </div>

                            <div class="ac-naked" style="height: 106px; width: 106px;background-size: 100%; background-repeat: no-repeat;    position: absolute;bottom: -1rem;left: 19.5%;"></div>

                            <div class="outdoor" style="background-color: rgba(229, 243, 255, 1); display: inline-block;position: absolute;bottom: 1.5rem;height: 279px;left: 28.5%;width: 100px;">
                                <div class="outdoor-bubbles">
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">OEE 0%</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Per 0/100</div>

                                    <div class="computer" style="height: 30px;width: 30px;background-repeat: no-repeat;background-size: 100% 100%;"></div>
                                    <div>Outdoor <br> Aging Test</div>
                                </div>
                            </div>

                            <div class="ac-naked" style="height: 106px; width: 106px;background-size: 100%; background-repeat: no-repeat; position: absolute;bottom: -1rem;left: 37%;"></div>

                            <div class="prod-line inovace-sensor pro-2" style="left: 40.6%;"></div>

                            <div class="bubble lb3" style="left: 37%">
                                <router-link :to="generateStationId(scadaData[4] ? scadaData[4].station_id : 0)" style="text-decoration: none; color: white;">
                                    <div style="background-color: #6D6D6D;" class="rounded-top p-1">
                                        OEE-{{scadaData[4] ? scadaData[4].oee : 0}}%
                                    </div>
                                </router-link>
                                <router-link :to="generateStationId(scadaData[4] ? scadaData[4].station_id : 0)" style="text-decoration: none; color: white;">
                                  <div style="background-color: #00C853;" class="rounded-bottom p-1">
                                    PER-{{ scadaData[4] ? scadaData[4].performance : 0}}/100
                                  </div>
                                </router-link>
                            </div>

                            <div class="outdoor" style="background-color: rgba(229, 243, 255, 1); display: inline-block;position: absolute;bottom: 1.5rem;height: 279px;left: 46%;width: 100px;">
                                <div class="outdoor-bubbles">
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Quality 0%</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Reject 0</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Pass 0</div>

                                    <div class="computer" style="height: 30px;width: 30px;background-repeat: no-repeat;background-size: 100% 100%;"></div>
                                    <div>Outdoor <br> Leek Test-1</div>
                                </div>
                            </div>

                            <div class="station-1" style="position: absolute;bottom: 1.5em;left: 55%;"></div>

                            <div class="station-2" style="position: absolute;bottom: 1.5em;left: 58%; white-space: nowrap;">
                                <div class="packaging-bubble bottom-arrow" style="right: -25px;">Gas Charge</div>
                            </div>

                            <div class="bubble lb2" style="left: 60%;">
                              <router-link :to="generateStationId(scadaData[3] ? scadaData[3].station_id : 0)" style="text-decoration: none; color: white;">
                                  <div style="background-color: #6D6D6D;" class="rounded-top p-1">
                                      OEE-{{ scadaData[3] ? scadaData[3].oee : 0}}%
                                  </div>
                              </router-link>
                              <router-link :to="generateStationId(scadaData[3] ? scadaData[3].station_id : 0)" style="text-decoration: none; color: white;">
                                <div style="background-color: #00C853;" class="rounded-bottom p-1">
                                  PER-{{ scadaData[3] ? scadaData[3].performance : 0}}/100
                                </div>
                              </router-link>
                            </div>

                            <div class="station-3" style="position: absolute;bottom: 1.5em;left: 61%;"></div>

                            <div class="prod-line inovace-sensor pro-3" style="left: 63.6%;"></div>

                            <div class="ac-introlly" style="height: 106px; width: 106px; background-size: 100%; background-repeat: no-repeat; position: absolute; bottom: -1.4rem; left: 65.4%;"></div>

                            <div class="outdoor" style="background-color: rgba(229, 243, 255, 1); display: inline-block;position: absolute;bottom: 1.5rem;height: 279px;left: 73%;width: 100px;">
                                <div class="outdoor-bubbles">
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Quality 0%</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Reject 0</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Pass 0</div>

                                    <div class="computer" style="height: 30px;width: 30px;background-repeat: no-repeat;background-size: 100% 100%;"></div>
                                    <div>Outdoor <br> Helium Leak Test</div>
                                </div>
                            </div>

                            <div class="station-4" style="position: absolute;bottom: 1.5em;left: 80.4%;"></div>

                            <div class="introlly" style="height: 106px; width: 106px; background-size: 100%; background-repeat: no-repeat; position: absolute; bottom: -1.4rem; left: 83%;"></div>

                            <div class="trolly" style="height: 106px; width: 106px; background-size: 100%; background-repeat: no-repeat; position: absolute; bottom: -1.4rem; left: 93%;"></div>

                            <div class="prod-line inovace-sensor pro-4" style="left: 91.6%;"></div>
                            <div class="bubble rounded-2 lb" style="left: 88%;">
                              <router-link :to="generateStationId(scadaData[1] ? scadaData[1].station_id: 0)" style="text-decoration: none; color: white;">
                                <div style="background-color: #6D6D6D;" class="rounded-top p-1">
                                    OEE-{{ scadaData[1] ? scadaData[1].oee : 0}}%
                                </div>
                              </router-link>
                              <router-link :to="generateStationId(scadaData[1] ? scadaData[1].station_id: 0)" style="text-decoration: none; color: white;">
                                <div style="background-color: #00C853;" class="rounded-bottom p-1">
                                  PER-{{ scadaData[1] ? scadaData[1].performance : 0}}/100
                                </div>
                              </router-link>
                            </div>

                        </div>


                        <div class="scada-container">
                            <div class="d-block text-center"><h3 class="text-center btn btn-primary">Outdoor Assembly Line</h3></div>

                            <div class="belt"></div>
                            <div class="connect-line" style="left: -9.6%;"></div>

                            <div class="connect-line lc" style="left: 83.6%; border-left: 0px; border-right: 3px solid #E8B900;"></div>

                            <div class="bubble" style="left: 1%;">
                                <router-link :to="generateStationId(scadaData[5] ? scadaData[5].station_id : 0)" style="text-decoration: none; color: white;">
                                    <div style="background-color: #6D6D6D;" class="rounded-top p-1">
                                        OEE-{{ scadaData[7] ? scadaData[7].oee : 0}}%
                                    </div>
                                </router-link>

                                <router-link :to="generateStationId(scadaData[5] ? scadaData[5].station_id : 0)" style="text-decoration: none; color: white;">
                                    <div style="background-color: #00C853;" class="rounded-bottom p-1">
                                        PER-{{ scadaData[7] ? scadaData[7].performance : 0}}/100
                                    </div>
                                </router-link>

                            </div>
                            <div class="prod-line inovace-sensor pro-1" style="left: 1%;"></div>
                            <div class="product ac-boxed" style="left: 4%; height: 80px; width: 80px;"></div>



                            <div class="ac-naked" style="height: 106px; width: 106px;background-size: 100%; background-repeat: no-repeat;    position: absolute;bottom: -1rem;left: 19.5%;"></div>

                            <div class="outdoor" style="background-color: rgba(229, 243, 255, 1); display: inline-block;position: absolute;bottom: 1.5rem;height: 279px;left: 28.5%;width: 100px;">
                                <div class="outdoor-bubbles">
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">OEE 0%</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Per 0/100</div>

                                    <div class="computer" style="height: 30px;width: 30px;background-repeat: no-repeat;background-size: 100% 100%;"></div>
                                    <div>Outdoor <br> Aging Test</div>
                                </div>
                            </div>

                            <div class="ac-naked" style="height: 106px; width: 106px;background-size: 100%; background-repeat: no-repeat; position: absolute;bottom: -1rem;left: 37%;"></div>




                            <div class="ac-introlly" style="height: 106px; width: 106px; background-size: 100%; background-repeat: no-repeat; position: absolute; bottom: -1.4rem; left: 54.4%;"></div>

                            <div class="outdoor" style="background-color: rgba(229, 243, 255, 1); display: inline-block;position: absolute;bottom: 1.5rem;height: 279px;left: 65%;width: 100px;">
                                <div class="outdoor-bubbles">
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Quality 0%</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Reject 0</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Pass 0</div>

                                    <div class="computer" style="height: 30px;width: 30px;background-repeat: no-repeat;background-size: 100% 100%;"></div>
                                    <div>Outdoor <br> Helium Leak Test</div>
                                </div>
                            </div>


                            <div class="introlly" style="height: 106px; width: 106px; background-size: 100%; background-repeat: no-repeat; position: absolute; bottom: -1.4rem; left: 83%;"></div>

                            <div class="trolly" style="height: 106px; width: 106px; background-size: 100%; background-repeat: no-repeat; position: absolute; bottom: -1.4rem; left: 93%;"></div>

                            <div class="prod-line inovace-sensor pro-4" style="left: 91.6%;"></div>
                            <div class="bubble rounded-2 lb" style="left: 88%;">
                                <router-link :to="generateStationId(scadaData[1] ? scadaData[1].station_id: 0)" style="text-decoration: none; color: white;">
                                    <div style="background-color: #6D6D6D;" class="rounded-top p-1">
                                        OEE-{{ scadaData[6] ? scadaData[6].oee : 0}}%
                                    </div>
                                </router-link>
                                <router-link :to="generateStationId(scadaData[1] ? scadaData[1].station_id: 0)" style="text-decoration: none; color: white;">
                                    <div style="background-color: #00C853;" class="rounded-bottom p-1">
                                        PER-{{ scadaData[6] ? scadaData[6].performance : 0}}/100
                                    </div>
                                </router-link>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ScadaService from "../../services/ScadaService";

    export default {
        name: "ScadaView",
        props: {
        },
        components: {
        },
        data: () => ({
            scadaData: [],
            filter: "last_hour",
        }),
        computed:{
        },
        methods: {
          generateStationId(stationId) {
            return "/lineview?stn_id=" + stationId;
          },

          getScadaData(){
              ScadaService.getScada(  {filter: this.filter},res => {
                  this.scadaData = res;
                  console.log(this.scadaData);
              });
          },
        },
        mounted(){
            this.getScadaData();
        }
    }
</script>

<style scoped>
    .outdoor{
        border: 2px solid #76CCFF;
        text-align: center;
        padding: 2px;
    }

    .outdoor-bubbles div:last-child{
        color: #4695FC;
    }
    .bottom-arrow:after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -22px;
        width: 0;
        height: 0;
        border-top: solid 18px #4896FC;
        border-left: solid 10px transparent;
        border-right: solid 10px transparent;
    }

    .scada-container{
        height: 74%;
        width: 100%;
        position: relative;
    }

    .belt{
        background-image: url("../../../assets/images/scada/belt.png");
        background-repeat: no-repeat;
        height: 1.5rem;
        width: 100%;
        background-size: 100%;
        background-position: bottom;
        position: absolute;
        bottom: 0;
    }

    .prod-line{
        height: 30px;
        width: 30px;
        background-repeat: no-repeat;
        background-size: 100% 100%;
    }

    .inovace-sensor{
        background-image: url("../../../assets/images/scada/sensor.png");
        position: absolute;
        bottom: 47%;
        left: 5%;
    }

    .station-1{
        background-image: url("../../../assets/images/scada/station1.png");
        height: 30px;
        width: 30px;
        background-repeat: no-repeat;
        background-size: 100% 100%;
    }

    .station-2{
        background-image: url("../../../assets/images/scada/station2.png");
        height: 30px;
        width: 30px;
        background-repeat: no-repeat;
        background-size: 100% 100%;
    }

    .station-3{
        background-image: url("../../../assets/images/scada/station3.png");
        height: 30px;
        width: 30px;
        background-repeat: no-repeat;
        background-size: 100% 100%;
    }

    .station-4{
        background-image: url("../../../assets/images/scada/station4.png");
        height: 30px;
        width: 30px;
        background-repeat: no-repeat;
        background-size: 100% 100%;
    }

    .product{
        height: 40px;
        width: 40px;
        background-repeat: no-repeat;
        background-size: 100% 100%;
        position: absolute;

        bottom: 1.5rem;
    }

    .ac-boxed{
        background-image: url("../../../assets/images/scada/ac1.png");
    }

    .computer{
        background-image: url("../../../assets/images/scada/computer.png");
        display: inline-block;
    }
    .ac-naked{
        background-image: url("../../../assets/images/scada/ac2.png");
    }

    .ac-introlly{
        background-image: url("../../../assets/images/scada/ac3.png");
    }

    .introlly{
        background-image: url("../../../assets/images/scada/ac4.png");
    }

    .trolly{
        background-image: url("../../../assets/images/scada/ac5.png");
    }

    .connect-line{
        position: absolute;
        bottom: 51%;
        left: -6.6%;
        height: 96px;

        border-left: 3px solid #E8B900;
        border-top: 3px solid #E8B900;
        border-bottom: 3px solid #E8B900;

        margin: 0 10%;
        width: 15px;
    }

    .straight-connect-line{
        position: absolute;
        bottom: 20%;
        height: 143px;
        border-left: 3px solid #E8B900;
    }

    .bubble{
        position: absolute;
        display: inline-block;
        left: 5%;
        bottom: 67%;
        text-align: center;
        color: white;
    }

    .packaging-bubble{
        background-color: #4896FC;
        position: absolute;
        bottom: 46px;
        color: white;
        border-radius: 3px;
        padding: 3px;
    }

    @media(min-height:900px)
    {
        .lc{left: 82% !important;}
        .lc2{left: 57% !important;}
        .lb{left: 87.6% !important;}
        .lb2{left: 62.5% !important;}
        .lb3{left: 38.7% !important;}
        .lc3{left: 33% !important;}
        .bubble{bottom: 46% !important;}
        .connect-line{bottom: 34% !important;}
        .inovace-sensor{bottom: 31% !important;}
        .pro-2{left: 41.6% !important;}
        .pro-3{left: 65.6% !important;}
        .pro-4{left: 90.6% !important;}
        .scada-container{height: 48%;}
    }
</style>
