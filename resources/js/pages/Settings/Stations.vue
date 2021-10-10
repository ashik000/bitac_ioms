<template>
    <span>
        <b-overlay :show="showInprogress" opacity="0.6" no-wrap></b-overlay>
        <div class="card-wrapper row">
            <div class="section col-md-3 col-sm-12 h-100">
                <StationGroup sectionHeader="Station Groups" :items="groups" @action-clicked="openGroupAddModal" buttonText="Add Stations Group">
                    <template v-slot="{ item }">
                        <span class="hide_overflow_text anchor_btn" @click.prevent="loadGroupData(item.id)">
                            {{ item.name }}
                        </span>
                        <span class="float-end">
                            <button type="button" class="btn btn-primary btn-sm" @click.prevent="showGroupEditModal(item)">
                                <b-icon icon="pencil-square" class="pb-sm-1" font-scale="1.30"></b-icon> EDIT
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" @click.prevent="showGroupDeleteModal(item)">
                                <b-icon icon="trash" class="pb-sm-1" font-scale="1.30"></b-icon> DELETE
                            </button>
                        </span>
                    </template>
                </StationGroup>
            </div>

            <div class="section col-md-9 col-sm-12 h-100">
                <StationList :items="stations"
                                sectionHeader="Stations"
                                :selected-id="selectedStationId"
                                @action-clicked="openStationAddModal">

                    <template v-slot:row="{ row }">
                        <h2 class="accordion-header" id="panelsStayOpen-headingOne" @click="setSelectedStationId(row)">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" :data-bs-target="'#station-details' + row.id" type="button" aria-expanded="false" aria-controls="panelsStayOpen-collapseOne" @click="setSelectedStationId(row)">
                            {{ row.name }}
                            </button>
                            <div class="d-flex stations-setting-button-group">
                                <button type="button" class="btn btn-secondary btn-sm" :data-bs-target="'#station-details' + row.id" aria-expanded="false" data-bs-toggle="collapse" @click="setSelectedStationId(row)">
                                    <b-icon icon="gear" class="pb-sm-1" font-scale="1.30"></b-icon> MANAGE COMPONENTS
                                </button>
                                <button type="button" class="btn btn-primary btn-sm" @click.prevent="showStationEditModal(row)">
                                    <b-icon icon="pencil-square" class="pb-sm-1" font-scale="1.30"></b-icon> EDIT CONFIGURATION
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" @click.prevent="showStationDeleteModal(row)">
                                    <b-icon icon="trash" class="pb-sm-1" font-scale="1.30"></b-icon> DELETE
                                </button>
                            </div>
                        </h2>

                        <div :id="'station-details' + row.id" class="accordion-collapse collapse" >
                            <div v-if="selectedStationId === row.id || selectedStationId == null || selectedStationId === 0" class="accordion-body">
                                <!-- Tabs navs -->
                                <ul class="nav nav-tabs nav-justified mb-3" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a
                                            class="nav-link active"
                                            id="ex3-tab-1"
                                            data-bs-toggle="tab"
                                            :href="'#products-tab' + row.id"
                                            role="tab"
                                            :aria-controls="'products-tab' + row.id"
                                            aria-selected="true"
                                        >Products</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a
                                            class="nav-link"
                                            id="ex3-tab-2"
                                            data-bs-toggle="tab"
                                            :href="'#shifts-tab' + row.id"
                                            role="tab"
                                            :aria-controls="'shifts-tab' + row.id"
                                            aria-selected="false"
                                        >Shifts</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a
                                            class="nav-link"
                                            id="ex3-tab-3"
                                            data-bs-toggle="tab"
                                            :href="'#operators-tab' + row.id"
                                            role="tab"
                                            :aria-controls="'operators-tab' + row.id"
                                            aria-selected="false"
                                        >Operators</a>
                                    </li>
                                </ul>

                                <div class="tab-content row">
                                    <div class="tab-pane fade show active"
                                        :id="'products-tab' + row.id"
                                        role="tabpanel"
                                        :aria-labelledby="'products-tab' + row.id">
                                        <StationProduct :station-id="selectedStationId"></StationProduct>
                                    </div>

                                    <div class="tab-pane fade"
                                        :id="'shifts-tab' + row.id"
                                        role="tabpanel"
                                        :aria-labelledby="'shifts-tab' + row.id">
                                        <StationShift :station-id="selectedStationId"></StationShift>
                                    </div>

                                    <div class="tab-pane fade"
                                        :id="'operators-tab' + row.id"
                                        role="tabpanel"
                                        :aria-labelledby="'operators-tab' + row.id">
                                        <StationOperator :station-id="selectedStationId"></StationOperator>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </template>
                </StationList>
            </div>
        </div>

        <Modal v-if="showGroupDeleteForm" @close="closeGroupForm">
            <template v-slot:header>
                <h5>
                    Delete Group
                </h5>
            </template>
            <template v-slot:content>
                <form @submit.prevent="deleteGroup">
                    <p>Are you sure you want to delete the group named <span style="color: darkred">{{groupName}}</span>?</p>
                    <button class="btn btn-danger float-end">SUBMIT</button>
                </form>
                <b-overlay :show="showInprogress" opacity="0.6" no-wrap></b-overlay>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>

        <Modal v-if="showGroupForm" @close="closeGroupForm">
            <template v-slot:header>
                <h5>
                    {{ modalTitleText }} Station Group
                </h5>
            </template>
            <template v-slot:content>
                <form>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" v-model="groupName" class="form-control" id="name" placeholder="Enter Name">
                    </div>
                </form>
                <b-overlay :show="showInprogress" opacity="0.6" no-wrap></b-overlay>
            </template>
            <template v-slot:footer>
                <div class="float-end pb-3" style="padding-right: 15px;">
                    <button class="btn btn-outline-danger" @click.prevent="closeGroupForm()">CLOSE</button>
                    <button class="btn btn-success ms-3" @click="groupId == null ? createGroup(): updateGroup()">SUBMIT</button>
                </div>
            </template>
        </Modal>

        <Modal v-if="showStationForm" @close="closeStationForm">
            <template v-slot:header>
                <h5>
                    {{selectedStationId != 0 ? "EDIT STATION" : "ADD STATION"}}
                </h5>
            </template>
            <template v-slot:content>
                <form>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" v-model="name" class="form-control" placeholder="Enter Name">
                        <label class="mt-3">Group</label>
                        <select class="form-select" v-model="selectedGroupId">
                            <option disabled value="0"> -- Select -- </option>
                            <option v-for="group in groups" :value="group.id" :key="group.id">
                                {{ group.name }}
                            </option>
                        </select>
                        <label class="mt-3">Description</label>
                        <input type="text" v-model="description" class="form-control" placeholder="Enter Description">
                        <label class="mt-3">Performance Threshold</label>
                        <input type="number" v-model="oee_threshold" class="form-control" placeholder="Enter Performance Threshold">
                    </div>
                </form>
                <b-overlay :show="showInprogress" opacity="0.6" no-wrap></b-overlay>
            </template>
            <template v-slot:footer>
                <div class="float-end pb-3" style="padding-right: 15px;">
                    <button class="btn btn-outline-danger" @click.prevent="closeStationForm()">CLOSE</button>
                    <button class="btn btn-success ms-3" @click="stationId == null ? createStation(): updateStation()">SUBMIT</button>
                </div>
            </template>
        </Modal>

        <Modal v-if="showStationDeleteForm" @close="closeStationForm">
            <template v-slot:header>
                <h5>
                    Delete Station
                </h5>
            </template>
            <template v-slot:content>
                <form @submit.prevent="deleteStations">
                    <p>Are you sure you want to delete the station named <span style="color: darkred">{{name}}</span>?</p>
                    <button class="btn btn-danger float-end">SUBMIT</button>
                </form>
                <b-overlay :show="showInprogress" opacity="0.6" no-wrap></b-overlay>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>
    </span>

