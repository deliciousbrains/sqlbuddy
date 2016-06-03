@extends('common.layout')

@section('content')
	<sqlbuddy-table :selected-database="selectedDatabase" :selected-table="selectedTable" inline-template>
		<div class="sqlbuddy-table">
			<div class="sqlbuddy-table-table-wrapper">
				<table class="table">
					<thead>
						<tr>
							<th v-for="column in columns">@{{ column }}</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="row in rows.data">
							<td v-for="column in columns">@{{ row[column] }}</td>
						</tr>
					</tbody>
				</table>
			</div>

			<pagination name="rows" :data="rows"></pagination>
		</div>
	</sqlbuddy-table>
@endsection