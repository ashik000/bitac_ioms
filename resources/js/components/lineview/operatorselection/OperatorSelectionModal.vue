<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <modal @close="$emit('close')">
        <template v-slot:header>
            <div class="container" style="width: 960px; ">
                <div class="row" style="margin-left: 0!important; margin-bottom: 10px;">
                    <h5>Select Operator</h5>
                </div>
            </div>
        </template>
        <template v-slot:content>
            <div class="container" style="width: 960px; ">
                <div class="row" style="margin-left: 0!important; margin-bottom: 10px;">
                    <span>Current Station: {{ stationName }}</span>
                </div>
                <div style="overflow-y: scroll; height: 400px;">
                    <ul class="list-group">
                        <operator-selection-list-item
                            v-for="operator in operators"
                            :key="operator.id"
                            :operator="operator"
                            :station-id="stationId"
                            :station-name="stationName">
                        </operator-selection-list-item>
                    </ul>
                </div>
            </div>
        </template>

        <template v-slot:footer>
            <button class="btn btn-outline-danger" @click.prevent="$emit('close')">Close</button>
        </template>
    </modal>

</template>

<script>
    import Modal from "../../Modal";
    import OperatorsService from "../../../services/OperatorsService";
    import OperatorSelectionListItem from "./OperatorSelectionListItem";

    export default {
        name: "OperatorSelectionModal",
        data: function () {
            return {
                operators: []
            }
        },

        components: {
            modal: Modal,
            OperatorSelectionListItem
        },

        methods: {

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
            stationName: String
        }

    }
</script>

<style scoped>

</style>
