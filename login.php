<?php
session_start();

// Dummy credentials
$username = 'admin';
$password = '123456';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    if ($inputUsername === $username && $inputPassword === $password) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_name'] = $inputUsername;
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">

    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md border-2 border-gray-300">
        <h2 class="text-center text-2xl font-bold text-gray-700 mb-4">Login</h2>

        <?php if (isset($error)): ?>
            <div class="mb-4 p-2 text-red-600 bg-red-100 border border-red-400 rounded-md">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label for="username" class="block text-gray-600 font-medium">Username</label>
                <div class="flex items-center border border-gray-300 rounded-md bg-gray-50 p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <input type="text" class="w-full bg-transparent outline-none" id="username" name="username" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-600 font-medium">Password</label>
                <div class="flex items-center border border-gray-300 rounded-md bg-gray-50 p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 17v.01"></path>
                        <rect x="3" y="11" width="18" height="10" rx="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <input type="password" class="w-full bg-transparent outline-none" id="password" name="password" required>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition">Login</button>
        </form>
        
    </div>

</body>
</html>

