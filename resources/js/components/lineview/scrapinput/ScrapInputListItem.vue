<template>
    <li class="list-group-item listitemm border">
        <div class="row">
            <div class="col-sm-4">
                <span>{{ timeRange }}</span>
            </div>
            <div class="col-sm-4">{{ scrap.name }}</div>
            <div class="col-sm-4">
                <input class="input" type="number" @blur="scrap.updated=true" v-model.number="scrap.scraped" style="width: 130px;"><span> / {{ scrap.produced }}</span>
            </div>
        </div>
    </li>
</template>

<script>
    export default {
        name: "ScrapInputListItem",
        data: ()=>{
            return {
            }
        },
        props:{
            scrap : Object,
        },
        computed: {
            timeRange(){
                return moment().hour(this.scrap.hour).startOf('hour').format("hh:mm a") + " - " + moment().hour(this.scrap.hour).endOf('hour').format("hh:mm a");
            }
        }
    }
</script>

<style scoped lang="scss">
    .listitemm {
        margin-bottom: 15px;
        /*-webkit-box-shadow: 1px 2px 5px -1px rgba(0, 0, 0, 0.5);*/
        /*-moz-box-shadow: 1px 2px 5px -1px rgba(0, 0, 0, 0.5);*/
        /*box-shadow: 1px 2px 5px -1px rgba(0, 0, 0, 0.5);*/
    }

    li:hover {
        /*margin-right:40px;*/
        background: #f5f5f5;
    }

    /*li {*/
    /*    transition: all 300ms ease-in-out;*/
    /*    &:hover {*/
    /*        margin-right:40px;*/
    /*    }*/
    /*}*/
    .listitemm-uncommented {
        border-left: 3px solid yellow;
    }
    .listitemm-unplanned {
        border-left: 3px solid red;
    }
    .listitemm-planned {
        border-left: 3px solid blue;
    }

    .list-item-dropdown {
        color: black;
        background-color: white;
        border-color: white;
        width: 80%;
    }

    .input {
        border: 0;
        outline: 0;
        background: transparent;
        border-bottom: 1px solid #868e96;
        &:focus{
            border-color: #0B312A;
        }
    }
</style>
