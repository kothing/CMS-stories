<template>
    <div class="input-group justify-content-end">
        <input type="text"
               v-model="keyword"
               class="form-control"
               :placeholder="__('base.keyword')"
               :aria-label="__('base.keyword')"
               aria-describedby="basic-addon2" style="max-width: 200px">

        <div class="input-group-append">
            <button @click="search" class="btn btn-primary">{{ __('base.search') }}</button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'marketplace-layout-search',
    data() {
        return {
            keyword: '',
        }
    },
    methods: {
        search() {
            this.$emit('search', this.keyword)
        }
    }
}
</script>
