<form class="form form--add-lot container <?php echo !empty($errors) ? 'form--invalid' : '' ?>" action="../add.php"
      method="post" enctype="multipart/form-data">
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item <?php echo isset($errors['category']) ? 'form__item--invalid' : ''; ?>">
            <label for="lot-name">Наименование <sup>*</sup></label>
            <input id="lot-name" type="text" name="lot_name" value="<?= $_POST['lot_name'] ?? ''; ?>"
                   placeholder="Введите наименование лота">
            <span
                class="form__error"><?php echo isset($errors['lot_name']) ? 'Введите наименование лота' : '' ?></span>

        </div>
        <div class="form__item <?php echo isset($errors['category']) ? 'form__item--invalid' : ''; ?>">
            <label for="category">Категория <sup>*</sup></label>
            <select id="category" name="category">
                <option value="">Выберите категорию</option>
                <option
                    value="Доски и лыжи" <?php echo $_POST['category'] === 'Доски и лыжи' ? 'selected' : ''; ?>>
                    Доски и лыжи
                </option>
                <option value="Крепления" <?php echo $_POST['category'] === 'Крепления' ? 'selected' : ''; ?>>
                    Крепления
                </option>
                <option value="Ботинки" <?php echo $_POST['category'] === 'Ботинки' ? 'selected' : ''; ?>>Ботинки
                </option>
                <option value="Одежда" <?php echo $_POST['category'] === 'Одежда' ? 'selected' : ''; ?>>Одежда
                </option>
                <option value="Инструменты" <?php echo $_POST['category'] === 'Инструменты' ? 'selected' : ''; ?>>Инструменты
                </option>
                <option value="Разное" <?php echo $_POST['category'] === 'Разное' ? 'selected' : ''; ?>>
                    Разное
                </option>
            </select>
            <span class="form__error"><?php echo isset($errors['category']) ? 'Выберите категорию' : '' ?></span>
        </div>
    </div>
    <div class="form__item form__item--wide <?php echo isset($errors['lot_message']) ? 'form__item--invalid' : ''; ?>">
        <label for="message">Описание <sup>*</sup></label>
        <textarea id="message" name="lot_message" placeholder="Напишите описание лота"> <?php echo htmlspecialchars(isset($_POST['lot_message']) ? $_POST['lot_message'] : ''); ?></textarea>
        <span class="form__error"><?php echo isset($errors['lot_message']) ? 'Напишите описание лота' : '' ?></span>
    </div>
    <div>
        <label>Изображение <sup>*</sup></label>
        <div class="form__input-file">
            <?php if(isset($_SESSION['uploaded_file'])) : ?>
                <img src="<?php echo $_SESSION['uploaded_file'];?>" alt="Загруженное изображение" width="100px ">
                <button class="remove-button" onClick="removeFile() ">УДАЛИТЬ</button>
            <?php else: ?>
                <input type="file" id="lot_img" name="image" required>
            <?php endif;?>
            <span class="form__error"><?=$errors['image'] ?? ''?></span>
        </div>
    </div>
    <div class="form__container-three">
        <div
            class="form__item form__item--small <?php echo isset($errors['cur_price']) ? 'form__item--invalid' : ''; ?>">
            <label for="lot_rate">Начальная цена <sup>*</sup></label>
            <input id="lot_rate" type="number" min="1" step="1" value="<?= $_POST['cur_price'] ?? ''; ?>" name="cur_price"
                   placeholder="0">
            <span
                class="form__error"><?php echo isset($errors['cur_price']) ? 'Введите начальную цену' : '' ?></span>
        </div>
        <div
            class="form__item form__item--small <?php echo isset($errors['lot_step']) ? 'form__item--invalid' : ''; ?>">
            <label for="lot-step">Шаг ставки <sup>*</sup></label>
            <input id="lot-step" type="number" min="1" step="1" value="<?= $_POST['lot_step'] ?? ''; ?>" name="lot_step"
                   placeholder="0">
            <span class="form__error"><?php echo isset($errors['lot_step']) ? 'Введите шаг ставки' : '' ?></span>
        </div>
        <div class="form__item <?php echo isset($errors['lot_date']) ? 'form__item--invalid' : ''; ?>">
            <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
            <input class="form__input-date" id="lot-date" value="<?= $_POST['lot_date'] ?? ''; ?>" type="date"
                   name="lot_date" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
            <span
                class="form__error"><?php echo isset($errors['lot_date']) ? $errors['lot_date'] : '' ?></span>
        </div>
    </div>
    <button type="submit" class="button">Добавить лот</button>
</form>
