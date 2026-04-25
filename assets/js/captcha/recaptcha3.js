function onloadRecaptcha3Callback(tokenFieldId, siteKey) {
    const tokenField = document.getElementById(tokenFieldId);
    function executeRecaptcha3() {
        grecaptcha.ready(() => {
            grecaptcha.execute(siteKey, {action: 'submit'}).then(function(token) {
                tokenField.value = token;
            });
        });
    }

    executeRecaptcha3();

    const form = tokenField.closest('form');
    if (form) {
        form.addEventListener('marsFormSubmitFinished', function(event) {
            if (event.detail.form.contains(tokenField)) {
                executeRecaptcha3();
            }
        });
    }
}