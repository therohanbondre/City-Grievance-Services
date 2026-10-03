(function () {
    var table = document.getElementById('complaint-history-table');
    var search = document.getElementById('complaint-search');
    var statusFilter = document.getElementById('complaint-status-filter');
    var clearButton = document.getElementById('clear-complaint-filters');
    var resultSummary = document.getElementById('complaint-results');
    var noMatchRow = document.getElementById('no-matching-complaints');

    if (!table || !search || !statusFilter || !clearButton || !resultSummary || !noMatchRow) {
        return;
    }

    var rows = table.querySelectorAll('tbody tr[data-status]');
    var total = rows.length;

    function updateComplaints() {
        var query = search.value.trim().toLowerCase();
        var selectedStatus = statusFilter.value;
        var visible = 0;

        for (var i = 0; i < rows.length; i++) {
            var matchesText = rows[i].textContent.toLowerCase().indexOf(query) !== -1;
            var matchesStatus = selectedStatus === 'all' || rows[i].getAttribute('data-status') === selectedStatus;
            var showRow = matchesText && matchesStatus;
            rows[i].hidden = !showRow;
            if (showRow) {
                visible++;
            }
        }

        noMatchRow.hidden = visible !== 0 || total === 0;
        resultSummary.textContent = 'Showing ' + visible + ' of ' + total + ' complaints.';
    }

    search.addEventListener('input', updateComplaints);
    statusFilter.addEventListener('change', updateComplaints);
    clearButton.addEventListener('click', function () {
        search.value = '';
        statusFilter.value = 'all';
        updateComplaints();
        search.focus();
    });

    updateComplaints();
}());
