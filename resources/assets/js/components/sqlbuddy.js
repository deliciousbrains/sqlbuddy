Vue.component('sqlbuddy', {
	data() {
		return {
			databases: [],
			selectedDatabase: '',
			selectedTable: '',
			error: null
		}
	},

	ready() {
		this.getDatabases();
	},

	methods: {
		getDatabases() {
			this.$http.get('/api/databases')
				.then(response => {
					this.databases = response.data;
				});
		}
	},

	events: {
		'error': function(error) {
			this.error = error;
		}
	}
});