<sqlbuddy-sidebar :databases="databases" :selected-database.sync="selectedDatabase" :selected-table.sync="selectedTable" inline-template>
    <div class="sqlbuddy-sidebar">
        <select class="form-control" v-model="selectedDatabase">
            <option value="">Choose Database&hellip;</option>
            <option value="@{{ database }}" v-for="database in databases">
                @{{ database }}
            </option>
        </select>

        <div v-if="tables.length">
            <ul class="sqlbuddy-sidebar-table-list">
                <li v-for="table in tables" :class="{ 'active': table == selectedTable }">
                    <a href="#" @click.prevent="selectTable(table)">@{{ table }}</a>
                </li>
            </ul>
        </div>
    </div>
</sqlbuddy-sidebar>