</template>

<script>
import StationGroup from "../../components/settings/StationGroup";
import StationList from "../../components/settings/StationList";
import stationService from '../../services/StationsService';
import toastrService from '../../services/ToastrService';
import groupMixin from '../../mixins/groupMixin';
import StationProduct from "../../components/station/StationProduct";
import StationOperator from "../../components/station/StationOperator";
import StationShift from "../../components/station/StationShift";

export default {
    name: "Stations",
    components: {StationOperator, StationShift, StationProduct, StationGroup, StationList},
    mixins:[groupMixin],
    data: () => ({
        showStationForm: false,
        showStationDeleteForm : false,
        name:"",
        description:"",
        selectedGroupId: 0,
        oee_threshold: 0,
        groups: [],
        stations: [],
        selectedStationId:0,
        showInprogress: false,
        groupName: '',
    }),
    methods:{
        showStationEditModal(item){
            this.showStationForm = true;
            this.stationId = item.id;
            this.oee_threshold = item.oee_threshold;
            this.name = item.name;
            this.selectedGroupId = item.station_group.id;
            this.description = item.description;
        },
        updateGroup(){
            this.showInprogress = true;
            stationService.updateGroup(this.groupId,{name:this.groupName}, r=>{
                this.groups = r;
                this.showInprogress = false;
                this.closeGroupForm();
                toastrService.showSuccessToast('Station group updated.');
            }, error => {
                this.showInprogress = false;
                toastrService.showErrorToast(error);
                // console.log(error);
            });
        },
        deleteGroup(){
            this.showInprogress = true;
            if (this.stations.length > 0) {
                this.closeGroupForm();
                this.showInprogress = false;
                toastrService.showErrorToast("You can't delete this because the group has stations.");
            } else {
                stationService.deleteGroup(this.groupId, r =>{
                    this.groups = r;
                    this.showInprogress = false;
                    this.closeGroupForm();
                    toastrService.showSuccessToast('Station group deleted.');
                }, error =>{
                    this.showInprogress = false;
                    toastrService.showErrorToast(error);
                    // console.log(e);
                })

            }
        },
        createGroup() {
            this.showInprogress = true;
            stationService.addGroup({name: this.groupName}, data => {
                this.groups = data;
                this.showGroupForm = false;
                this.showInprogress = false;
                this.groupName = '';
                toastrService.showSuccessToast('Station group created.');
            }, error => {
                this.showInprogress = false;
                toastrService.showErrorToast(error);
            })
        },
        openStationAddModal(){
            this.showStationForm = true;
        },
        createStation(){
            this.showInprogress = true;
            stationService.addStation({
                name:this.name,
                station_group_id: this.selectedGroupId,
                oee_threshold : this.oee_threshold,
                description : this.description
            }, data => {
                this.stations = data;
                this.showStationForm = false;
                this.showInprogress = false;
                this.closeStationForm();
                toastrService.showSuccessToast('Station added.');
            }, error => {
                this.showInprogress = false;
                toastrService.showErrorToast(error);
            })
        },
        closeStationForm(){
            this.name = "";
            this.selectedGroupId = null;
            this.oee_threshold = null;
            this.description = null;
            this.showStationForm = null;
            this.showStationDeleteForm = null;
        },
        updateStation(){
            this.showInprogress = true;
            stationService.updateStation(this.stationId,{
                name:this.name,
                station_group_id: this.selectedGroupId,
                oee_threshold : this.oee_threshold,
                description : this.description
            }, data => {
                this.stations = data;
                this.showStationForm = false;
                this.showInprogress = false;
                toastrService.showSuccessToast('Station updated.');
            }, error => {
                this.showInprogress = false;
                toastrService.showErrorToast(error);
            })
        },
        deleteStations() {
            this.showInprogress = true;
            stationService.deleteStation(this.stationId,r =>{
                this.stations = r;
                this.showInprogress = false;
                this.closeStationForm();
                toastrService.showSuccessToast('Station deleted.');
            }, error => {
                this.showInprogress = false;
                toastrService.showErrorToast(error);
            })
        },
        showStationDeleteModal(item) {
            this.showStationDeleteForm = true;
            this.stationId = item.id;
            this.oee_threshold = item.oee_threshold;
            this.name = item.name;
            this.selectedGroupId = item.station_group.id;
            this.description = item.description;
        },
        setSelectedStationId(item){
            // console.log("Selected station id " + item.id);
            this.selectedStationId = item.id;
        },
        loadGroupData(groupId){
            this.showInprogress = true;
            stationService.fetchAllStationsByGroupId(groupId, stations => {
                this.stations = stations;
                this.showInprogress = false;
            });
        },
    },
    computed : {
        showConfigureStation(){
            return this.selectedStationId != 0;
        }
    },
    mounted() {
        stationService.fetchAllGroups(groups => {
            this.showInprogress = true;
            this.groups = groups;
            stationService.fetchAllStationsByGroupId(this.groups[0].id, stations => {
                // console.log(stations);
                this.stations = stations;
                this.showInprogress = false;
            });
        });
    }
}
</script>

