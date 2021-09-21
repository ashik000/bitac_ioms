<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <modal @close="$emit('close')">
        <template v-slot:header>
            <div class="container" style="width: 960px; ">
                <div class="row" style="margin-left: 0!important;">
                    <h5>Select Operator</h5>
                </div>
            </div>
        </template>
        <template v-slot:content>
            <div class="container" style="width: 960px; ">
                <div class="row" style="margin-left: 0!important;">
                    <span>Current Station: {{ stationName }}</span>
                </div>
                <div style="overflow-y: scroll; height: 400px;">
                    <ul class="list-group">
                        <operator-selection-list-item
                            v-for="operator in operators"
                            :key="operator.id"
                            :operator="operator"
                            :station-id="stationId"
                            :station-name="stationName"
                            :operator-id="operatorId"
                            @update-operator="updateOperator"
                            >
                        </operator-selection-list-item>
                    </ul>
                </div>
            </div>
        </template>

        <template v-slot:footer>
            <button class="btn btn-outline-danger" @click.prevent="$emit('close')">Close</button>
            <button class="btn btn-success ms-3" @click="assignOperatorToStation();" >Assign</button>
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
                selectedOperatorId: null,
            }
        },

        components: {
            modal: Modal,
            OperatorSelectionListItem
        },

        methods: {
            updateOperator(selectedOperatorId) {
                console.log('when updateOperator emit triggers on modal')
                console.log(selectedOperatorId);
                this.selectedOperatorId = selectedOperatorId;
            },
            assignOperatorToStation() {
                stationOperatorService.assignOperatorToStation({
                    stationId: this.stationId,
                    operatorId: this.selectedOperatorId
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
                console.log('fetch operators')
                console.log(data)
                // console.log(vm.operators)
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

<style scoped>

</style>
