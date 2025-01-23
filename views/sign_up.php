<form class="form container form--invalid" action="../sign_up.php" method="post" autocomplete="off" enctype="multipart/form-data">
    <span class="form__error form__error--bottom"><?=  !empty($errors) ? 'Пожалуйста, исправьте ошибки в форме.' : ''?></span>
      <h2>Регистрация нового аккаунта</h2>
      <div class="form__item">
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= isset($_POST['email']) ? $_POST['email']: ''?>">
        <span class="form__error"> <?=$errors['email'] ?? ''?></span>
      </div>
      <div class="form__item">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?= isset($_POST['password']) ? $_POST['password']: ''?>">
        <span class="form__error"><?=$errors['password'] ?? ''?></span>
      </div>
      <div class="form__item">
        <label for="name">Имя <sup>*</sup></label>
        <input id="name" type="text" name="name" placeholder="Введите имя" value="<?= isset($_POST['name']) ? $_POST['name'] : '' ?>">
        <span class="form__error"><?=$errors['name'] ?? ''?></span>
      </div>
    <div>
        <label>Аватар</label>
        <div class="form__input-file">
            <input type="file" id="avatar" name="avatar">
            <span class="form__error"><?=$errors['image'] ?? ''?></span>
        </div>
    </div>
      <div class="form__item">
        <label for="message">Контактные данные <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?= isset($_POST['message']) ? $_POST['message'] : '' ?></textarea>
        <span class="form__error"><?=$errors['message'] ?? ''?></span>
      </div>
      <button type="submit" class="button">Зарегистрироваться</button>
      <a class="text-link" href="<?=$login_page;?>">Уже есть аккаунт</a>
    </form>
