<div class="container-sm p-5 my-5 border">
    <p class="h1">Login Seite</p>
    <form method="post" id="login_form">
        <div class="mb-3 mt-3">
            <label for="username" class="form-label">Benutzername:</label>
            <input class="form-control" id="username" placeholder="Benutzername" name="username">
            <span class="error"><?php echo $this->getNameErr();?></span>
        </div>
        <button type="submit" class="btn btn-primary">Einloggen</button>
        <a class="btn btn-secondary" href="index.php?page=7">Registrieren</a>
    </form>
</div>