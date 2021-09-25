<template>
    <li class="list-group-item listitemm border">
        <div class="row">
            <div class="col-sm-5">
                <span>{{ operator.first_name + " " + operator.last_name }}</span>
                <pre>{{ operator.code }}</pre>
            </div>

            <div class="col-sm-3" v-if="operator.stations && operator.stations.length > 0">
                <span v-for="(operatorStation, index) in operator.stations" :key="operatorStation.id">
                    <span v-if="index < operator.stations.length -1">{{ operatorStation.name + ", " }}</span>
                    <span v-else>{{ operatorStation.name }}</span>
                </span>
            </div>
            <div class="col-sm-3" v-else>
                <span>None</span>
            </div>

            <div class="col-sm-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1"
                        :value="operator.id" @change="onOperatorChange($event)" :checked="operator.id == checkedVal"
                        style="float: right;" />
                </div>
            </div>
        </div>
    </li>

</template>

<script>
    import stationOperatorService from "../../../services/StationOperatorService"

    export default {
        name: "OperatorSelectionListItem",

        data: function () {
            return {
                assignedStationIds: [],
                checkedVal: null,
                selectedOperatorId: null
            }
        },

        methods: {
            onOperatorChange: function (event) {
                var data = event.target.value;
                console.log('on operator change triggered')
                this.selectedOperatorId = data;
                console.log(this.selectedOperatorId);
                this.$emit('update-operator', this.selectedOperatorId);
            },
        },

        props: {
            operator: Object,
            stationId: Number,
            stationName: String,
            operatorId: Number,
        },
        mounted() {
            console.log('test operator mount')
            console.log(this.operator);

            let vm = this;
            vm.checkedVal = vm.operatorId;     // to set the default value of the radio button means set the operatorId
        },
    }
</script>

<style src="vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css"></style>

<style scoped lang="scss">
    .listitemm {
        margin-bottom: 15px;
        /*-webkit-box-shadow: 1px 2px 5px -1px rgba(0, 0, 0, 0.5);
        -moz-box-shadow: 1px 2px 5px -1px rgba(0, 0, 0, 0.5);
        box-shadow: 1px 2px 5px -1px rgba(0, 0, 0, 0.5);*/
    }

    li:hover {
        /*margin-right:40px;*/
        background: #f5f5f5;
        border-left: 3px solid #337ab7;
    }

    /*li {*/
    /*    transition: all 300ms ease-in-out;*/
    /*    &:hover {*/
    /*margin-right:40px;*/
    /*}*/
    /*}*/
</style>
