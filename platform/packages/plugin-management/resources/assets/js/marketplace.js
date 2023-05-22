require('@base.marketplace/marketplace')

import Plugins from './components/Plugins.vue';
import CardPlugin from './components/CardPlugin.vue';

vueApp.booting(vue => {
    vue.component('marketplace-plugins', Plugins);
    vue.component('marketplace-card-plugin', CardPlugin);
});


