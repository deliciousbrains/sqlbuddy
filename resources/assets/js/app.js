require('./bootstrap');

var app = new Vue({

    el: '#sqlbuddy',

    data: {
        databases: SQLBuddy.state.databases
    }

});