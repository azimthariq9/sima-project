import './bootstrap';

import Alpine from 'alpinejs';
import flasher from '@flasher/flasher';
import { DataTable } from 'simple-datatables';

window.flasher = flasher;
window.Alpine = Alpine;
window.DataTable = DataTable;

Alpine.start();

/**
 * Initialize DataTables on all tables with [data-datatable] attribute.
 * Usage in Blade: <table id="myTable" data-datatable>
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-datatable]').forEach(table => {
        new DataTable(table, {
            searchable: true,
            perPage: 10,
            perPageSelect: [5, 10, 15, 20, 25],
            labels: {
                search: "",
                placeholder: "",
                perPage: "rows per page",
                noRows: "No data found",
                info: "Showing {start} - {end} of {rows} rows",
                loading: "Loading...",
                linearPagination: { previous: "Previous", next: "Next" }
            },
            layout: {
                top: "{search}",
                bottom: "{info}{select}{pagination}"
            }
        });
    });
});
