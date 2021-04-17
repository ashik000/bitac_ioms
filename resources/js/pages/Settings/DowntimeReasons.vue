<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div>
        <h3 class="page-header">Manage Downtime Reasons</h3>
        <div class="row">
            <aside class="section col-3">
                <SettingsList sectionHeader="Reason Groups"
                              :items="groups"
                              @action-clicked="openGroupAddModal"
                              @item-selected="">
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
            <section class="section col-9">
                <SettingsTable :items="reasons"
                               sectionHeader="Reasons"
                               @action-clicked="showReasonForm = true">
                    <template v-slot:columnHeaders>
                    </template>
                    <div></div>
                    <template v-slot:row="{ row }">
                        <td>
                            <div class="d-flex justify-content-between align-items-center">
                                {{ row.name }}
                            <a class="btn btn-link" style="margin-left: auto">
                                <i class="material-icons" style="color: #e6e6e6;" @click.prevent="showDowntimeEditModal(row)">
                                    edit
                                </i>
                            </a>
                            <a class="btn btn-link">
                                <i class="material-icons" style="color: #e6e6e6;" @click.prevent="showReasonDeleteModal(row)">
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
                    Add Group
                </div>
            </template>
            <template v-slot:content>
                <form @submit.prevent="groupId == null? createGroup() : updateGroup() ">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" v-model="groupName" class="form-control" id="name" placeholder="Enter Name">
                    </div>
                    <button class="btn btn-primary" >Submit</button>
                </form>
            </template>
            <template v-slot:footer>
            </template>
        </Modal>

        <Modal v-if="showReasonForm" @close="closeShowReasonForm">
            <template v-slot:header>
                <div class="container">
                    Add Reason
                </div>
            </template>
            <template v-slot:content>
                <form @submit.prevent="reasonId == null ? createDowntimeReason():updateDowntimeReason()">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" v-model="reasonName" class="form-control" placeholder="Enter Name">
                        <label>Group</label>
                        <select class="form-control" v-model="selectedGroupId">
                            <option v-for="group in groups" :value="group.id">
                                {{ group.name }}
                            </option>
                        </select>
                        <label>Type</label>
                        <select v-model="type" class="form-control">
                            <option value="planned">Planned</option>
                            <option value="unplanned">Unplanned</option>
                        </select>
                    </div>
                    <button class="btn btn-primary" >Submit</button>
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
    </div>
</template>

<script>
    import downtimeReasonsService from '../../services/DowntimeReasons'
    import groupMixin from '../../mixins/groupMixin'
    export default {
        name: "DowntimeReasons",
        mixins:[groupMixin],
        data: () => ({
            showReasonForm: false,
            showReasonDeleteForm : false,
            reasonName:"",
            type:"",
            selectedGroupId : null,
            groups: [],
            reasons: [],
            reasonId : null,
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
              this.showReasonForm = true;
              this.reasonId = item.id;
              this.reasonName = item.name;
              this.type = item.type;
              this.selectedGroupId = item.reason_group_id;
              console.log(JSON.stringify(item));
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
            }
        },
        mounted() {
            downtimeReasonsService.fetchAllGroups(groups => {
                this.groups = groups;
            });
            downtimeReasonsService.fetchAll([], reasons => {
                this.reasons = reasons;
            })
        }
    }
</script>

<style scoped lang="scss">

</style>
