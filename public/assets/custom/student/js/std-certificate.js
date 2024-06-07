(function ($) {
    "use strict";

    var categoryListTable;
        $(document).on("input", "#dataTableSearch", function () {
            categoryListTable.search($(this).val()).draw();
        });

    categoryListTable = $("#certificateDataTable").DataTable({
        pageLength: 10,
        ordering: false,
        serverSide: true,
        processing: true,
        searching: true,
        responsive: true,

        ajax: $('#certificate-list-route').val(),
        language: {
            paginate: {
                previous: "<i class='fa-solid fa-angles-left'></i>",
                next: "<i class='fa-solid fa-angles-right'></i>",
            },
            searchPlaceholder: "Search event",
            search: "<span class='searchIcon'><i class='fa-solid fa-magnifying-glass'></i></span>",
        },
        dom: '<>tr<"tableBottom"<"row align-items-center"<"col-sm-6"<"tableInfo"i>><"col-sm-6"<"tablePagi"p>>>><"clear">',
        columns: [
            {"data": "certificate_num", "name": "certificate_num", responsivePriority:1},
            {"data": "created_at", "name": "created_at"},
            {"data": "status", "name": "status"},
            {"data": "action", "name": "action"},
        ],
    });

    

})(jQuery)