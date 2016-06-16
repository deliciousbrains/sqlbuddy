<template>
	<div class="pagination-container clearfix" v-if="data.total > data.per_page">
		<ul class="pagination pull-xs-left">
			<li class="page-item" :class="{ 'disabled': data.current_page <= 1 }">
				<a href="#" class="page-link" aria-label="First" @click.prevent="data.current_page = 1"><span aria-hidden="true">&laquo;</span><span class="sr-only">First</span></a>
			</li>
			<li class="page-item" :class="{ 'disabled': data.current_page <= 1 }">
				<a href="#" class="page-link" aria-label="Previous" @click.prevent="previousPage"><span aria-hidden="true">&lsaquo;</span><span class="sr-only">Previous</span></a>
			</li>
			<li class="page-item" :class="{ 'disabled': data.current_page >= data.last_page }">
				<a href="#" class="page-link" aria-label="Next" @click.prevent="nextPage"><span aria-hidden="true">&rsaquo;</span><span class="sr-only">Next</span></a>
			</li>
			<li class="page-item" :class="{ 'disabled': data.current_page >= data.last_page }">
				<a href="#" class="page-link" aria-label="Last" @click.prevent="data.current_page = data.last_page"><span aria-hidden="true">&raquo;</span><span class="sr-only">Last</span></a>
			</li>
		</ul>

		<div class="page-info pull-xs-left">
			Page
			<select class="form-control" v-model="data.current_page">
				<option v-for="n in data.last_page">{{ n + 1 }}</option>
			</select>
			of {{ data.last_page }}
		</div>

		<div class="row-info pull-xs-left">
			Rows {{ data.from }} - {{ data.to }} of {{ data.total }}
		</div>
	</div>
</template>

<script>
export default {
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

	methods: {
		'nextPage': function() {
			if (this.data.current_page >= this.data.last_page) {
				return;
			}

			this.data.current_page++;
		},

		'previousPage': function() {
			if (this.data.current_page <= 1) {
				return;
			}

			this.data.current_page--;
		}
	},

	watch: {
		'data.current_page': function(page) {
			this.$dispatch('pagination-' + this.name + '-page', page);
		}
	}
}
</script>
