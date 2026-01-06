<?php
$nume = $email = $mesaj = "";
$numeErr = $emailErr = $mesajErr = "";
$succes = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (empty($_POST["nume"])) {
        $numeErr = "Te rugăm să introduci numele.";
    } else {
        $nume = $_POST["nume"]; 
        
        if (strlen($nume) < 3) {
            $numeErr = "Numele trebuie să aibă minim 3 caractere.";
        }
    }

    if (empty($_POST["Email"])) {
        $emailErr = "Te rugăm să introduci adresa de email.";
    } else {
        $email = $_POST["Email"];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Formatul email-ului nu este valid.";
        }
    }

    if (empty($_POST["Mesaj"])) {
        $mesajErr = "Te rugăm să scrii un mesaj.";
    } else {
        $mesaj = $_POST["Mesaj"];
        
        if (strlen($mesaj) < 10) {
            $mesajErr = "Mesajul trebuie să aibă minim 10 caractere.";
        }
    }

    if (empty($numeErr) && empty($emailErr) && empty($mesajErr)) {
        $succes = true;
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formular Contact PHP</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <main>
        <h1>Formular de contact</h1>

        <?php if ($succes == true): ?>
            <div class="success-box">
                <h2>Mulțumim, <?php echo htmlspecialchars($nume); ?>!</h2>
                <p>Am primit mesajul tău:</p>
                <blockquote>"<?php echo htmlspecialchars($mesaj); ?>"</blockquote>
                <p>Te vom contacta în curând pe adresa <strong><?php echo htmlspecialchars($email); ?></strong>.</p>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>">Trimite un nou mesaj</a>
            </div>

        <?php else: ?>
            <p>Completează formularul de mai jos.</p>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                
                <div class="form-group">
                    <label for="nume">Nume:</label><br>
                    <input type="text" id="nume" name="nume" value="<?php echo htmlspecialchars($nume); ?>">
                    <span class="error"><?php echo $numeErr; ?></span>
                </div>

                <div class="form-group">
                    <label for="Email">Adresa de email:</label><br>
                    <input type="text" id="Email" name="Email" value="<?php echo htmlspecialchars($email); ?>">
                    <span class="error"><?php echo $emailErr; ?></span>
                </div>

                <div class="form-group">
                    <label for="Mesaj">Mesaj:</label><br>
                    <textarea id="Mesaj" name="Mesaj" rows="5"><?php echo htmlspecialchars($mesaj); ?></textarea>
                    <span class="error"><?php echo $mesajErr; ?></span>
                </div>

                <input type="submit" value="Trimite">
            </form>
        <?php endif; ?>

    </main>
  </body>
</html>