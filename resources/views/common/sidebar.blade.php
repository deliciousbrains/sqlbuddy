<sqlbuddy-sidebar :databases="databases" inline-template>
    <select class="form-control" @change="updateTables">
        <option>Choose Database&hellip;</option>
        <option value="@{{ database }}" v-for="database in databases">
            @{{ database }}
        </option>
    </select>

    <div v-if="tables.length">
        <ul>
            <li v-for="table in tables">
                @{{ table }}
            </li>
        </ul>
    </div>
</sqlbuddy-sidebar>