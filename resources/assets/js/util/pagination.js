Vue.component('pagination', {
	props: {
		name: {
			type: String,
			required: true
		},
		data: {
			type: Object,
			default() {
				return {
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
		}
	},

	template: '<ul class="pagination" v-if="data.total > data.per_page">' +
	'	<template v-if="data.prev_page_url">' +
	'		<li class="page-item">' +
	'			<a href="#" class="page-link" aria-label="First" @click.prevent="selectPage(1)"><span aria-hidden="true">&laquo;</span><span class="sr-only">First</span></a>' +
	'		</li>' +
	'		<li class="page-item">' +
	'			<a href="#" class="page-link" aria-label="Previous" @click.prevent="selectPage(--this.data.current_page)"><span aria-hidden="true">&lsaquo;</span><span class="sr-only">Previous</span></a>' +
	'		</li>' +
	'	</template>' +

	'	<template v-if="data.next_page_url">' +
	'		<li class="page-item">' +
	'			<a href="#" class="page-link" aria-label="Next" @click.prevent="selectPage(++this.data.current_page)"><span aria-hidden="true">&rsaquo;</span><span class="sr-only">Next</span></a>' +
	'		</li>' +
	'		<li class="page-item">' +
	'			<a href="#" class="page-link" aria-label="Last" @click.prevent="selectPage(this.data.last_page)"><span aria-hidden="true">&raquo;</span><span class="sr-only">Last</span></a>' +
	'		</li>' +
	'	</template>' +
	'</ul>',

	methods: {
		selectPage(page) {
			this.$dispatch('pagination-' + this.name + '-page', page);
		}
	}
});
