Vue.component('sqlbuddy-table', {
	props: [
		'selectedDatabase',
		'selectedTable',
	    'page'
	],

	data() {
		return {
			columns: [],
			rows: {}
		}
	},

	ready() {
		this.resetTableData();
	},

	methods: {
		getTableRows(table, page) {
			if (!this.selectedDatabase || !table) {
				return;
			}
			if (typeof page === 'undefined') {
				page = 1;
			}

			this.$http.get('/api/databases/' + encodeURI(this.selectedDatabase) + '/tables/' + encodeURI(table) + '?page=' + page)
				.then(response => {
					if (response.data.error) {
						this.resetTableData();
						this.$dispatch('error', response.data.error);
					} else {
						this.columns = response.data.columns;
						this.rows = response.data.rows;
					}
				});
		},

		resetTableData() {
			this.columns = [];
			this.rows = {
				current_page: 1,
				data: [],
				from: 1,
				last_page: 1,
				next_page_url: null,
				per_page: 10,
				prev_page_url: null,
				to: 1,
				total: 1
			};
		}
	},

	watch: {
		'selectedDatabase': function(database, oldVal) {
			if( oldVal.length && oldVal !== database ) {
				this.selectedTable = '';
				this.page = '';
			}
			this.resetTableData();
		},
		'selectedTable': function(table, oldVal) {
			if( oldVal.length && oldVal !== table ) {
				this.page = '';
			}
			this.getTableRows(table, this.page);
		},
		'rows': function() {
			$(function() {
				$('td:truncated').addClass('truncated').popover({
					'placement': 'bottom',
					'content': function() {
						return this.innerHTML;
					}
				});
			});
		}
	},

	events: {
		'pagination-rows-page': function(page) {
			this.getTableRows(this.selectedTable, page);
		}
	}
});
