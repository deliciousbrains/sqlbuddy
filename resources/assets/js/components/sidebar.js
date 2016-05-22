Vue.component('sqlbuddy-sidebar', {

    props: ['databases'],

    data() {
        return {
            tables: []
        }
    },

    methods: {
        updateTables(e) {
            var database = e.target[e.target.selectedIndex].value;
        }
    }

});