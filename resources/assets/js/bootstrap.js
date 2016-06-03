// VueJS
if (window.Vue === undefined) {
    window.Vue = require('vue');
}

Vue.use(require('vue-resource'));

require('./util/pagination');

require('./components/sqlbuddy');
require('./components/sidebar');
