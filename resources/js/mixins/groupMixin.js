export default   {
    data: () => ({
        showGroupForm: false,
        groupName:"",
        groupId:null,
        showGroupDeleteForm : false,
    }),
    methods:{
        showGroupDeleteModal(item){
            this.showGroupDeleteForm = true;
            this.groupName = item.name;
            this.groupId = item.id;
        },
        closeGroupForm(){
            this.groupId = null;
            this.showGroupForm = false;
            this.showGroupDeleteForm = false;
            this.groupName = "";
        },
        showGroupEditModal(item){
            this.modalTitleText = "Edit";
            this.showGroupForm = true;
            this.groupName = item.name;
            this.groupId = item.id;
        },
        openGroupAddModal() {
            this.modalTitleText = "Add";
            this.showGroupForm = true;
        }
    }
};
