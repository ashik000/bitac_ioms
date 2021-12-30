<template>
    <div class="container-fluid wrapper">
        <div class="card-wrapper row">
            <div class="col-12 h-100">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <div>SCADA View</div>
                        <div class="d-flex">
                            <div class="input-group remove-width">
                                <input type="text" class="form-control" placeholder="Search" aria-label="Stations search" aria-describedby="Stations search">
                                <button class="btn transparent-search-button" type="button">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="bi bi-search" viewBox="0 0 16 16">
                                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="scada-container">
                            <div class="belt"></div>
                            <div class="connect-line"></div>
                            <div class="connect-line lc3" style="left: 50%;left: 33.6%;border-left: 0;border-right: 5px solid;"></div>

<!--                            <div class="straight-connect-line" style="left: 14%"></div>-->

                            <div class="connect-line lc2" style="left: 58.6%; border-left: 0px; border-right: 5px solid;"></div>

                            <div class="connect-line lc" style="left: 83.6%; border-left: 0px; border-right: 5px solid;"></div>

                            <div class="bubble" style="left: 4%;">
                                <div style="background-color: #6D6D6D;" class="rounded-top p-1">
                                    OEE-{{ scadaData[5] ? scadaData[5].oee : 0}}%
                                </div>
                                <div style="background-color: #00C853;" class="rounded-bottom p-1">
                                    PER-{{ scadaData[5] ? scadaData[5].performance : 0}}/100
                                </div>
                            </div>
                            <div class="prod-line inovace-sensor" style="left: 4%;"></div>
                            <div class="product ac-boxed" style="left: 8%;"></div>
