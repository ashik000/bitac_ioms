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
                            <a class="btn-sm btn-primary me-1 anchor_btn">
                                <i class="material-icons" @click.prevent="showGroupEditModal(item)">
                                    edit
                                </i>
                            </a>
                            <a class="btn-sm btn-danger anchor_btn">
                                <i class="material-icons" @click.prevent="showGroupDeleteModal(item)">
                                    delete
                                </i>
                            </a>
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
                                <a class="btn btn-primary anchor_btn">
                                    <i class="material-icons" @click.prevent="showDowntimeEditModal(row)">
                                        edit
                                    </i>
                                </a>
                                <a class="btn btn-danger anchor_btn">
                                    <i class="material-icons" @click.prevent="showReasonDeleteModal(row)">
                                        delete
                                    </i>
                                </a>
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
    import downtimeReasonsService from '../../services/DowntimeReasons'
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
        }),
        methods: {
            deleteReason(){
              downtimeReasonsService.deleteReason(this.reasonId, data=>{
                  this.reasons = data;
                  this.closeShowReasonForm();
              })
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
                downtimeReasonsService.updateReason(this.reasonId, {
                    name:this.reasonName,
                    reason_group_id:this.selectedGroupId,
                    type:this.type
                },data=>{
                    this.reasons = data;
                    this.showReasonForm = false;
                    this.reasonId = null;
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
                downtimeReasonsService.deleteGroup(this.groupId, r =>{
                    this.groups = r;
                    this.closeGroupForm();
                }, e =>{
                    console.log(e);
                })
            },
            updateGroup(){
                downtimeReasonsService.updateGroup(this.groupId,{name:this.groupName}, r=>{
                    this.groups = r;
                    this.closeGroupForm();
                }, e => {
                });
            },
            createGroup() {
                downtimeReasonsService.addGroup({name: this.groupName}, data => {
                    this.groups = data;
                    this.showGroupForm = false;
                });
                this.clearReasonGroup();
            },
            createDowntimeReason(){
                downtimeReasonsService.addReason({
                    name:this.reasonName,
                    reason_group_id:this.selectedGroupId,
                    type:this.type
                },data=>{
                    this.reasons = data;
                    this.showReasonForm = false;
                });
                this.clearDowntimeReason();
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
                // console.log(this.groups);
                downtimeReasonsService.fetchAllDowntimeReasonsByGroupId(this.groups[0].id, reasons => {
                    console.log(reasons);
                    this.reasons = reasons['downtime_reason_list'];
                });
            });
            // downtimeReasonsService.fetchAll([], reasons => {
            //     this.reasons = reasons['downtime_reason_list'];
            // })

        }
    }
</script>

