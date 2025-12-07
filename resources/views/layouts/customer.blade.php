<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FLORA FLEUR</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&family=Rosarivo:ital@0;1&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'olive-dark': '#4a4a3a',
                        'green-card': 'rgba(40, 90, 70, 0.85)',
                        'input-bg': 'rgba(255, 255, 255, 0.2)',
                        'dashboard-btn': '#86a873',
                        'page-bg-trans': 'rgba(255, 255, 255, 0.15)',
                        'page-border-trans': 'rgba(255, 255, 255, 0.2)',
                        'account-card-bg': 'rgba(255, 255, 255, 0.25)',
                    },
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                        'rosarivo': ['Rosarivo', 'serif'],
                        'lato': ['Lato', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    @vite(['resources/css/customer.css', 'resources/js/customer.js'])
</head>
<body class="bg-gray-900 text-white antialiased">
    @yield('content')
</body>
</html>