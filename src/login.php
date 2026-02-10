<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accedi</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <h1 class="accedi">Accedi</h1>
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <label for="totp">Codice TOTP:</label>
        <input type="text" id="totp" name="totp" required><br><br>  
        <h3>Genera codice TOTP:</h3>
         <p>Se vuoi generare il tuo codice TOTP, visita <a href="http://localhost:8082" target="_blank">2FAuth App</a>.</p>
        <input type="submit" value="Login">
    </form>

    
    <h2>Se Non disponi di un account, <a href="index.php">Registrati</a></h2>
    
</body>
</html>