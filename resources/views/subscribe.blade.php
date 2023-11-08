<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Subscribe
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-5">
                    <form id="payment-form" method="POST" action="{{ route('subscribe.post') }}">
                        @csrf
                        <div class="my-4">
                            <input type="radio" name="plan" id="standard" value="price_1O9Yp0Il6tIi5qIxupWayVLw" checked>
                            <label for="standard">Standard - $10 / month</label> <br>

                            <input type="radio" name="plan" id="premium" value="price_1O9Yp0Il6tIi5qIxR4Q5ymaS">
                            <label for="premium">Premium - $20 / month</label>
                        </div>
                        <div id="payment-element">
                            <!--Stripe.js injects the Payment Element-->
                        </div>
                        <button id="btnSubmit" class="bg-gray-900 text-white px-4 py-2 rounded">
                            <div class="spinner hidden" id="spinner"></div>
                            <span id="button-text">Pay now</span>
                        </button>
                        <div id="payment-message" class="hidden"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>

        <script>
            const stripe = Stripe("pk_test_51O9YeNIl6tIi5qIxGAm1fQ0iUwxHgbc862QZA3kieQmiURhw7Rh2xZ5jI2hwtUp9ZMe4QFG34YnrQmptmnSHqWZj006OQssz90");

            let elements;

            initialize();

            document
                .querySelector("#payment-form")
                .addEventListener("submit", handleSubmit);

            function initialize() {

                elements = stripe.elements({
                    clientSecret: "{{ $intent->client_secret }}"
                });

                const paymentElementOptions = {
                  layout: "tabs",
                };

                const paymentElement = elements.create("payment", paymentElementOptions);
                paymentElement.mount("#payment-element");
              }

              async function handleSubmit(e) {
                e.preventDefault();

                const {
                    setupIntent,
                    error
                } = await stripe.confirmSetup({
                  elements,
                  confirmParams: {
                    // Make sure to change this to your payment completion page
                    return_url: "http://localhost:4242/checkout.html",
                  },
                  redirect: 'if_required'
                });

                // This point will only be reached if there is an immediate error when
                // confirming the payment. Otherwise, your customer will be redirected to
                // your `return_url`. For some payment methods like iDEAL, your customer will
                // be redirected to an intermediate site first to authorize the payment, then
                // redirected to the `return_url`.

                if(error) {
                    if (error.type === "card_error" || error.type === "validation_error") {
                        showMessage(error.message);
                      } else {
                        showMessage("An unexpected error occurred.");
                      }
                } else {
                    // console.log(setupIntent)

                    var form = document.getElementById('payment-form');
                    var hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'paymentMethod');
                    hiddenInput.setAttribute('value', setupIntent.payment_method);
                    form.appendChild(hiddenInput);

                    // Submit the form
                    form.submit();
                }
              }

              function showMessage(messageText) {
                const messageContainer = document.querySelector("#payment-message");

                messageContainer.classList.remove("hidden");
                messageContainer.textContent = messageText;

                setTimeout(function () {
                  messageContainer.classList.add("hidden");
                  messageContainer.textContent = "";
                }, 4000);
              }
        </script>
    @endpush
</x-app-layout>
