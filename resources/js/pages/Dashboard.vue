<template>
    <div>
        {{machineStatus.station_id}}
    </div>

</template>

<script>

export default {
    name: "Dashboard",

    data(){
        return{
            machineStatus: [],
            // station_id: '',
            // spindle_speed: '',
            // feed_rate: '',
            // machine_uptime: '',
            // alarm_code: '',
            // alarm_info: '',
            // program_number: '',
            // program_name: '',
            // cycle_time: '',
            // counter_number: '',
            // power_status: '',
            // produced_at: '',
        }
    },

    methods:{
        getData()
        {
            axios.get('/getMachineStatus')
                .then((response)=>{
                    console.log(response)
                    this.machineStatus = response.data
                })
        }
    },

    created: function (){
        this.getData()
        setInterval(() => {this.getData()}, 5000)
    },
}
</script>

<style scoped>

</style>
