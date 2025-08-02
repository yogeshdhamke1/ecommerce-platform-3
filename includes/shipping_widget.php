<?php
// Shipping Calculator Widget
function renderShippingWidget($productWeight = 1, $productId = null) {
    $widgetId = uniqid('shipping_');
    echo "
    <div class='shipping-widget bg-gray-50 p-4 rounded-lg'>
        <h3 class='text-lg font-semibold mb-3 text-gray-800'>
            <i class='fas fa-shipping-fast mr-2'></i>Check Delivery Options
        </h3>
        <form id='{$widgetId}_form' class='space-y-3'>
            <div class='flex gap-2'>
                <input type='text' id='{$widgetId}_pincode' name='pincode' 
                       class='flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500' 
                       placeholder='Enter pincode' maxlength='6' pattern='[1-9][0-9]{5}'>
                <button type='submit' class='bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition'>
                    <i class='fas fa-search'></i>
                </button>
            </div>
            <input type='hidden' name='weight' value='{$productWeight}'>
            <input type='hidden' name='product_id' value='{$productId}'>
        </form>
        
        <div id='{$widgetId}_results' class='mt-4 hidden'>
            <h4 class='font-medium text-gray-700 mb-2'>Available Shipping Options:</h4>
            <div id='{$widgetId}_rates' class='space-y-2'></div>
        </div>
        
        <div id='{$widgetId}_error' class='mt-3 text-red-600 text-sm hidden'></div>
    </div>
    
    <script>
    document.getElementById('{$widgetId}_form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const pincode = document.getElementById('{$widgetId}_pincode').value;
        const resultsDiv = document.getElementById('{$widgetId}_results');
        const ratesDiv = document.getElementById('{$widgetId}_rates');
        const errorDiv = document.getElementById('{$widgetId}_error');
        
        if (!/^[1-9][0-9]{5}$/.test(pincode)) {
            errorDiv.textContent = 'Please enter a valid 6-digit pincode';
            errorDiv.classList.remove('hidden');
            resultsDiv.classList.add('hidden');
            return;
        }
        
        errorDiv.classList.add('hidden');
        ratesDiv.innerHTML = '<div class=\"text-center py-4\"><i class=\"fas fa-spinner fa-spin\"></i> Calculating rates...</div>';
        resultsDiv.classList.remove('hidden');
        
        const formData = new FormData(this);
        
        fetch('../pages/shipping_calculator.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '';
                data.rates.forEach(rate => {
                    html += `
                        <div class='border border-gray-200 rounded-md p-3 hover:bg-white transition'>
                            <div class='flex justify-between items-start'>
                                <div class='flex-1'>
                                    <div class='font-medium text-gray-900'>\${rate.method}</div>
                                    <div class='text-sm text-gray-600'>\${rate.description}</div>
                                    <div class='text-xs text-gray-500 mt-1'>
                                        <i class='fas fa-truck mr-1'></i>\${rate.carrier}
                                    </div>
                                </div>
                                <div class='text-right'>
                                    <div class='font-bold text-blue-600'>\${rate.formatted_rate}</div>
                                    <div class='text-xs text-gray-500'>\${rate.estimate}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                ratesDiv.innerHTML = html;
            } else {
                errorDiv.textContent = data.error || 'Unable to calculate shipping rates';
                errorDiv.classList.remove('hidden');
                resultsDiv.classList.add('hidden');
            }
        })
        .catch(error => {
            errorDiv.textContent = 'Network error. Please try again.';
            errorDiv.classList.remove('hidden');
            resultsDiv.classList.add('hidden');
        });
    });
    </script>";
}
?>