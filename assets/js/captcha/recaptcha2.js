function onloadRecaptcha2Callback() {
    const recaptchas = document.querySelectorAll('.g-recaptcha');
    recaptchas.forEach(function(recaptcha) {
        const widgetId = grecaptcha.render(recaptcha, {
            sitekey: recaptcha.dataset.sitekey
        });

        recaptcha.dataset.widgetId = widgetId;

        const form = recaptcha.closest('form');
        if (form) {
            form.addEventListener('marsFormSubmitFinished', function(event) {
                if (event.detail.form.contains(recaptcha)) {
                    grecaptcha.reset(widgetId);
                }
            });
        }
    });
}
