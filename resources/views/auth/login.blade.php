<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lara's Flowershop - Login</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Instrument Sans', Arial, sans-serif;
        }

        body {
            background: #F9F6F0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* ===============================
           Decorative flower motifs
           =============================== */
        .flower {
            position: absolute;
            z-index: 0;
            opacity: 0.35;
            pointer-events: none;
        }
        .flower-tl { top: -20px; left: -20px; width: 180px; height: 180px; transform: rotate(-15deg); }
        .flower-tr { top: -30px; right: -30px; width: 220px; height: 220px; transform: rotate(25deg); opacity: 0.28; }
        .flower-bl { bottom: -40px; left: -40px; width: 200px; height: 200px; transform: rotate(40deg); opacity: 0.3; }
        .flower-br { bottom: -25px; right: -25px; width: 160px; height: 160px; transform: rotate(-30deg); }

        .flower-sm {
            position: absolute;
            z-index: 0;
            opacity: 0.2;
            pointer-events: none;
        }
        .flower-sm-1 { top: 15%; left: 8%; width: 60px; height: 60px; transform: rotate(20deg); }
        .flower-sm-2 { top: 70%; right: 10%; width: 70px; height: 70px; transform: rotate(-25deg); }
        .flower-sm-3 { top: 45%; left: 5%; width: 50px; height: 50px; transform: rotate(60deg); opacity: 0.15; }

        /* ===============================
           Login Card
           =============================== */
        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
        }

        .login-container {
            background: #FFFFFF;
            padding: 45px 40px 40px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(46, 90, 59, 0.12);
            border-top: 5px solid #D4AF37;
        }

        /* Logo */
        .logo-wrap {
            text-align: center;
            margin-bottom: 15px;
        }
        .logo-wrap img {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }

        /* Brand */
        h1 {
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: #2E5A3B;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .brand-sub {
            text-align: center;
            font-size: 10px;
            font-weight: 600;
            color: #D4AF37;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #F0E6DD;
        }

        /* Error alert */
        .error {
            background: #FDECEA;
            color: #C0392B;
            padding: 12px 15px;
            border-radius: 8px;
            border-left: 4px solid #DC3545;
            margin-bottom: 20px;
            font-size: 13px;
            font-weight: 500;
        }

        /* Form */
        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 12px;
            color: #2E5A3B;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 1.5px solid #F0E6DD;
            border-radius: 8px;
            font-size: 14px;
            background: #FEFCF9;
            color: #212121;
            transition: all 0.2s ease;
        }
        input:focus {
            outline: none;
            border-color: #E85D75;
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(232, 93, 117, 0.12);
        }
        input::placeholder {
            color: #B0A99F;
        }

        /* Button */
        button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background: #E85D75;
            color: #FFFFFF;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(232, 93, 117, 0.3);
            transition: all 0.2s ease;
            margin-top: 8px;
        }
        button:hover {
            background: #D14A62;
            box-shadow: 0 6px 16px rgba(232, 93, 117, 0.4);
            transform: translateY(-1px);
        }
        button:active {
            transform: translateY(0);
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 11px;
            color: #B0A99F;
            letter-spacing: 0.5px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container { padding: 35px 25px 30px; }
            h1 { font-size: 21px; }
            .logo-wrap img { width: 80px; height: 80px; }
            .flower-sm { display: none; }
        }
    </style>
</head>

