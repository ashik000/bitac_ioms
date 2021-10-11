<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <modal @close="$emit('close')">
        <template v-slot:header>
            <div class="container" style="width: 960px; padding-left: 0px;">
                <div class="row" style="margin-left: 0!important; padding-left: 0px;">
                    <h5 style="padding-left: 0px;">DEFECT ENTRY</h5>
                </div>
            </div>
        </template>
        <template v-slot:content>
            <div class="container" style="width: 960px; ">
                <div class="row" style="margin-bottom: 15px">
                    <span style="color: #868e96; padding-left: 15px">Total Defect : {{ totalScrap }}</span>
                </div>
                <div>
                    <ul class="list-group">
                        <scrap-input-list-item
                            v-for="(scrap, index) in scrapList"
                            :key="`scraped-${index}`"
                            :scrap="scrap">
                        </scrap-input-list-item>
                    </ul>
                </div>
            </div>
        </template>

        <template v-slot:footer>
            <div class="pb-3" style="padding-right: 15px;">
                <button class="btn btn-outline-danger" @click.prevent="$emit('close')">Close</button>
                <button class="btn btn-success ms-3" @click.prevent="save">Assign</button>
            </div>
        </template>

    </modal>
</template>

<script>
    import Modal from "../../Modal";
    import ScrapInputListItem from "./ScrapInputListItem";
    import ReportService from "../../../services/Reports";
    import ScrapService from "../../../services/ScrapService";
    import toastrService from "../../../services/ToastrService";

    export default {
        name: "ScrapInputModal",
        data: () => {
            return {
                scrapList: [],
            }
        },
        computed: {
            totalScrap(){
                return this.scrapList.reduce((sum, item) => sum + (item.scraped && item.scraped>=0 ? item.scraped : 0) , 0);
            }
        },
        components: {
            modal: Modal,
            ScrapInputListItem,
        },
        mounted: function () {
            const vm = this;
            ReportService.getHourlyProducedAndScrapedCountOfADay({
                'stationId': vm.stationId,
                'date': moment(vm.date).format('DD-MM-YYYY')
            },function (resData) {
                vm.scrapList = resData;
            });
        },
        props: {
            stationId : Number,
            date: Date
        },
        methods:{
            save(){
                const vm = this;
                let req = vm.scrapList.filter((s)=>s.updated);
                ScrapService.uploadHourlyScrapCount(req, (resData)=>{
                    vm.$emit('close');
                    toastrService.showSuccessToast('Defect input successful');
                }, (error) => {
                    toastrService.showErrorToast('Sorry could not input defect data');
                });
            }
        }
    }
</script>

<style scoped>

</style>
