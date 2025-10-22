<?php
// PHP Backend Logic Simulation
$error_message = '';
$success_message = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Simple validation
    if (empty($username) || empty($password)) {
        $error_message = "Please enter both username and password.";
    } else {
        // --- Simulated Authentication ---
        // In a real application, you would query a database here
        // to check credentials and hash the password.

        $simulated_db_username = 'user@example.com';
        $simulated_db_password = 'password123'; // Note: NEVER store plain passwords in a real database!

        if ($username === $simulated_db_username && $password === $simulated_db_password) {
            // Successful login (redirect the user in a real app)
            $success_message = "Login successful! Welcome, {$username}. (In a real app, you would be redirected.)";
        } else {
            // Failed login
            $error_message = "Invalid username or password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Custom font import */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f7f7;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white p-8 md:p-10 shadow-2xl rounded-xl border border-gray-100">

        <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-6">
            Sign In
        </h2>
        <p class="text-center text-sm text-gray-500 mb-8">
            Access your account securely.
        </p>

        <?php if (!empty($success_message)): ?>
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- The form action is set to the current file to process the PHP logic -->
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-6">

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                    Username / Email
                </label>
                <input
                    type="text"
                    name="username"
                    id="username"
                    required
                    autocomplete="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                    placeholder="you@example.com"
                    value="<?php echo htmlspecialchars($username ?? ''); ?>"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    autocomplete="current-password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                    placeholder="••••••••"
                >
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-blue-600 hover:text-blue-500 transition duration-150 ease-in-out">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <div>
                <button
                    type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-lg font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out transform hover:scale-[1.01] active:scale-[0.99]"
                >
                    Log In
                </button>
            </div>
        </form>

        <p class="mt-8 text-center text-sm text-gray-600">
            Don't have an account?
            <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                Sign up here
            </a>
        </p>

    </div>

</body>
</html>
