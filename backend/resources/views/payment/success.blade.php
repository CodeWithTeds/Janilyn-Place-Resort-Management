<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Payment Successful - Janilyn's Place</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.bunny.net/css?family=playfair-display:400,600,700|lato:300,400,700" rel="stylesheet" />
    </head>
    <body class="bg-sky-50 flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h1 class="text-3xl font-serif font-bold text-slate-900 mb-2">Payment Successful!</h1>
            <p class="text-slate-600 mb-6">Your booking has been confirmed.</p>
            
            <div class="bg-slate-50 p-4 rounded-lg mb-8 text-left">
                <p class="text-sm text-slate-500 mb-1">Booking Reference</p>
                <p class="text-lg font-bold text-slate-900">#{{ $booking->id }}</p>
            </div>

            <div class="space-y-3">
                <a href="/" class="block w-full bg-sky-600 hover:bg-sky-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                    Return to Home
                </a>
                <!-- Optional: Deep link back to app if needed -->
                <!-- <a href="myapp://booking/success" class="block w-full text-sky-600 font-semibold py-2">Open in App</a> -->
            </div>
        </div>
    </body>
</html>