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
	'	<li class="page-item" v-if="data.prev_page_url">' +
	'		<a href="#" class="page-link" aria-label="Previous" @click.prevent="selectPage(--this.data.current_page)"><span aria-hidden="true">&laquo;</span><span class="sr-only">Previous</span></a>' +
	'	</li>' +
	'	<li class="page-item" v-for="n in data.last_page" :class="{ \'active\': n + 1 == data.current_page }"><a href="#" class="page-link" @click.prevent="selectPage(n + 1)">{{ n + 1 }}</a></li>' +
	'	<li class="page-item" v-if="data.next_page_url">' +
	'		<a href="#" class="page-link" aria-label="Next" @click.prevent="selectPage(++this.data.current_page)"><span aria-hidden="true">&raquo;</span><span class="sr-only">Next</span></a>' +
	'	</li>' +
	'</ul>',

	methods: {
		selectPage(page) {
			this.$dispatch('pagination-' + this.name + '-page', page);
		}
	}
});
