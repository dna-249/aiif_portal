<?php
// PHP Backend Logic for User Registration (Sign Up)
$error_message = '';
$success_message = '';
$username = ''; // Initialize to keep the username value in the input field on error

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // 1. Simple validation: Check if all fields are filled
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required for registration.";
    } 
    // 2. Check if passwords match
    elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } 
    // 3. Minimum password length check
    elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    }
    else {
        // --- Simulated Registration ---
        // In a real application, you would perform these critical steps:
        // 1. Hash the password (e.g., using password_hash($password, PASSWORD_DEFAULT))
        // 2. Check if the username/email already exists in your database.
        // 3. Securely insert the new user record into the database.

        // Simulating success:
        $success_message = "Registration successful! You can now log in. (In a real app, the user would be created and redirected.)";
        
        // Clear the username input on successful registration
        $username = '';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Sign Up Form</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
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
            Create Account
        </h2>
        <p class="text-center text-sm text-gray-500 mb-8">
            Start your free account today.
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
                    type="email"
                    name="username"
                    id="username"
                    required
                    autocomplete="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                    placeholder="you@example.com"
                    value="<?php echo htmlspecialchars($username); ?>"
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
                    autocomplete="new-password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                    placeholder="••••••••"
                >
            </div>
            
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                    Confirm Password
                </label>
                <input
                    type="password"
                    name="confirm_password"
                    id="confirm_password"
                    required
                    autocomplete="new-password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                    placeholder="••••••••"
                >
            </div>

            <div>
                <button
                    type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-lg font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out transform hover:scale-[1.01] active:scale-[0.99]"
                >
                    Sign Up
                </button>
            </div>
        </form>

        <p class="mt-8 text-center text-sm text-gray-600">
            Already have an account?
            <a href="login.php" class="font-medium text-blue-600 hover:text-blue-500">
                Log In here
            </a>
        </p>

    </div>

</body>
</html>
