$('button#submit-payment').on('click', _ => {
    $('#payment-modal').modal('hide')
    toastr.success("Payment received, thanks!")
    trackEvent('pay', { event_category: 'demo_payment', event_value: 0, })

    const instance = $('#payment-modal').data('instance')
    instance && instance.success();
} );
