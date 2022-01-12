<template>
    <div class="col-md-3 mb-2">
        <div class="card shadow rounded-2 mx-2">
            <div class="card-body d-flex justify-content-between">
                <a href="/accuracy?machine_id=1" class="btn btn-sm me-1 col rounded-1 btn-danger">PSM 14</a>
                <a href="/accuracy?machine_id=3" class="btn btn-sm ms-1 col rounded-1 btn-success">Boones 23</a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ChartCard",
    components: {
    },
    data: () => ({
    }),
}
</script>