<body>

    <!-- Decorative Flowers -->
    <svg class="flower flower-tl" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="50" cy="25" rx="6" ry="12" fill="#E85D75"/>
        <ellipse cx="50" cy="25" rx="12" ry="6" fill="#F8BBD0"/>
        <ellipse cx="35" cy="35" rx="6" ry="12" fill="#E85D75" transform="rotate(-40 35 35)"/>
        <ellipse cx="65" cy="35" rx="6" ry="12" fill="#E85D75" transform="rotate(40 65 35)"/>
        <ellipse cx="40" cy="50" rx="6" ry="12" fill="#F8BBD0" transform="rotate(-70 40 50)"/>
        <ellipse cx="60" cy="50" rx="6" ry="12" fill="#F8BBD0" transform="rotate(70 60 50)"/>
        <circle cx="50" cy="38" r="5" fill="#D4AF37"/>
        <path d="M50 50 Q55 70, 60 92" stroke="#2E5A3B" stroke-width="1.5" fill="none"/>
        <ellipse cx="58" cy="75" rx="6" ry="3" fill="#80B918" transform="rotate(30 58 75)"/>
    </svg>

    <svg class="flower flower-tr" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="50" cy="25" rx="7" ry="14" fill="#F8BBD0"/>
        <ellipse cx="50" cy="25" rx="14" ry="7" fill="#E85D75"/>
        <ellipse cx="32" cy="38" rx="7" ry="14" fill="#F8BBD0" transform="rotate(-40 32 38)"/>
        <ellipse cx="68" cy="38" rx="7" ry="14" fill="#E85D75" transform="rotate(40 68 38)"/>
        <ellipse cx="38" cy="55" rx="7" ry="14" fill="#E85D75" transform="rotate(-70 38 55)"/>
        <ellipse cx="62" cy="55" rx="7" ry="14" fill="#F8BBD0" transform="rotate(70 62 55)"/>
        <circle cx="50" cy="40" r="5" fill="#D4AF37"/>
    </svg>

    <svg class="flower flower-bl" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="50" cy="25" rx="6" ry="13" fill="#E85D75"/>
        <ellipse cx="50" cy="25" rx="13" ry="6" fill="#F8BBD0"/>
        <ellipse cx="34" cy="36" rx="6" ry="13" fill="#E85D75" transform="rotate(-40 34 36)"/>
        <ellipse cx="66" cy="36" rx="6" ry="13" fill="#F8BBD0" transform="rotate(40 66 36)"/>
        <ellipse cx="40" cy="52" rx="6" ry="13" fill="#F8BBD0" transform="rotate(-70 40 52)"/>
        <ellipse cx="60" cy="52" rx="6" ry="13" fill="#E85D75" transform="rotate(70 60 52)"/>
        <circle cx="50" cy="38" r="4.5" fill="#D4AF37"/>
        <path d="M50 50 Q45 70, 40 92" stroke="#2E5A3B" stroke-width="1.5" fill="none"/>
        <ellipse cx="43" cy="72" rx="6" ry="3" fill="#80B918" transform="rotate(-30 43 72)"/>
    </svg>

    <svg class="flower flower-br" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="50" cy="25" rx="6" ry="12" fill="#F8BBD0"/>
        <ellipse cx="50" cy="25" rx="12" ry="6" fill="#E85D75"/>
        <ellipse cx="36" cy="36" rx="6" ry="12" fill="#E85D75" transform="rotate(-40 36 36)"/>
        <ellipse cx="64" cy="36" rx="6" ry="12" fill="#F8BBD0" transform="rotate(40 64 36)"/>
        <ellipse cx="40" cy="50" rx="6" ry="12" fill="#F8BBD0" transform="rotate(-70 40 50)"/>
        <ellipse cx="60" cy="50" rx="6" ry="12" fill="#E85D75" transform="rotate(70 60 50)"/>
        <circle cx="50" cy="38" r="4" fill="#D4AF37"/>
    </svg>

    <svg class="flower-sm flower-sm-1" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="50" cy="28" rx="5" ry="10" fill="#E85D75"/>
        <ellipse cx="50" cy="28" rx="10" ry="5" fill="#F8BBD0"/>
        <ellipse cx="38" cy="38" rx="5" ry="10" fill="#F8BBD0" transform="rotate(-40 38 38)"/>
        <ellipse cx="62" cy="38" rx="5" ry="10" fill="#E85D75" transform="rotate(40 62 38)"/>
        <circle cx="50" cy="38" r="3.5" fill="#D4AF37"/>
    </svg>

    <svg class="flower-sm flower-sm-2" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="50" cy="28" rx="5" ry="10" fill="#F8BBD0"/>
        <ellipse cx="50" cy="28" rx="10" ry="5" fill="#E85D75"/>
        <ellipse cx="38" cy="38" rx="5" ry="10" fill="#E85D75" transform="rotate(-40 38 38)"/>
        <ellipse cx="62" cy="38" rx="5" ry="10" fill="#F8BBD0" transform="rotate(40 62 38)"/>
        <circle cx="50" cy="38" r="3.5" fill="#D4AF37"/>
    </svg>

    <svg class="flower-sm flower-sm-3" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="50" cy="28" rx="5" ry="10" fill="#E85D75"/>
        <ellipse cx="50" cy="28" rx="10" ry="5" fill="#F8BBD0"/>
        <ellipse cx="38" cy="38" rx="5" ry="10" fill="#F8BBD0" transform="rotate(-40 38 38)"/>
        <ellipse cx="62" cy="38" rx="5" ry="10" fill="#E85D75" transform="rotate(40 62 38)"/>
        <circle cx="50" cy="38" r="3.5" fill="#D4AF37"/>
    </svg>

    <div class="login-wrapper">
        <div class="login-container">

            <div class="logo-wrap">
                <img src="{{ asset('images/logo.png') }}" alt="Lara's Flowershop">
            </div>

            <h1>Lara's Flowershop</h1>
            <div class="brand-sub">Est. 2021</div>

            @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Enter your username"
                        required
                        autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required>
                </div>

                <button type="submit">
                    Log In
                </button>
            </form>

            <div class="login-footer">
                Sales &amp; Inventory System
            </div>

        </div>
    </div>

</body>

</html>