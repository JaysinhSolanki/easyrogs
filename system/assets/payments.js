class DiscoveryPayment {
  constructor(discoveryId, caseId, success)  {
    this.setupIntentSecret = null;
    this.discoveryId  = discoveryId;
    this.caseId       = caseId;
    this.success      = success;
    this.ccElementStyle = {
      base: {
        color: '#32325d',
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
          color: '#aab7c4'
        }
      },
      invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
      }
    };
  
    this.start();
  }
  
  hidePaymentForm() {
    $('#payment-form').hide();
    $('input#save_to_profile').prop('checked', true);
  } 

  async showPaymentForm() {
    if(!this.card) {
      const elements = stripe.elements();
      const card = elements.create("card", {style: this.ccElementStyle});
      card.mount("#card-element");
      card.on('change', ({error}) => {
        $('#card-errors').text(error ? error.message : '');
      });
      this.card = card;
    }

    $('#payment-form').show();
	}
  
  hookPaymentSubmit()  {
    const self = this;
    $('button#submit-payment').on('click', async function(ev) {
      const buttonText = $(this).text();
      $(this).text('Please wait...')
      $(this).attr('disabled', true)

      let paymentMethodId = $("input[name=payment_method_id]:checked").val();
      const response = await getDiscoveryPayment(
        self.discoveryId,
        paymentMethodId,
        $('#save_to_side').is(":checked") ? 1 : 0,
        $('#save_to_profile').is(":checked") ? 1 : 0,
      );
      const intent = await response.json();

      let result = null;
      if (paymentMethodId) {
        result = await stripe.confirmCardPayment(intent.client_secret);
      }
      else {
        result = await stripe.confirmCardPayment(intent.client_secret, {
          payment_method: {card: self.card}
        });
      }

      if (result.error) {
        toastr.error(result.error.message);
      }
      else if ( result.paymentIntent.status === 'succeeded' ) {
        $('#payment-modal').modal('hide');
        toastr.success("Payment received, thanks!");
        self.success();
      }

      $(this).text(buttonText)
      $(this).attr('disabled', false)
		});
  }

  hookFutureUsageUX() {
    const self = this;
    
    $('input[name=payment_method_id]').on('change', function() {
      $(this).val() ? self.hidePaymentForm() : self.showPaymentForm();
      if ($(this).data('type') == 'side') {
        $('input#save_to_side').prop('disabled', true).prop('checked', true);
        $('#save-to-side-input').hide();
      }
      else {
        $('input#save_to_side').prop('disabled', false).prop('checked', false);
        $('#save-to-side-input').show();
      }
    });
    $('input[name=payment_method_id]:checked').trigger('change');
    
    $('input#save_to_profile').on('change', function() {
      const checked = $(this).is(":checked");
      $('input#save_to_side').prop('disabled', !checked);
      if (!checked) { $('input#save_to_side').prop('checked', false); }
    });

  }

	start() {
    const self = this;
		getPaymentMethods(this.caseId,
			(methods) => {
        $('#stored-payment-methods').html('');
				for(let idx in methods) {
          let paymentMethod = methods[idx];
					$('#stored-payment-methods').append(`
            <div class="payment-method">
              <div class="form-check" style="cursor: pointer">
                <input type="radio" name="payment_method_id" id="payment_method_id_${paymentMethod.id}" value="${paymentMethod.id}" class="form-check-input" data-type="${paymentMethod.type}" ${paymentMethod.default && 'checked' || ''} />
                <label class="form-check-label" for="payment_method_id_${paymentMethod.id}" style="cursor: pointer">${paymentMethod.name}</label>
              </div>
						</div>
					`);
        }
        
        self.hookFutureUsageUX();
        self.hookPaymentSubmit();

        $('#payment-modal').modal('show');
			},
			(error) => showResponseMessage(error)
    );    
  }

}

window.DiscoveryPayment = DiscoveryPayment;