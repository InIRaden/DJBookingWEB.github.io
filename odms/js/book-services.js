document.addEventListener('DOMContentLoaded', function() {
    // Initialize Fancybox
    if (typeof Fancybox !== 'undefined') {
        Fancybox.bind("[data-fancybox]", {
            animationEffect: "fade",
            transitionEffect: "fade",
            buttons: ["zoom", "slideShow", "fullScreen", "close"]
        });
    }

    // Handle payment method selection
    const paymentRadios = document.querySelectorAll('.payment-radio');
    const installmentOptions = document.getElementById('installment-options');
    const bankDropdown = document.getElementById('bank-dropdown');

    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Update border styles for selected/unselected methods
            document.querySelectorAll('.payment-radio').forEach(r => {
                const parent = r.parentElement;
                if (r.checked) {
                    parent.classList.add('border-blue-500');
                    parent.classList.remove('border-gray-700');
                } else {
                    parent.classList.remove('border-blue-500');
                    parent.classList.add('border-gray-700');
                }
            });

            // Show/hide bank dropdown based on payment method
            if (this.value === 'transfer' || this.value === 'installment') {
                bankDropdown.classList.add('show');
                bankDropdown.style.opacity = '1';
                bankDropdown.style.maxHeight = '200px';
            } else {
                bankDropdown.classList.remove('show');
                bankDropdown.style.opacity = '0';
                bankDropdown.style.maxHeight = '0';
            }

            // Show/hide installment options for installment payment
            if (this.value === 'installment') {
                installmentOptions.classList.add('show');
                installmentOptions.style.opacity = '1';
                installmentOptions.style.maxHeight = '200px';
            } else {
                installmentOptions.classList.remove('show');
                installmentOptions.style.opacity = '0';
                installmentOptions.style.maxHeight = '0';
            }
        });
    });

    // Trigger change event for pre-selected payment method
    const checkedRadio = document.querySelector('input[name="payment_method"]:checked');
    if (checkedRadio) {
        checkedRadio.dispatchEvent(new Event('change'));
    }

    // Handle form submission
    const form = document.getElementById('booking-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            // Populate confirmation modal with form data
            document.getElementById('confirm-name').textContent = data.name || 'N/A';
            document.getElementById('confirm-email').textContent = data.email || 'N/A';
            document.getElementById('confirm-mobnum').textContent = data.mobnum || 'N/A';
            document.getElementById('confirm-edate').textContent = data.edate || 'N/A';
            document.getElementById('confirm-est').textContent = data.est || 'N/A';
            document.getElementById('confirm-eetime').textContent = data.eetime || 'N/A';
            document.getElementById('confirm-vaddress').textContent = data.vaddress || 'N/A';
            document.getElementById('confirm-eventtype').textContent = data.eventtype || 'N/A';
            document.getElementById('confirm-addinfo').textContent = data.addinfo || 'N/A';
            document.getElementById('confirm-payment-method').textContent = data.payment_method || 'N/A';
            document.getElementById('confirm-selected-bank').textContent = data.selected_bank || 'N/A';
            
            // Show installment count if applicable
            if (data.payment_method === 'installment' && data.installment_count) {
                document.getElementById('confirm-installment-count').textContent = data.installment_count + ' payments';
            } else {
                document.getElementById('confirm-installment-count').textContent = 'N/A';
            }

            // Open confirmation modal (Step 1/2)
            openModal('confirm-modal');
        });
    }
});

// Global variables for booking process
let bookingData = null;
let countdownInterval = null;
let remainingTime = 24 * 60 * 60; // 24 hours in seconds

// Function to open modal with animation
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    // Hide any error messages when opening modal
    const errorElements = modal.querySelectorAll('.error-message');
    errorElements.forEach(el => {
        el.style.display = 'none';
        el.textContent = '';
    });
    
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.classList.add('show');
    }, 10);
}

// Function to close modal with animation
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

function copyToClipboard(elementId) {
    const copyText = document.getElementById(elementId);
    if (!copyText) return;
    
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    const copyBtn = copyText.nextElementSibling;
    if (copyBtn) {
        const originalHTML = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => {
            copyBtn.innerHTML = originalHTML;
        }, 1500);
    }
}

