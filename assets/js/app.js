import Tagify from '@yaireo/tagify';
import '@yaireo/tagify/dist/tagify.css';

// Tags input elementini seç
const tagInput = document.querySelector('input[name="tags"]');

if (tagInput) {
    // AJAX ile mevcut etiketleri al
    fetch('/tags')
        .then(response => response.json())
        .then(function(whitelist) {
            // Tagify'ı başlat ve öneriler listesi olarak whitelist kullan
            new Tagify(tagInput, {
                whitelist: whitelist,  // Mevcut etiketler öneri olarak gösterilecek
                dropdown: {
                    maxItems: 10,           // Önerilecek en fazla etiket sayısı
                    classname: "tags-look", // Dropdown sınıfı
                    enabled: 0,             // Focus ile öneriler gösterilecek
                    closeOnSelect: false    // Seçim yapıldığında dropdown kapanmayacak
                },
                // Yeni etiket oluşturulmasına izin ver
                enforceWhitelist: false,   // Yalnızca mevcut etiketlerden seçim yapmaya zorlamayın
                editTags: 1                // Etiket düzenlemeye izin ver
            });
        })
        .catch(function(error) {
            console.error('Error fetching tags:', error);
        });

}
// Initialize DataTables for all issue tables
$(document).ready(function() {
    $('.issue-table').DataTable({
        "paging": false, // Disable paging
        "info": false,   // Disable info text
        "searching": false, // Disable searching
        "ordering": true,  // Enable sorting
        "columnDefs": [
            { "orderable": false, "targets": [1, 4] } // Disable sorting on 'Kişi' and 'Label' columns
        ]
    });
});

