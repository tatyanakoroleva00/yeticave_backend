<form class="form container" method="post" action="../login.php">
    <span class="form__error" ><?= isset($errors['message']) ? $errors['message'] : '';?></span>
    <h2>Вход</h2>
    <div class="form__item">
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" placeholder="Введите e-mail" />
        <span class="form__error"><?=$errors['email'] ?? ''?></span>
    </div>
    <div class="form__item">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" value="<?= isset($_POST['password']) ? $_POST['password'] : ''; ?>" placeholder="Введите пароль"/>
        <span class="form__error"><?=$errors['password'] ?? ''?></span>
    </div>
    <div><input type="checkbox" name="remember" id="remember" <?php echo isset($_COOKIE['email']) ? 'checked' : ''; ?>/><label>&nbsp;Запомнить меня</label></div><br>
    <button type="submit" name="submit" class="button">Войти</button>
</form>
