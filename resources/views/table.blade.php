@extends('common.layout')

@section('content')
	<sqlbuddy-table :selected-database="selectedDatabase" :selected-table="selectedTable" inline-template>
		<div class="sqlbuddy-table">
			<div v-if="columns.length">
				<div class="sqlbuddy-table-table-wrapper">
					<table class="table">
						<thead>
							<tr>
								<th v-for="column in columns">@{{ column }}</th>
							</tr>
						</thead>
						<tbody v-if="rows.data.length">
							<tr v-for="row in rows.data">
								<td v-for="column in columns">@{{ row[column] }}</td>
							</tr>
						</tbody>
						<tbody v-else>
							<tr>
								<td class="text-xs-center" colspan="@{{ columns.length }}">No data.</td>
							</tr>
						</tbody>
					</table>
				</div>
				<pagination name="rows" :data="rows"></pagination>
			</div>
			<div class="card text-xs-center" v-else>
				<div class="card-block">
					Select a table.
				</div>
			</div>
		</div>
	</sqlbuddy-table>
@endsection