// Function to show payment details (Step 2/2)
function showPaymentDetails() {
    const form = document.getElementById('booking-form');
    const formData = new FormData(form);
    formData.append('confirm_submit', '1');

    // Show loading indicator or disable button to prevent multiple clicks
    const nextButton = document.querySelector('#confirm-modal .btn-primary');
    const originalText = nextButton.textContent;
    nextButton.textContent = 'Processing...';
    nextButton.disabled = true;

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('confirm-modal');
            const bookingData = data.booking_data;
            
            // Populate payment modal with booking data
            document.getElementById('payment-booking-id').textContent = bookingData.bookingid;
            document.getElementById('payment-amount').textContent = formatCurrency(bookingData.amount);
            document.getElementById('payment-method').textContent = bookingData.paymentMethod;
            document.getElementById('payment-bank').textContent = bookingData.selectedBank || 'N/A';
            document.getElementById('payment-va-number').value = bookingData.va_number;

            // Start countdown timer
            startTimer(bookingData.expiryTime);
            
            // Open payment modal (Step 2/2)
            openModal('payment-modal');
        } else {
            // Show error message in the modal instead of alert
            const errorElement = document.getElementById('confirm-error-message');
            if (errorElement) {
                errorElement.textContent = data.message || 'An error occurred. Please try again.';
                errorElement.style.display = 'block';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Show error message in the modal instead of alert
        const errorElement = document.getElementById('confirm-error-message');
        if (errorElement) {
            errorElement.textContent = 'An error occurred while processing your request. Please try again.';
            errorElement.style.display = 'block';
        }
    })
    .finally(() => {
        // Reset button state
        nextButton.textContent = originalText;
        nextButton.disabled = false;
    });
}

// Function to start countdown timer
function startTimer(expiryTime) {
    const timerElement = document.getElementById('payment-timer');
    const completeButton = document.querySelector('#payment-modal .btn-primary');
    const expiryDate = new Date(expiryTime).getTime();

    // Clear any existing timer
    if (window.paymentTimer) {
        clearInterval(window.paymentTimer);
    }

    window.paymentTimer = setInterval(function() {
        const now = new Date().getTime();
        const distance = expiryDate - now;

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        timerElement.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

        if (distance < 0) {
            clearInterval(window.paymentTimer);
            timerElement.textContent = "Waktu pembayaran telah habis";
            timerElement.style.color = "#ef4444"; // Red color for expired timer
            if (completeButton) {
                completeButton.disabled = true;
                completeButton.style.opacity = "0.5";
            }
        }
    }, 1000);
}

// Function to confirm payment and finalize booking
function confirmPayment() {
    const formData = new FormData();
    formData.append('final_submit', '1');

    // Show loading indicator or disable button to prevent multiple clicks
    const completeButton = document.querySelector('#payment-modal .btn-primary');
    const originalText = completeButton.textContent;
    completeButton.textContent = 'Processing...';
    completeButton.disabled = true;

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('payment-modal');
            openModal('success-modal');
        } else {
            // Show error message in the modal instead of alert
            const errorElement = document.getElementById('payment-error-message');
            if (errorElement) {
                errorElement.textContent = data.message || 'An error occurred. Please try again.';
                errorElement.style.display = 'block';
            } else {
                // Fallback to alert if error element doesn't exist
                alert(data.message || 'An error occurred. Please try again.');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Show error message in the modal instead of alert
        const errorElement = document.getElementById('payment-error-message');
        if (errorElement) {
            errorElement.textContent = 'An error occurred while processing your payment. Please try again.';
            errorElement.style.display = 'block';
        } else {
            // Fallback to alert if error element doesn't exist
            alert('An error occurred while processing your payment. Please try again.');
        }
    })
    .finally(() => {
        // Reset button state
        completeButton.textContent = originalText;
        completeButton.disabled = false;
    });
}

// Show the appropriate payment modal based on method
function showPaymentModalByMethod(paymentInfo) {
    if (paymentInfo.method === 'cash') {
        document.getElementById('cash-booking-id').textContent = paymentInfo.bookingid;
        document.getElementById('cash-amount').textContent = formatCurrency(paymentInfo.amount);
        openModal('cash-modal');
    } else if (paymentInfo.method === 'transfer') {
        document.getElementById('transfer-booking-id').textContent = paymentInfo.bookingid;
        document.getElementById('transfer-bank').textContent = paymentInfo.bank || 'Not selected';
        document.getElementById('transfer-amount').textContent = formatCurrency(paymentInfo.amount);
        document.getElementById('transfer-name').textContent = paymentInfo.name;
        document.getElementById('transfer-va-number').value = paymentInfo.va_number;
        openModal('transfer-modal');
    } else if (paymentInfo.method === 'installment') {
        document.getElementById('installment-booking-id').textContent = paymentInfo.bookingid;
        document.getElementById('installment-bank').textContent = paymentInfo.bank || 'Not selected';
        document.getElementById('installment-total').textContent = formatCurrency(paymentInfo.amount);
        document.getElementById('installment-amount').textContent = formatCurrency(paymentInfo.installment_amount);
        document.getElementById('installment-name').textContent = paymentInfo.name;
        document.getElementById('installment-va-number').value = paymentInfo.va_number;
        openModal('installment-modal');
    }
}

// Simulate payment completion
function simulatePayment(method) {
    closeModal(method + '-modal');
    setTimeout(() => {
        openModal('payment-success-modal');
    }, 300);
}

// Handle payment completion
function paymentCompleted() {
    closeModal('payment-success-modal');
    window.location.href = 'services.php';
}

// Function to format currency
function formatCurrency(amount) {
    return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
}