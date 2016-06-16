// VueJS
if (window.Vue === undefined) {
    window.Vue = require('vue');
}

Vue.use(require('vue-resource'));

// Components
require('./components/sqlbuddy');
require('./components/sidebar');
require('./components/table');

// Utils
Vue.component('pagination', require('./util/pagination.vue'));
