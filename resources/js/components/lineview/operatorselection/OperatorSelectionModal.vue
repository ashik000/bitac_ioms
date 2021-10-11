<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <modal @close="$emit('close')">
        <template v-slot:header>
            <div class="container" style="width: 960px; padding-left: 0;">
                <div class="row" style="margin-left: 0!important;">
                    <h5 style="padding-left: 0;">Select Operator</h5>
                </div>
            </div>
        </template>
        <template v-slot:content>
            <div class="container" style="width: 960px; ">
                <div class="row" style="margin-left: 0!important;">
                    <span style="padding-left: 5px;">Current Station: {{ stationName }}</span>
                </div>
                <div>
                    <ul class="list-group">
                        <operator-selection-list-item
                            v-for="operator in operators"
                            :key="operator.id"
                            :operator="operator"
                            :station-id="stationId"
                            :station-name="stationName"
                            :operator-id="operatorId"
                            @update-operator="updateOperator"
                            :isChecked="selectedOperatorId === operator.id"
                            >
                        </operator-selection-list-item>
                    </ul>
                </div>
            </div>
        </template>

        <template v-slot:footer>
            <div class="pb-3" style="padding-right: 15px;">
                <button class="btn btn-outline-danger" @click.prevent="$emit('close')">Close</button>
                <button class="btn btn-success ms-3" @click="assignOperatorToStation();" >Assign</button>
            </div>
        </template>
    </modal>

</template>

<script>
    import Modal from "../../Modal";
    import OperatorsService from "../../../services/OperatorsService";
    import stationOperatorService from "../../../services/StationOperatorService";
    import ToastrService from "../../../services/ToastrService";
    import OperatorSelectionListItem from "./OperatorSelectionListItem";

    export default {
        name: "OperatorSelectionModal",
        data: function () {
            return {
                operators: null,
                selectedOperatorId: this.operatorId,
            }
        },

        components: {
            modal: Modal,
            OperatorSelectionListItem
        },

        methods: {
            updateOperator(selectedOperatorId) {
                this.selectedOperatorId = selectedOperatorId;
            },
            assignOperatorToStation() {
                stationOperatorService.assignOperatorToStation({
                    station_id: this.stationId,
                    operator_id: this.selectedOperatorId
                }, data => {
                    ToastrService.showSuccessToast('Operator updated successfully.');
                    this.$emit('close');
                }, error => {
                    ToastrService.showErrorToast('Error! Try again.');
                })
            }
        },
        mounted: function (){
            const vm = this;

            OperatorsService.fetchAll((data) => {
                vm.operators = data;
            }, (error) => {
                console.log(error);
            });
        },

        props: {
            stationId: Number,
            stationName: String,
            operatorId: Number,
        }

    }
</script>
