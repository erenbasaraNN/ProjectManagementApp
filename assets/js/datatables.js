import $ from 'jquery';
import 'datatables.net';

export default function initializeDataTables() {
    $(document).ready(function() {
        console.log('Initializing DataTables');

        try {
            $('.issue-table').DataTable({
                paging: true,  // Enable paging (default behavior)
                searching: true, // Enable search (default behavior)
                ordering: true,  // Enable column sorting
                columnDefs: [
                    { orderable: false, targets: [1] } // Disable sorting for the "Kişi" column
                ]
            });
            console.log('DataTables initialized successfully');
        } catch (error) {
            console.error('Error initializing DataTables:', error);
        }
    });
}
