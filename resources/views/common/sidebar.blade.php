<sqlbuddy-sidebar :databases="databases" inline-template>
    <select class="form-control">
        <option>Choose Database&hellip;</option>
        <option v-for="database in databases">
            @{{ database }}
        </option>
    </select>
</sqlbuddy-sidebar>