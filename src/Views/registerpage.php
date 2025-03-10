<div class="container-sm p-5 my-5 border">
    <p class="h1">Erstellen Sie einen neuen Benutzer</p>
    <form method="post" id="register_form">
        <div class="mb-3 mt-3">
            <label for="username" class="form-label">Benutzername:</label>
            <input class="form-control" id="username" placeholder="Benutzername eingeben" name="username"
                   value="<?php echo !empty($this->username) ? htmlspecialchars($this->username) : ''; ?>">
            <span class="error"><?php echo $this->getNameErr();?></span>
        </div>
        <div class="mb-3 mt-3">
            <label for="realestate" class="form-label">Immobilienname:</label>
            <input class="form-control" id="realestate" placeholder="Immobilie einen Namen geben" name="realestate"
                   value="<?php echo !empty($this->realestate) ? htmlspecialchars($this->realestate) : ''; ?>">
            <span class="error"><?php echo $this->getRealestateErr();?></span>
        </div>
        <button type="submit" class="btn btn-primary">Registrieren</button>
    </form>
</div>
