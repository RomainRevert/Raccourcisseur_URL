<?php

// Si le shortcut à été reçu
if(isset($_GET['q']))
{
    //On redéfini la variable shortcut
    $shortcut = htmlspecialchars($_GET['q']);

    // On vérifie si c'est une url raccourcie
    $bdd = new PDO('mysql:host=localhost;dbname=bitly;charset=utf8','root', '');
    $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE shortcut = ?');
    $req->execute(array($shortcut));

    while($result = $req->fetch())
    {
        if($result['x'] != 1)
        {
            header('location: index.php?error=true&message=Adresse url inconnue');
            exit();
        }
    }

    // Redirection si le shortcut est valide
    $req = $bdd->prepare('SELECT * FROM links WHERE shortcut = ?');
    $req->execute(array($shortcut));

    while($result = $req->fetch())
    {
        header('location:'.$result['url']);
        exit();
    }
}

if(isset($_POST['url']))
{
    $url = $_POST['url'];

    // Verification d'URL valide
    if(!filter_var($url, FILTER_VALIDATE_URL))
    {
        // Si non valide
        header('location: index.php?error=true&message=Adresse url invalide');
        exit();
    }

    // Lien d'URL raccourcis
    $shortcut = crypt($url,rand());

    // Verification si l'URL a déjà été raccourcie
    $bdd = new PDO('mysql:host=localhost;dbname=bitly;charset=utf8','root', '');
    $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE url = ? ');
    $req->execute(array($url));

    while($result = $req->fetch())
    {
        if($result['x'] !=0)
        {
            header('location: index.php?error=true&message=Adresse url déjà raccourcie');
            exit();
        }
    }

    // Envoi de l'URL
    $req = $bdd->prepare('INSERT INTO links(url, shortcut) VALUES (?, ?)');
    $req->execute(array($url, $shortcut));
    header('location: index.php?short='.$shortcut);
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Raccourcisseur d'URL express</title>
    <link rel="stylesheet" type="text/css" href="design/style.css">
    <link rel="icon" type="image/png" href="pictures/favico.png">
</head>
<body>

    <section id ="hello">
        <div class="container">
            <header>
                <img src="pictures/logo.png" alt="logo" id="logo">
            </header>

            <h1>Une URL longue ? Raccourcissez-là !</h1>
            <h2>Largement meilleur et plus court que les autres</h2>

            <form method="post" action="index.php">
                <input type="url" name="url" placeholder="Collez un lien à raccourcir">
                <input type="submit" value="raccourcir">
            </form>

            <?php if(isset($_GET['error']) && isset($_GET['message'])){ ?>

                <div class="center">
                        <div id="result">
                            <b><?php echo htmlspecialchars ($_GET['message']); ?></b>
                        </div>
                    </div>
                    <?php } else if(isset($_GET['short'])) { ?>
					<div class="center">
						<div id="result">
							<b>URL RACCOURCIE : </b>
							http://localhost/?q=<?php echo htmlspecialchars($_GET['short']); ?>
						</div>
					</div>
				<?php } ?>
        </div>
    </section>

    <footer>
        <img src="pictures/logo2.png" alt="logo" id="logo">
        <br>
        2021 © Bittly
    </footer>

</body>
</html>