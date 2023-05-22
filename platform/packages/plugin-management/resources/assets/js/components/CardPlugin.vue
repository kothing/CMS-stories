<template>

    <marketplace-card :data="plugin">
    </marketplace-card>

</template>

<script>

import Card from '@base.marketplace/components/Card/Card.vue';

export default {
    name: 'marketplace-card-plugin',
    props: {
        plugin: [],
    },
    components: {
        'marketplace-card': Card
    },

}
</script>
