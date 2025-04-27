// Show/hide credit card details based on payment method selection
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('payment_method');
    if (paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', function() {
            const creditCardDetails = document.getElementById('credit_card_details');
            if (this.value === 'credit_card') {
                creditCardDetails.classList.remove('hidden');
            } else {
                creditCardDetails.classList.add('hidden');
            }
        });
    }
}); 