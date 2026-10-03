(function () {
    var copyButton = document.getElementById('copy-complaint-number');
    if (!copyButton) {
        return;
    }

    copyButton.addEventListener('click', function () {
        var complaintNumber = copyButton.getAttribute('data-complaint-number');
        var feedback = document.getElementById('copy-feedback');

        if (!navigator.clipboard || !navigator.clipboard.writeText) {
            feedback.textContent = 'Copy is unavailable in this browser. Complaint number: ' + complaintNumber;
            return;
        }

        Promise.resolve().then(function () {
            return navigator.clipboard.writeText(complaintNumber);
        }).then(function () {
            feedback.textContent = 'Complaint number copied.';
        }, function () {
            feedback.textContent = 'Could not copy the complaint number. You can select it above.';
        });
    });
}());
