<section class="lot-item container">
    <h2><?= $lot_name ?? 'Без имени'; ?></h2>
    <div class="lot-item__content">
        <!--        Left column-->
        <div class="lot-col left">
            <div class="description">
                <div class="lot-item__image">
                    <img src='../<?= $img_url ?? ''; ?>' width="730" height="548" alt="<? $lot_name ?? '' ?>">
                </div>
                <p class="lot-item__name"><b style="font-size: 14px;">Наименование:</b> <span><?= $lot_name?></span>
                </p>
                <p class="lot-item__category"><b style="font-size: 14px;">Категория:</b>
                    <span><?= $category_name; ?></span></p>
                <p class="lot-item__description"><b style="font-size: 14px;">Описание:</b>
                    <span><?= $lot_message ?? '' ?></span></p>
                <p><b style="font-size: 14px;">Контакты:</b>
                    <span><?= $user_name . ', ' . $contacts ?? '' ?></span></p>

            </div>
            <?php if (isset($_SESSION['user']) && (strtotime($lot_date) > strtotime(date('Y-m-d')))) : ?>
                <?php
                # Если не текущий пользователь создал лот и дата истечения срока лота больше текущей
                if ($user_id != $_SESSION['user']['id']) :?>
                    <div>
                        <div class="lot-item__state">
                            <form class="lot-item__form" action='show_lot.php?id=<?= $lot_id; ?>' method="post">
                                <div>
                                    <p class="rates__title">Добавить ставку</p>
                                    <p class="lot-item__form-item form__item form__item--invalid">
                                        <label for="cost">Ваша cумма:</label>
                                        <input id="cost" type="text" name="lot_rate" placeholder="0"
                                               value="<?php echo isset($_POST['lot_rate']) ? $_POST['lot_rate'] : ''; ?>">
                                    </p>
                                    <p style="color:red;"><?= $errors; ?></p>
                                </div>
                                <div>
                                    <button type="submit" class="button">Разместить ставку</button>
                                    <div class="lot-item__min-cost">
                                        Мин. шаг <span><?= $lot_step; ?>р</span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!--        Right column-->
        <div class="lot-col">
            <h3>Информация торгов</h3>
            <?php if (isset($_SESSION['user'])) : ?>
                <h4><b><a href="/my_bets.php">Мои ставки тут</a></b></h4>
            <? endif; ?>

            <h4>Торги</h4>
            <div>
                <span class="lot-item__timer timer"><?= formattedDate($lot_date); ?></span>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost"><?= $cur_price; ?><b class="rub">р</b></span>
                    </div>
                </div>
            </div>

            <p>Общее количество ставок: <?= $rates_number; ?></p>
            <h4>История торгов (<span>10</span>):</h4>
            <table class="history__list">
                <?php

                if (mysqli_num_rows($result5) > 0) {
                    echo "<table class='history__list'>";

                    foreach ($result5 as $index => $row) {
                        $dateLot = $row['rate_date'];
                        $dateLot2 = humanReadableTimeDifference($row['rate_date']);

                        if (strtotime($lot_date) < time() && ($index === 0)) {
                            echo "<tr class='history__item winner'>
                    <td class='history__name'>" . $row['price'] . "</td>
                    <td class='history__price'>" . $row['lot_name'] . "</td>
                    <td class='history__price'>" . $row['users_name'] . "</td>
                    <td class='history__time'>" . "ПОБЕДИТЕЛЬ!" . "</td>
                </tr>";
                        } else {
                            echo "<tr class='history__item'>
                    <td class='history__name'>" . $row['price'] . "</td>
                    <td class='history__price'>" . $row['lot_name'] . "</td>
                    <td class='history__price'>" . $row['users_name'] . "</td>
                    <td class='history__time'>" . $dateLot2 . "</td>
                </tr>";
                        }
                    }
                }
                ?>
            </table>
        </div
    </div>
</section>
