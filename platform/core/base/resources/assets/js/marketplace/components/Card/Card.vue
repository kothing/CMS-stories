<template>
    <div class="p-3 col-lg-3 col-md-6 col-sm-6 col-12" aria-hidden="true">
        <div class="card h-100">
            <img :src="data.image_url" class="card-img-top" :alt="data.name">
            <div class="card-body">
                <h5 class="card-title">{{ data.name }}</h5>
                <p class="card-text text-truncate">{{ data.description }}</p>
                <span class="badge rounded-pill bg-primary">{{ __('base.version') }} {{ data.latest_version }}</span>
                <span class="badge rounded-pill bg-primary">{{
                        __('base.minimum_core_version')
                    }} {{ data.minimum_core_version }}</span>

                <p class="mt-2 card-text d-flex">
                    <small class="text-muted">
                        {{ __('base.last_update') }}:
                        <TimeAgo refresh :datetime="data.last_updated_at" long tooltip></TimeAgo>
                    </small>

                    <Rating :count="data.ratings_count" :avg="data.ratings_avg"></Rating>
                </p>

                <Compatible v-if="versionCheck"></Compatible>

            </div>

            <div class="card-footer d-flex">
                <button v-if="!installed"
                        class="btn btn-warning"
                        @click.prevent="install()"
                >
                    <span v-if="!installing"><i class="fa-solid fa-download"></i> {{ __('base.install_now') }}</span>
                    <span v-else><i class="fas fa-circle-notch fa-spin"></i> {{ __('base.installing') }}</span>
                </button>

                <button v-if="installed && !activated" class="btn btn-success"
                        @click.prevent="changeStatus()">
                    <span v-if="!activating"><i class="fa-solid fa-check"></i> {{ __('base.activate') }}</span>
                    <span v-else><i class="fas fa-circle-notch fa-spin"></i> {{ __('base.activating') }}</span>
                </button>

                <button v-if="installed && activated" class="btn btn-info btn-disabled" disabled="disabled">
                    <span>{{ __('base.activated') }}</span>
                </button>

                <button type="button"
                        class="btn btn-secondary ms-auto"
                        @click.prevent="detail()"
                >
                    <i class="fa-solid fa-asterisk"></i> {{ __('base.detail') }}
                </button>

            </div>
        </div>
    </div>

</template>

<script>

import {TimeAgo} from 'vue2-timeago';
import 'vue2-timeago/dist/vue2-timeago.css';
import Rating from '@base.marketplace/components/Card/Rating.vue';
import Compatible from '@base.marketplace/components/Card/Compatible.vue';

export default {
    name: 'marketplace-card',
    data() {
        return {
            versionCheck: false,
            installing: false,
            installed: false,
            activating: false,
            activated: false,
            pluginName: '',
        }
    },
    props: {
        data: []

    },
    components: {
        TimeAgo,
        Rating,
        Compatible
    },
    created() {
        vueApp.eventBus.$on('assignInstalled', this.assignInstalled);
        vueApp.eventBus.$on('assignActivated', this.assignActivated);

        this.setNamePlugin();
        this.checkVersion();
        this.checkInstalled();
        this.checkActivated();
    },
    methods: {
        setNamePlugin() {
            const packageName = this.data.package_name;
            this.pluginName = packageName.substring(packageName.indexOf('/') + 1);
        },
        detail() {
            vueApp.eventBus.$emit('detail', this.data);
        },
        install() {
            this.installing = true;
            vueApp.eventBus.$emit('install', this.data.id);
        },
        changeStatus() {
            if (!this.activated) {
                this.activating = true;
                vueApp.eventBus.$emit('changeStatus', this.pluginName);
            }
        },
        assignInstalled(name) {
            const size = Object.keys(window.marketplace.installed).length;
            if (this.pluginName === name) {
                this.installing = false;
                window.marketplace.installed[size] = this.pluginName;
            }
            this.checkInstalled()
        },
        assignActivated(name) {
            const size = Object.keys(window.marketplace.activated).length;
            if (this.pluginName === name) {
                this.activated = false;
                window.marketplace.activated[size] = this.pluginName;
            }
            this.checkActivated()
        },
        checkVersion() {
            const requiredVer = this.data.minimum_core_version.split('.')
            const cmsVer = window.marketplace.coreVersion;

            for (let i = 0; i < cmsVer.length; i++) {
                const cms = ~~cmsVer[i];
                const require = ~~requiredVer[i];
                if (cms > require) {
                    return this.versionCheck = true;
                } else if (cms < require) {
                    return this.versionCheck = false;
                }
            }

            return this.versionCheck = false;
        },
        checkInstalled() {
            if (Object.values(window.marketplace.installed).indexOf(this.pluginName) > -1) {
                this.installed = true;
            }
        },
        checkActivated() {
            if (Object.values(window.marketplace.activated).indexOf(this.pluginName) > -1) {
                this.activated = true;
            }
        }
    }
}
</script>
