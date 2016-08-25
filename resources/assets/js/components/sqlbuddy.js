var createHistory = require('history').createHistory;

Vue.component('sqlbuddy', {
	data() {
		return {
			history: createHistory(),
			databases: [],
			selectedDatabase: '',
			selectedTable: '',
			page: 1,
			isLoading: true,
			error: null
		}
	},

	ready() {
		this.isLoading = false;
		this.getDatabases();
	},

	methods: {
		getDatabases() {
			this.$emit('is-loading', true);
			this.$http.get('/api/databases')
				.then(response => {
					this.$emit('is-loading', false);
					this.databases = response.data;
					this.setLocation();
				});
		},
		setLocation() {
			var location = this.history.getCurrentLocation();
			if (!location.state) {
				// Set the state from the path
				var segments = location.pathname.substring(1).split('/');
				location.state = {
					database: segments[0],
					table: segments[1],
					page: segments[2],
				}
			}

			if (location.state.database) {
				this.selectedDatabase = location.state.database;
			}
			if (location.state.table) {
				this.selectedTable = location.state.table;
			}
			if (location.state.page) {
				this.page = location.state.page;
			}
		},
		updateLocation() {
			var path = '/';
			if (this.selectedDatabase) {
				path += this.selectedDatabase;
			}
			if (this.selectedTable) {
				path += '/' + this.selectedTable;
			}
			if (this.page > 1) {
				path += '/' + this.page;
			}

			this.history.push({
				pathname: path,
				state: {
					'database': this.selectedDatabase,
					'table': this.selectedTable,
					'page': this.page
				}
			});
		}
	},

	watch: {
		'selectedDatabase': function(database, oldVal) {
			if (oldVal.length && oldVal !== database) {
				this.selectedTable = '';
				this.page = '';
			}
			this.error = null;
			this.updateLocation();
		},
		'selectedTable': function(table, oldVal) {
			if (oldVal.length && oldVal !== table) {
				this.page = '';
			}
			this.error = null;
			this.updateLocation();
		}
	},

	events: {
		'is-loading': function(isLoading) {
			this.isLoading = isLoading;
		},
		'error': function(error) {
			this.error = error;
		},
		'pagination-page-updated': function(page) {
			this.page = page;
			this.updateLocation();
		}
	}
});
