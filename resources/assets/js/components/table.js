Vue.component('sqlbuddy-table', {
	props: [
		'selectedDatabase',
		'selectedTable'
	],

	data() {
		return {
			columns: [],
			rows: {
				current_page: 1,
				data: [],
				from: 1,
				last_page: 1,
				next_page_url: null,
				per_page: 10,
				prev_page_url: null,
				to: 1,
				total: 1
			}
		}
	},

	methods: {
		getTableRows(table, page) {
			if (typeof page === 'undefined') {
				page = 1;
			}

			this.$http.get('/api/databases/' + encodeURI(this.selectedDatabase) + '/tables/' + encodeURI(table) + '?page=' + page)
				.then(response => {
					if (response.data.error) {
						this.$dispatch('error', response.data.error);
					} else {
						this.columns = response.data.columns;
						this.rows = response.data.rows;
					}
				});
		}
	},

	watch: {
		'selectedTable': function(table) {
			this.getTableRows(table);
		}
	},

	events: {
		'pagination-rows-page': function(page) {
			this.getTableRows(this.selectedTable, page);
		}
	}
});