<!--                            <div class="bubble" style="left: 12%;">-->
<!--                                <div style="background-color: gray;">-->
<!--                                    OEE-80%-->
<!--                                </div>-->
<!--                                <div style="background-color: red;">-->
<!--                                    PER-90/100-->
<!--                                </div>-->
<!--                            </div>-->
                            <div class="product computer" style="left: 13%; bottom: 14%; white-space: nowrap;">
                                <div class="packaging-bubble bottom-arrow">Packaging</div>
                            </div>

                            <div class="outdoor" style="background-color: rgba(229, 243, 255, 1); display: inline-block;position: absolute;bottom: 1.5rem;height: 279px;left: 19%;width: 86px;">
                                <div class="outdoor-bubbles">
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Quality 0%</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Reject 0</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Pass 0</div>

                                    <div class="computer" style="height: 30px;width: 30px;background-repeat: no-repeat;background-size: 100% 100%;"></div>
                                    <div>Outdoor <br> Leek Test-2</div>
                                </div>
                            </div>

                            <div class="ac-naked" style="height: 60px; width: 60px;background-size: 100%; background-repeat: no-repeat;    position: absolute;bottom: -0.1rem;left: 26%;"></div>

                            <div class="outdoor" style="background-color: rgba(229, 243, 255, 1); display: inline-block;position: absolute;bottom: 1.5rem;height: 279px;left: 31%;width: 86px;">
                                <div class="outdoor-bubbles">
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">OEE 0%</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Per 0/100</div>

                                    <div class="computer" style="height: 30px;width: 30px;background-repeat: no-repeat;background-size: 100% 100%;"></div>
                                    <div>Outdoor <br> Aging Test</div>
                                </div>
                            </div>

                            <div class="ac-naked" style="height: 60px; width: 60px;background-size: 100%; background-repeat: no-repeat; position: absolute;bottom: -0.1rem;left: 38%;"></div>

                            <div class="prod-line inovace-sensor" style="left: 41.6%;"></div>

                            <div class="bubble lb3" style="left: 37%">
                                <div style="background-color: #6D6D6D;" class="rounded-top p-1">
                                    OEE-{{scadaData[4] ? scadaData[4].oee : 0}}%
                                </div>
                                <div style="background-color: #00C853;" class="rounded-bottom p-1">
                                    PER-{{ scadaData[4] ? scadaData[4].performance : 0}}/100
                                </div>
                            </div>

                            <div class="outdoor" style="background-color: rgba(229, 243, 255, 1); display: inline-block;position: absolute;bottom: 1.5rem;height: 279px;left: 47.4%;width: 86px;">
                                <div class="outdoor-bubbles">
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Quality 0%</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Reject 0</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Pass 0</div>

                                    <div class="computer" style="height: 30px;width: 30px;background-repeat: no-repeat;background-size: 100% 100%;"></div>
                                    <div>Outdoor <br> Leek Test-1</div>
                                </div>
                            </div>

                            <div class="station-1" style="position: absolute;bottom: 1.5em;left: 55%;"></div>

                            <div class="station-2" style="position: absolute;bottom: 1.5em;left: 60%; white-space: nowrap;">
                                <div class="packaging-bubble bottom-arrow" style="right: -25px;">Gas Charge</div>
                            </div>

                            <div class="bubble lb2" style="left: 62%;"><div style="background-color: #6D6D6D;" class="rounded-top p-1">
                                OEE-{{ scadaData[3] ? scadaData[3].oee : 0}}%
                            </div> <div style="background-color: #00C853;" class="rounded-bottom p-1">
                                PER-{{ scadaData[3] ? scadaData[3].performance : 0}}/100
                            </div></div>

                            <div class="station-3" style="position: absolute;bottom: 1.5em;left: 65%;"></div>

                            <div class="prod-line inovace-sensor" style="left: 66.6%;"></div>

                            <div class="ac-introlly" style="height: 60px; width: 60px; background-size: 100%; background-repeat: no-repeat; position: absolute; bottom: -0.1rem; left: 70.4%;"></div>

                            <div class="outdoor" style="background-color: rgba(229, 243, 255, 1); display: inline-block;position: absolute;bottom: 1.5rem;height: 279px;left: 75.4%;width: 86px;">
                                <div class="outdoor-bubbles">
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Quality 0%</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Reject 0</div>
                                    <div class="rounded-2" style="border: 2px solid #EEBE00;margin-bottom: 3px;">Pass 0</div>

                                    <div class="computer" style="height: 30px;width: 30px;background-repeat: no-repeat;background-size: 100% 100%;"></div>
                                    <div>Outdoor <br> Hellium Leak Test</div>
                                </div>
                            </div>

                            <div class="station-4" style="position: absolute;bottom: 1.5em;left: 84%;"></div>

                            <div class="introlly" style="height: 60px; width: 60px; background-size: 100%; background-repeat: no-repeat; position: absolute; bottom: -0.1rem; left: 87%;"></div>

                            <div class="trolly" style="height: 60px; width: 60px; background-size: 100%; background-repeat: no-repeat; position: absolute; bottom: -0.1rem; left: 94%;"></div>

                            <div class="prod-line inovace-sensor" style="left: 91.6%;"></div>
                            <div class="bubble rounded-2 lb" style="left: 86.6%;">
                                <div style="background-color: #6D6D6D;" class="rounded-top p-1">
                                    OEE-{{ scadaData[1] ? scadaData[1].oee : 0}}%
                                </div>
                                <div style="background-color: #00C853;" class="rounded-bottom p-1">
                                    PER-{{ scadaData[1] ? scadaData[1].performance : 0}}/100
                                </div>
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
        }),
        computed:{
        },
        methods: {
        },
        mounted(){
            ScadaService.getScada( success => {
               this.scadaData = success;
            });
        }
    }
</script>

<style scoped>
    .outdoor{
        border: 2px solid #76CCFF;
        text-align: center;
        padding: 2px;
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
        bottom: 30%;
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
        bottom: 33%;
        left: -6.6%;
        height: 96px;

        border-left: 5px solid;
        border-top: 5px solid;
        border-bottom: 5px solid;

        margin: 0 10%;
        width: 15px;
    }

    .straight-connect-line{
        position: absolute;
        bottom: 20%;
        height: 143px;
        border-left: 5px solid;
    }

    .bubble{
        position: absolute;
        display: inline-block;
        left: 5%;
        bottom: 50%;
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
        .lc{left: 83% !important;}
        .lc2{left: 58% !important;}
        .lb{left: 87.6% !important;}
        .lb2{left: 62.5% !important;}
        .lb3{left: 38.7% !important;}
        .lc3{left: 33% !important;}
    }
</style>
