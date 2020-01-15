<template>
    <v-data-table
        :headers="headers"
        :items="items"
        :items-per-page="5"
        class="elevation-1"
    />
</template>

<script>
	export default {
		name: "People",
        data: () => ({
            headers: [
                { text: 'Name', align: 'left', value: 'name' },
                { text: 'Belt', value: 'belt' },
                { text: 'Email', value: 'email' },
                { text: 'Last Check-in', value: 'lastcheckin' },
            ],
            items: [],
        }),
	}
</script>

<style scoped>

</style>
