<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فشل الدفع</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @if(isset($redirect_url))
        <meta http-equiv="refresh" content="2;url={{ $redirect_url }}">
    @endif
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Tahoma', Arial, sans-serif;
            background-color: #f4f4f9;
        }
        .container {
            text-align: center;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
        }
        h1 {
            color: #dc3545;
            font-size: 2em;
            margin-bottom: 20px;
        }
        p {
            color: #555;
            font-size: 1.2em;
            margin-bottom: 15px;
        }
        .countdown {
            font-weight: bold;
            color: #dc3545;
        }
        .redirect-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .redirect-link:hover {
            background-color: #a71d2a;
        }
        .try-again {
            display: block;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>فشل عملية الدفع!</h1>
    <p>للأسف، لم تكتمل عملية الدفع. يرجى المحاولة مرة أخرى أو التواصل مع الدعم.</p>

    @if(isset($redirect_url))
        <p>سيتم تحويلك تلقائياً خلال <span class="countdown" id="countdown">2</span> ثانية...</p>
        <a href="{{ $redirect_url }}" class="redirect-link">العودة الآن</a>
        <a href="{{ url()->previous() }}" class="try-again">المحاولة مرة أخرى</a>

        <script>
            // عد تنازلي مرئي للمستخدم
            let seconds = 2;
            const countdownElement = document.getElementById('countdown');

            const interval = setInterval(() => {
                seconds--;
                countdownElement.textContent = seconds;

                if (seconds <= 0) {
                    clearInterval(interval);
                }
            }, 1000);
        </script>
    @endif
</div>
</body>
</html>
