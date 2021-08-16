<template>
    <span>

        <div class="card-wrapper row">
            <div class="col-3 h-100">
                <StationGroup sectionHeader="Station Groups" :items="groups" @action-clicked="openGroupAddModal" buttonText="Add Stations Group">
                    <template v-slot="{ item }">
                        <span class="hide_overflow_text" @click.prevent="loadGroupData(item.id)">
                            {{ item.name }}
                        </span>
                        <span style="float: right;">
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


                <div class="col-9 h-100">
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
                                        <button type="button" class="btn btn-secondary btn-sm" >
                                            <b-icon icon="gear" class="pb-sm-1" font-scale="1.30"></b-icon> MANAGE COMPONENTS
                                        </button>
                                        <button type="button" class="btn btn-primary btn-sm" @click.prevent="showStationEditModal(row)">
                                            <b-icon icon="pencil-square" class="pb-sm-1" font-scale="1.30"></b-icon>  EDIT CONFIGURATION
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" @click.prevent="showStationDeleteModal(row)">
                                            <b-icon icon="trash" class="pb-sm-1" font-scale="1.30"></b-icon> DELETE
                                        </button>
                                      </div>
                                  </h2>

                                  <div :id="'station-details' + row.id" class="accordion-collapse collapse" >
                                    <div class="accordion-body">
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
                    <div class="container">
                        Delete Group
                    </div>
                </template>
                <template v-slot:content>
                    <form @submit.prevent="deleteGroup">
                        <p>Are you sure you want to delete the group named <span style="color: darkred">{{groupName}}</span>?</p>
                        <button class="btn btn-danger" >Submit</button>
                    </form>
                </template>
                <template v-slot:footer>
                </template>
            </Modal>
            <Modal v-if="showGroupForm" @close="closeGroupForm">
                <template v-slot:header>
                    <div class="container">
                        Add Station Group
                    </div>
                </template>
                <template v-slot:content>
                    <form @submit.prevent="groupId == null ? createGroup():updateGroup()">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" v-model="groupName" class="form-control" id="name" placeholder="Enter Name">
                        </div>
                        <button class="btn btn-primary mt-2" >Submit</button>
                    </form>
                </template>
            </Modal>
            <Modal v-if="showStationForm" @close="closeStationForm">
                <template v-slot:header>
                    <div class="container">
                        {{selectedStationId != 0 ? "Edit Station" : "Add Station"}}
                    </div>
                </template>
                <template v-slot:content>
                    <form @submit.prevent="stationId == null? createStation():updateStation()">
                        <div class="form-group">
                            <label class="mt-2">Name</label>
                            <input type="text" v-model="name" class="form-control" placeholder="Enter Name">
                            <label class="mt-2">Group</label>
                            <select class="form-control" v-model="selectedGroupId">
                                <option v-for="group in groups" :value="group.id">
                                    {{ group.name }}
                                </option>
                            </select>
                            <label class="mt-2">Description</label>
                            <input type="text" v-model="description" class="form-control" placeholder="Enter Description">
                            <label class="mt-2">Performance Threshold</label>
                            <input type="number" v-model="oee_threshold" class="form-control" placeholder="Enter Performance Threshold">
                        </div>
                        <button class="btn btn-primary mt-2" >Submit</button>
                    </form>
                </template>
                <template v-slot:footer>
                </template>
            </Modal>
            <Modal v-if="showStationDeleteForm" @close="closeStationForm">
                <template v-slot:header>
                    <div class="container">
                        Delete Station
                    </div>
                </template>
                <template v-slot:content>
                    <form @submit.prevent="deleteStations">
                        <p>Are you sure you want to delete the station named <span style="color: darkred">{{name}}</span>?</p>
                        <button class="btn btn-danger">Submit</button>
                    </form>
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
        selectedGroupId : null,
        oee_threshold:0,
        groups: [],
        stations: [],
        selectedStationId:0,
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
            stationService.updateGroup(this.groupId,{name:this.groupName}, r=>{
                this.groups = r;
                toastrService.showSuccessToast('Station group updated.');
                this.closeGroupForm();
            }, error => {
                toastrService.showErrorToast(error);
                // console.log(error);
            });
        },
        deleteGroup(){
            stationService.deleteGroup(this.groupId, r =>{
                this.groups = r;
                toastrService.showSuccessToast('Station group deleted.');
                this.closeGroupForm();
            }, error =>{
                toastrService.showErrorToast(error);
                // console.log(e);
            })
        },
        createGroup() {
            stationService.addGroup({name: this.groupName}, data => {
                this.groups = data;
                this.showGroupForm = false;
                toastrService.showSuccessToast('Station group created.');
            }, error => {
                toastrService.showErrorToast(error);
            })
        },
        openStationAddModal(){
            this.showStationForm = true;
        },
        createStation(){
            stationService.addStation({
                name:this.name,
                station_group_id: this.selectedGroupId,
                oee_threshold : this.oee_threshold,
                description : this.description
            }, data => {
                this.stations = data;
                this.showStationForm = false;
                this.closeStationForm();
                toastrService.showSuccessToast('Station added.');
            }, error => {
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
            stationService.updateStation(this.stationId,{
                name:this.name,
                station_group_id: this.selectedGroupId,
                oee_threshold : this.oee_threshold,
                description : this.description
            }, data => {
                this.stations = data;
                this.showStationForm = false;
                toastrService.showSuccessToast('Station updated.');
            }, error => {
                toastrService.showErrorToast(error);
            })
        },
        deleteStations() {
            stationService.deleteStation(this.stationId,r =>{
                this.stations = r;
                this.closeStationForm();
                toastrService.showSuccessToast('Station deleted.');
            }, error => {
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
            console.log("Selected station id " + item.id);
            this.selectedStationId = item.id;
        },
        loadGroupData(groupId){
            stationService.fetchAllStationsByGroupId(groupId, stations => {
                this.stations = stations;
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
            this.groups = groups;

            stationService.fetchAllStationsByGroupId(this.groups[0].id, stations => {
                console.log(stations);
                this.stations = stations;
            });
        });
    }
}
</script>
