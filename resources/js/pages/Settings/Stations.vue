<template>
    <div>
        <div>
            <h3 class="page-header">Manage Stations</h3>
            <div class="row">
                <aside class="section col-4">
                    <SettingsList sectionHeader="Station Groups" :items="groups" @action-clicked="openGroupAddModal">
                        <template v-slot="{ item }">
                            <div class="d-flex justify-content-between align-items-center">
                                {{ item.name }}
                                <a class="btn btn-link" style="margin-left: auto">
                                    <i class="material-icons" style="color: #e6e6e6;" @click.prevent="showGroupEditModal(item)">
                                        edit
                                    </i>
                                </a>
                                <a class="btn btn-link">
                                    <i class="material-icons" style="color: #e6e6e6;" @click.prevent="showGroupDeleteModal(item)">
                                        delete
                                    </i>
                                </a>
                            </div>
                        </template>
                    </SettingsList>
                </aside>
                <section class="section col-8" style="height: 500px;overflow-y: scroll">
                    <SettingsTable :items="stations"
                                   sectionHeader="Stations"
                                   :selected-id="selectedStationId"
                                   @action-clicked="openStationAddModal">
                        <template v-slot:columnHeaders>

                        </template>
                        <div></div>
                        <template v-slot:row="{ row }">
                            <td>
                                <div class="d-flex justify-content-between align-items-center" @click="setSelectedStationId(row)">
                                    {{ row.name }}
                                    <a class="btn btn-link" style="margin-left: auto">
                                        <i class="material-icons" style="color: #e6e6e6;" @click.prevent="showStationEditModal(row)">
                                            edit
                                        </i>
                                    </a>
                                    <a class="btn btn-link">
                                        <i class="material-icons" style="color: #e6e6e6;" @click.prevent="showStationDeleteModal(row)">
                                            delete
                                        </i>
                                    </a>
                                </div>
                            </td>
                        </template>
                    </SettingsTable>
                </section>
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
                        <button class="btn btn-primary" >Submit</button>
                    </form>
                </template>
                <template v-slot:footer>
                    <button class="btn btn-danger" @click="showGroupForm = false">Close</button>
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
                            <label>Name</label>
                            <input type="text" v-model="name" class="form-control" placeholder="Enter Name">
                            <label>Group</label>
                            <select class="form-control" v-model="selectedGroupId">
                                <option v-for="group in groups" :value="group.id">
                                    {{ group.name }}
                                </option>
                            </select>
                            <label>Description</label>
                            <input type="text" v-model="description" class="form-control" placeholder="Enter Description">
                            <label>Performance Threshold</label>
                            <input type="number" v-model="oee_threshold" class="form-control" placeholder="Enter Performance Threshold">
                        </div>
                        <button class="btn btn-primary" >Submit</button>
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
        </div>
        <div v-if="showConfigureStation">
            <h3 class="page-header">
                Configure Station
            </h3>
            <div class="row">
                <div class="col-md-6">
                    <StationProduct :station-id="selectedStationId"></StationProduct>
                </div>
                <div class="col-md-3">
                    <StationShift :station-id="selectedStationId"></StationShift>
                </div>
                <div class="col-md-3">
                    <StationOperator :station-id="selectedStationId"></StationOperator>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import stationService from '../../services/StationsService'
    import groupMixin from '../../mixins/groupMixin';
    import StationProduct from "../../components/station/StationProduct";
    import StationOperator from "../../components/station/StationOperator";
    import StationShift from "../../components/station/StationShift";

    export default {
        name: "Stations",
        components: {StationOperator, StationShift, StationProduct},
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
                    this.closeGroupForm();
                }, e => {
                    console.log(e);
                });
            },
            deleteGroup(){
                stationService.deleteGroup(this.groupId, r =>{
                    this.groups = r;
                    this.closeGroupForm();
                }, e =>{
                    console.log(e);
                })
            },
            createGroup() {
                stationService.addGroup({name: this.groupName}, data => {
                    this.groups = data;
                    this.showGroupForm = false;
                });
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
                });
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
                });
            },
            deleteStations() {
                stationService.deleteStation(this.stationId,r =>{
                    this.stations = r;
                    this.closeStationForm();
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
            }
        },
        computed : {
          showConfigureStation(){
              return this.selectedStationId != 0;
          }
        },
        mounted() {
            stationService.fetchAllGroups(groups => {
                this.groups = groups;
            });
            stationService.fetchAll([], products => {
                this.stations = products;
            })
        }
    }
</script>

<style scoped lang="scss">

</style>
