// VueJS
if (window.Vue === undefined) {
	window.Vue = require('vue');
}

Vue.use(require('vue-resource'));

// Utils
require('./util/truncated');
Vue.component('pagination', require('./util/pagination.vue'));

// Components
require('./components/sqlbuddy');
require('./components/sidebar');
require('./components/table');
