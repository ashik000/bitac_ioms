<template>
    <span>

            <div class="card-wrapper row">
                <div class="col-3 h-100">

                    <SettingsList sectionHeader="Station Groups" :items="groups" @action-clicked="openGroupAddModal" buttonText="Add Stations Group">
                        <template v-slot="{ item }">
                                {{ item.name }}
                            <span style="float: right;">
                                  <button type="button" class="btn btn-primary btn-sm" @click.prevent="showGroupEditModal(item)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                      <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"></path>
                                      <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"></path>
                                    </svg>
                                    Edit
                                  </button>

                                  <button type="button" class="btn btn-danger btn-sm" @click.prevent="showGroupDeleteModal(item)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                      <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"></path>
                                      <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"></path>
                                    </svg>
                                    Delete
                                  </button>
                            </span>
                        </template>
                    </SettingsList>
                </div>


                <div class="col-9 h-100">
                    <SettingsTable :items="stations"
                                   sectionHeader="Stations"
                                   :selected-id="selectedStationId"
                                   @action-clicked="openStationAddModal">
                        <template v-slot:columnHeaders>

                        </template>

                        <template v-slot:row="{ row }">
                            <div class="accordion-item">

                              <h2 class="accordion-header" id="panelsStayOpen-headingOne" @click="setSelectedStationId(row)">
                                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="false" aria-controls="panelsStayOpen-collapseOne" @click="setSelectedStationId(row)">
                                    {{ row.name }}
                                  </button>

                                  <button type="button" class="btn btn-secondary btn-sm">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                                          <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"></path>
                                          <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"></path>
                                      </svg>
                                        MANAGE COMPONENTS
                                  </button>
                                  <button type="button" class="btn btn-primary btn-sm" @click.prevent="showStationEditModal(row)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                      <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"></path>
                                      <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"></path>
                                    </svg>
                                    EDIT CONFIGURATION
                                  </button>
                                  <button type="button" class="btn btn-danger btn-sm" @click.prevent="showStationDeleteModal(row)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                      <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"></path>
                                      <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"></path>
                                    </svg>
                                    Delete
                                  </button>
                              </h2>

                              <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse" >
                                <div class="accordion-body">
                                        <strong>This is the first item's accordion body.</strong>
                                         It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element.
                                          These classes control the overall appearance, as well as the showing and hiding via CSS transitions.
                                          You can modify any of this with custom CSS or overriding our default variables.
                                          It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                                </div>
                              </div>

                            </div>
                        </template>
                    </SettingsTable>
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
    </span>

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