<style scoped>
.toast-title{font-weight:700}.toast-message{-ms-word-wrap:break-word;word-wrap:break-word}.toast-message a,.toast-message label{color:#FFF}.toast-message a:hover{color:#CCC;text-decoration:none}.toast-close-button{position:relative;right:-.3em;top:-.3em;float:right;font-size:20px;font-weight:700;color:#FFF;-webkit-text-shadow:0 1px 0 #fff;text-shadow:0 1px 0 #fff;opacity:.8;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=80);filter:alpha(opacity=80);line-height:1}.toast-close-button:focus,.toast-close-button:hover{color:#000;text-decoration:none;cursor:pointer;opacity:.4;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=40);filter:alpha(opacity=40)}.rtl .toast-close-button{left:-.3em;float:left;right:.3em}button.toast-close-button{padding:0;cursor:pointer;background:0 0;border:0;-webkit-appearance:none}.toast-top-center{top:0;right:0;width:100%}.toast-bottom-center{bottom:0;right:0;width:100%}.toast-top-full-width{top:0;right:0;width:100%}.toast-bottom-full-width{bottom:0;right:0;width:100%}.toast-top-left{top:12px;left:12px}.toast-top-right{top:12px;right:12px}.toast-bottom-right{right:12px;bottom:12px}.toast-bottom-left{bottom:12px;left:12px}#toast-container{position:fixed;z-index:999999;pointer-events:none}#toast-container *{-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box}#toast-container>div{position:relative;pointer-events:auto;overflow:hidden;margin:0 0 6px;padding:15px 15px 15px 50px;width:300px;-moz-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;background-position:15px center;background-repeat:no-repeat;-moz-box-shadow:0 0 12px #999;-webkit-box-shadow:0 0 12px #999;box-shadow:0 0 12px #999;color:#FFF;opacity:.8;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=80);filter:alpha(opacity=80)}#toast-container>div.rtl{direction:rtl;padding:15px 50px 15px 15px;background-position:right 15px center}#toast-container>div:hover{-moz-box-shadow:0 0 12px #000;-webkit-box-shadow:0 0 12px #000;box-shadow:0 0 12px #000;opacity:1;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=100);filter:alpha(opacity=100);cursor:pointer}#toast-container>.toast-info{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAGwSURBVEhLtZa9SgNBEMc9sUxxRcoUKSzSWIhXpFMhhYWFhaBg4yPYiWCXZxBLERsLRS3EQkEfwCKdjWJAwSKCgoKCcudv4O5YLrt7EzgXhiU3/4+b2ckmwVjJSpKkQ6wAi4gwhT+z3wRBcEz0yjSseUTrcRyfsHsXmD0AmbHOC9Ii8VImnuXBPglHpQ5wwSVM7sNnTG7Za4JwDdCjxyAiH3nyA2mtaTJufiDZ5dCaqlItILh1NHatfN5skvjx9Z38m69CgzuXmZgVrPIGE763Jx9qKsRozWYw6xOHdER+nn2KkO+Bb+UV5CBN6WC6QtBgbRVozrahAbmm6HtUsgtPC19tFdxXZYBOfkbmFJ1VaHA1VAHjd0pp70oTZzvR+EVrx2Ygfdsq6eu55BHYR8hlcki+n+kERUFG8BrA0BwjeAv2M8WLQBtcy+SD6fNsmnB3AlBLrgTtVW1c2QN4bVWLATaIS60J2Du5y1TiJgjSBvFVZgTmwCU+dAZFoPxGEEs8nyHC9Bwe2GvEJv2WXZb0vjdyFT4Cxk3e/kIqlOGoVLwwPevpYHT+00T+hWwXDf4AJAOUqWcDhbwAAAAASUVORK5CYII=)!important}#toast-container>.toast-error{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAHOSURBVEhLrZa/SgNBEMZzh0WKCClSCKaIYOED+AAKeQQLG8HWztLCImBrYadgIdY+gIKNYkBFSwu7CAoqCgkkoGBI/E28PdbLZmeDLgzZzcx83/zZ2SSXC1j9fr+I1Hq93g2yxH4iwM1vkoBWAdxCmpzTxfkN2RcyZNaHFIkSo10+8kgxkXIURV5HGxTmFuc75B2RfQkpxHG8aAgaAFa0tAHqYFfQ7Iwe2yhODk8+J4C7yAoRTWI3w/4klGRgR4lO7Rpn9+gvMyWp+uxFh8+H+ARlgN1nJuJuQAYvNkEnwGFck18Er4q3egEc/oO+mhLdKgRyhdNFiacC0rlOCbhNVz4H9FnAYgDBvU3QIioZlJFLJtsoHYRDfiZoUyIxqCtRpVlANq0EU4dApjrtgezPFad5S19Wgjkc0hNVnuF4HjVA6C7QrSIbylB+oZe3aHgBsqlNqKYH48jXyJKMuAbiyVJ8KzaB3eRc0pg9VwQ4niFryI68qiOi3AbjwdsfnAtk0bCjTLJKr6mrD9g8iq/S/B81hguOMlQTnVyG40wAcjnmgsCNESDrjme7wfftP4P7SP4N3CJZdvzoNyGq2c/HWOXJGsvVg+RA/k2MC/wN6I2YA2Pt8GkAAAAASUVORK5CYII=)!important}#toast-container>.toast-success{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAADsSURBVEhLY2AYBfQMgf///3P8+/evAIgvA/FsIF+BavYDDWMBGroaSMMBiE8VC7AZDrIFaMFnii3AZTjUgsUUWUDA8OdAH6iQbQEhw4HyGsPEcKBXBIC4ARhex4G4BsjmweU1soIFaGg/WtoFZRIZdEvIMhxkCCjXIVsATV6gFGACs4Rsw0EGgIIH3QJYJgHSARQZDrWAB+jawzgs+Q2UO49D7jnRSRGoEFRILcdmEMWGI0cm0JJ2QpYA1RDvcmzJEWhABhD/pqrL0S0CWuABKgnRki9lLseS7g2AlqwHWQSKH4oKLrILpRGhEQCw2LiRUIa4lwAAAABJRU5ErkJggg==)!important}#toast-container>.toast-warning{background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAGYSURBVEhL5ZSvTsNQFMbXZGICMYGYmJhAQIJAICYQPAACiSDB8AiICQQJT4CqQEwgJvYASAQCiZiYmJhAIBATCARJy+9rTsldd8sKu1M0+dLb057v6/lbq/2rK0mS/TRNj9cWNAKPYIJII7gIxCcQ51cvqID+GIEX8ASG4B1bK5gIZFeQfoJdEXOfgX4QAQg7kH2A65yQ87lyxb27sggkAzAuFhbbg1K2kgCkB1bVwyIR9m2L7PRPIhDUIXgGtyKw575yz3lTNs6X4JXnjV+LKM/m3MydnTbtOKIjtz6VhCBq4vSm3ncdrD2lk0VgUXSVKjVDJXJzijW1RQdsU7F77He8u68koNZTz8Oz5yGa6J3H3lZ0xYgXBK2QymlWWA+RWnYhskLBv2vmE+hBMCtbA7KX5drWyRT/2JsqZ2IvfB9Y4bWDNMFbJRFmC9E74SoS0CqulwjkC0+5bpcV1CZ8NMej4pjy0U+doDQsGyo1hzVJttIjhQ7GnBtRFN1UarUlH8F3xict+HY07rEzoUGPlWcjRFRr4/gChZgc3ZL2d8oAAAAASUVORK5CYII=)!important}#toast-container.toast-bottom-center>div,#toast-container.toast-top-center>div{width:300px;margin-left:auto;margin-right:auto}#toast-container.toast-bottom-full-width>div,#toast-container.toast-top-full-width>div{width:96%;margin-left:auto;margin-right:auto}.toast{background-color:#030303}.toast-success{background-color:#51A351}.toast-error{background-color:#BD362F}.toast-info{background-color:#2F96B4}.toast-warning{background-color:#F89406}.toast-progress{position:absolute;left:0;bottom:0;height:4px;background-color:#000;opacity:.4;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=40);filter:alpha(opacity=40)}@media all and (max-width:240px){#toast-container>div{padding:8px 8px 8px 50px;width:11em}#toast-container>div.rtl{padding:8px 50px 8px 8px}#toast-container .toast-close-button{right:-.2em;top:-.2em}#toast-container .rtl .toast-close-button{left:-.2em;right:.2em}}@media all and (min-width:241px) and (max-width:480px){#toast-container>div{padding:8px 8px 8px 50px;width:18em}#toast-container>div.rtl{padding:8px 50px 8px 8px}#toast-container .toast-close-button{right:-.2em;top:-.2em}#toast-container .rtl .toast-close-button{left:-.2em;right:.2em}}@media all and (min-width:481px) and (max-width:768px){#toast-container>div{padding:15px 15px 15px 50px;width:25em}#toast-container>div.rtl{padding:15px 50px 15px 15px}}
</style>
