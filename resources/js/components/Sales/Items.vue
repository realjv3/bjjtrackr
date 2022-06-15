<template>
    <div>
        <v-row v-for="(item, i) in items" class="mx-2" :key="i">

            <v-col cols="2">
                <div class="text-body-1">{{item.count}}</div>
            </v-col>

            <v-col v-if="item.unit" cols="3">
                <div class="text-body-1">{{item.unit}}</div>
            </v-col>

            <v-col cols="5">
                <div class="text-body-1">{{item.product}}</div>
            </v-col>

            <v-col cols="2">
                <div class="text-body-1">${{(item.price / 100).toFixed(2)}}</div>
            </v-col>

        </v-row>
    </div>
</template>

<script>
export default {
    name: 'Items',
    props: {
        items: {type: Array, required: true}
    }
}
</script>
