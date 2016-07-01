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

	watch: {
		'selectedDatabase': function() {
			this.error = null;
		},
		'selectedTable': function() {
			this.error = null;
		}
	},

	events: {
		'error': function(error) {
			this.error = error;
		}
	}
});