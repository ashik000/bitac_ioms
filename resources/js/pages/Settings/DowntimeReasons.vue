<template>
    <span>
        <div class="card-wrapper row y-scroll">
            <aside class="section col-md-3 col-sm-12 pt-md-2 pt-4 pb-4">
                <DowntimeReasonGroup sectionHeader="Reason Groups"
                              :items="groups"
                              @action-clicked="openGroupAddModal"
                              @item-selected="" buttonText="Add Reason Group">
                    <template v-slot="{ item }">
                        <span class="hide_overflow_text anchor_btn" @click.prevent="loadGroupData(item.id)">
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
                </DowntimeReasonGroup>
            </aside>
            <section class="section col-md-9 col-sm-12 pt-md-2 pt-4 pb-4">
                <DowntimeReasonList :items="reasons"
                               sectionHeader="Reasons"
                               @action-clicked="showReasonForm = true">
<!--                    <template v-slot:columnHeaders>-->
<!--                    </template>-->

                    <template v-slot:row="{ row }">
                        <div class="d-flex justify-content-between align-items-center">
                            {{ row.name }}
                            <span style="float: right;">
                                <button type="button" class="btn btn-primary btn-sm" @click.prevent="showDowntimeEditModal(row)">
                                    <b-icon icon="pencil-square" class="pb-sm-1" font-scale="1.30"></b-icon> EDIT
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" @click.prevent="showReasonDeleteModal(row)">
                                    <b-icon icon="trash" class="pb-sm-1" font-scale="1.30"></b-icon> DELETE
                                </button>
                            </span>
                        </div>
                    </template>
                </DowntimeReasonList>
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
                <b-overlay :show="showInprogress" opacity="0.6" no-wrap></b-overlay>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>

        <Modal v-if="showGroupForm" @close="closeGroupForm">
            <template v-slot:header>
                <div class="container">
                    {{ modalTitleText }} Group
                </div>
            </template>
            <template v-slot:content>
                <form @submit.prevent="groupId == null? createGroup() : updateGroup() ">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" v-model="groupName" class="form-control" id="name" placeholder="Enter Name">
                    </div>
                    <button class="btn btn-primary mt-2" >Submit</button>
                </form>
                <b-overlay :show="showInprogress" opacity="0.6" no-wrap></b-overlay>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>

        <Modal v-if="showReasonForm" @close="closeShowReasonForm">
            <template v-slot:header>
                <div class="container card-title">
                    {{ modalTitleText }} Reason
                </div>
            </template>
            <template v-slot:content>
                <form @submit.prevent="reasonId == null ? createDowntimeReason():updateDowntimeReason()">
                    <div class="form-group">
                        <label class="mt-2">Name</label>
                        <input type="text" v-model="reasonName" class="form-control" placeholder="Enter Name">
                        <label class="mt-2">Group</label>
                        <select class="form-control" v-model="selectedGroupId">
                            <option disabled value="">--Select--</option>
                            <option v-for="group in groups" :value="group.id">
                                {{ group.name }}
                            </option>
                        </select>
                        <label class="mt-2">Type</label>
                        <select v-model="type" class="form-control">
                            <option disabled value="">--Select--</option>
                            <option value="planned">Planned</option>
                            <option value="unplanned">Unplanned</option>
                        </select>
                    </div>
                    <button class="btn btn-primary mt-2" >Submit</button>
                </form>
                <b-overlay :show="showInprogress" opacity="0.6" no-wrap></b-overlay>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>

        <Modal v-if="showReasonDeleteForm" @close="closeShowReasonForm">
            <template v-slot:header>
                <div class="container">
                    Delete Reason
                </div>
            </template>
            <template v-slot:content>
                <form @submit.prevent="deleteReason">
                    <p>Are you sure you want to delete the reason named <span style="color: darkred">{{reasonName}}</span>?</p>
                    <button class="btn btn-danger">Submit</button>
                </form>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>
    </span>
</template>

