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
                    <div class="card-body  y-scroll">
                        <div class="belt">
                            <div class="connect-line"></div>
                            <div class="oee-bubble" style="border-radius: 30px; display: inline-block;">
                                <div style="background-color: gray;">
                                    OEE-80%
                                </div>
                                <div style="background-color: blue;">
                                    PER-90/100
                                </div>
                            </div>
                            <div class="prod-line inovace-sensor"></div>
                            <div class="product ac-boxed"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ScadaView",
        props: {
        },
        components: {
        },
        data: () => ({

        }),
        computed:{
        },
        methods: {
        },
        watch: {
        },
        mounted(){
        }
    }
</script>

<style scoped>
    .belt{
        background-image: url("../../../assets/images/scada/belt.png");
        background-repeat: no-repeat;
        height: 100%;
        width: 100%;
        background-size: 100%;
        background-position: bottom;
        background-color: purple;
        position: relative;
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
        left: 3%;
    }

    .product{
        height: 30px;
        width: 30px;
        background-repeat: no-repeat;
        background-size: 100% 100%;
        background-color: #0a53be;
        position: absolute;

        bottom: 5%;
        left: 10%;
    }

    .ac-boxed{
        background-image: url("../../../assets/images/scada/ac1.png");
    }
    .ac-naked{
        background-image: url("../../../assets/images/scada/ac2.png");
    }

    .connect-line{
        position: absolute;
        bottom: 40%;
        left: 30%;
        height: 40px;

        border-left: 5px solid;
        border-top: 5px solid;
        border-bottom: 5px solid;

        margin: 0 10%;
        width: 15px;


    }
</style>
