Vue.component('sqlbuddy-sidebar', {
	props: [
		'databases',
		'selectedDatabase',
		'selectedTable'
	],

	data() {
		return {
			tables: []
		}
	},

	methods: {
		getTables(database) {
			this.$http.get('/api/databases/' + encodeURI(database) + '/tables')
				.then(response => {
					if (response.data.error) {
						this.$dispatch('error', response.data.error);
					} else {
						this.tables = response.data;
					}
				});
		},
		selectTable(table) {
			this.selectedTable = table;
		}
	},

	watch: {
		'selectedDatabase': function(database) {
			if (database) {
				this.getTables(database);
			}
		}
	}
});