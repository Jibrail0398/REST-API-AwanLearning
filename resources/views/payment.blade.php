<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <h2>Complete Your Payment</h2>
        <button id="pay-button">Pay Now</button>
    </div>

    <script type="text/javascript">
        // For example trigger on button clicked, or any time you need
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function () {
            // Trigger snap popup. @TODO: Replace TRANSACTION_TOKEN_HERE with your transaction token
            window.snap.pay('{{ $snap_token }}', {
                onSuccess: function(result){
                    /* You may add your own implementation here */
                    alert("Payment successful!");
                    console.log(result);
                },
                onPending: function(result){
                    /* You may add your own implementation here */
                    alert("Waiting for your payment!");
                    console.log(result);
                },
                onError: function(result){
                    /* You may add your own implementation here */
                    alert("Payment failed!");
                    console.log(result);
                },
                onClose: function(){
                    /* You may add your own implementation here */
                    alert('You closed the popup without finishing the payment');
                }
            })
        });
    </script>
</body>
</html>