<script>
    import DowntimeReasonGroup from "../../components/settings/DowntimeReasonGroup";
    import DowntimeReasonList from "../../components/settings/DowntimeReasonList";
    import downtimeReasonsService from '../../services/DowntimeReasons';
    import toastrService from '../../services/ToastrService';
    import groupMixin from '../../mixins/groupMixin'

    export default {
        name: "DowntimeReasons",
        components: {DowntimeReasonGroup, DowntimeReasonList},
        mixins:[groupMixin],
        data: () => ({
            showReasonForm: false,
            showReasonDeleteForm : false,
            reasonName:"",
            type:"",
            selectedGroupId : "",
            groups: [],
            reasons: [],
            reasonId : null,
            modalTitleText: "Add",
            showInprogress: false,
        }),
        methods: {
            deleteReason(){
                this.showInprogress = true;
                downtimeReasonsService.deleteReason(this.reasonId, data=>{
                    this.reasons = data;
                    this.closeShowReasonForm();
                    this.showInprogress = false;
                    toastrService.showSuccessToast('Downtime reason group deleted.');
                }, error => {
                    this.showInprogress = false;
                    toastrService.showErrorToast(error);
                });
            },
            showReasonDeleteModal(row){
              this.showReasonDeleteForm = true;
              this.reasonId = row.id;
              this.reasonName = row.name;
            },
            closeShowReasonForm(){
              this.showReasonForm = false;
              this.showReasonDeleteForm = false;
              this.reasonId = null;
              this.reasonName = null;
              this.selectedGroupId = null;
              this.type = null;
              this.modalTitleText = "Add";
            },
            updateDowntimeReason(){
                this.showInprogress = true;
                downtimeReasonsService.updateReason(this.reasonId, {
                    name:this.reasonName,
                    reason_group_id:this.selectedGroupId,
                    type:this.type
                },data=>{
                    this.reasons = data;
                    this.showReasonForm = false;
                    this.reasonId = null;
                    this.showInprogress = false;
                    toastrService.showSuccessToast('Downtime reason group updated.');
                }, error => {
                    this.showInprogress = false;
                    toastrService.showErrorToast(error);
                });
            },
            showDowntimeEditModal(item){
                this.modalTitleText = "Edit";
                this.showReasonForm = true;
                this.reasonId = item.id;
                this.reasonName = item.name;
                this.type = item.type;
                this.selectedGroupId = item.reason_group_id;
                // console.log(JSON.stringify(item));
            },
            deleteGroup(){
                this.showInprogress = true;
                downtimeReasonsService.deleteGroup(this.groupId, r =>{
                    this.groups = r;
                    this.closeGroupForm();
                    this.showInprogress = false;
                    toastrService.showSuccessToast('Downtime reason group deleted.');
                }, error =>{
                    this.showInprogress = false;
                    toastrService.showErrorToast(error);
                    // console.log(error);
                })
            },
            updateGroup(){
                this.showInprogress = true;
                downtimeReasonsService.updateGroup(this.groupId,{name:this.groupName}, r=> {
                    this.groups = r;
                    this.showInprogress = false;
                    toastrService.showSuccessToast('Downtime reason updated.');
                    this.closeGroupForm();
                }, error => {
                    this.showInprogress = false;
                    toastrService.showErrorToast(error);
                });
            },
            createGroup() {
                this.showInprogress = true;
                downtimeReasonsService.addGroup({name: this.groupName}, data => {
                    this.groups = data;
                    this.showGroupForm = false;
                    this.showInprogress = false;
                    toastrService.showSuccessToast('Downtime reason group added.');
                    this.clearReasonGroup();
                }, error => {
                    this.showInprogress = false;
                    toastrService.showErrorToast(error);
                });
            },
            createDowntimeReason(){
                this.showInprogress = true;
                downtimeReasonsService.addReason({
                    name:this.reasonName,
                    reason_group_id:this.selectedGroupId,
                    type:this.type
                },data=>{
                    this.reasons = data;
                    this.showReasonForm = false;
                    this.showInprogress = false;
                    toastrService.showSuccessToast('Downtime reason added.');
                    this.clearDowntimeReason();
                }, error => {
                    this.showInprogress = false;
                    toastrService.showErrorToast(error);
                });
            },
            clearDowntimeReason(){
                this.reasonName = "";
                this.selectedGroupId = null;
                this.type = "";
            },
            clearReasonGroup(){
                this.groupName = "";
            },
            loadGroupData(groupId){
                // console.log(groupId);
                downtimeReasonsService.fetchAllDowntimeReasonsByGroupId(groupId, reasons => {
                    this.reasons = reasons['downtime_reason_list'];
                });
            },
        },
        mounted() {
            downtimeReasonsService.fetchAllGroups(groups => {
                this.groups = groups;
                downtimeReasonsService.fetchAllDowntimeReasonsByGroupId(this.groups[0].id, reasons => {
                    this.reasons = reasons['downtime_reason_list'];
                });
            });
            // downtimeReasonsService.fetchAll([], reasons => {
            //     this.reasons = reasons['downtime_reason_list'];
            // })

        }
    }
</script>

