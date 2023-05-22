<template>
    <marketplace-layout v-slot:default="props">
        <div class="row">
            <marketplace-card-plugin v-for="plugin in props.list.data" :key="plugin.id" :plugin="plugin"></marketplace-card-plugin>
        </div>
    </marketplace-layout>
</template>

<script>

import Layout from '@base.marketplace/components/Layout.vue';

export default {
    components: {
        'marketplace-layout': Layout,
    }
}
</script>
