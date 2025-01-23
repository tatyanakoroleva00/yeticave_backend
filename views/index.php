<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и
        горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <?php
        foreach($categories_query as $row=> $elem) {
            $name_eng = $elem['name_eng'];
            ;?>
            <li class="promo__item promo__item--boards">
                <a class="promo__link" href="category.php?category=<?=$name_eng?>"><?=$elem['name'];?> </a>
            </li>
        <?php } ?>
    </ul>
    <br/>
    <div>
        <?php echo $_SESSION['user'] ? "<button onclick=\"window.location.href = '/history.php'\">Просмотренные лоты</button>" : '' ?>
        <button onclick="window.location.href = '?show=new'">Открытые лоты</button>
        <button onclick="window.location.href = '?show=old'">Закрытые лоты</button>
    </div>
    <hr/>
    <div>
        <button class="sort-button <?= $order ?>" onclick="window.location.href='?order=<?= $nextOrder ?>'">
            Окончание аукциона - <?php echo $order === 'desc' ? "с начала": "с конца"?>
            <span class="<?= $order ?>"></span>
        </button>
        <button class="sort-button <?= $publicationOrder ?>" onclick="window.location.href='?publicationOrder=<?= $nextPublicationOrder ?>'">
            Дата публикации - <?php echo $publicationOrder === 'desc' ? "по возрастанию" : "по убыванию" ?>
            <span class="<?= $publicationOrder ?>"></span>
        </button>
    </div>
    <h1>Фильтр лотов по цене</h1>
    <form action="index.php" method="get">
        <label for="min_price">Минимальная цена:</label>
        <input type="number" id="min_price" name="min_price" step="0.01" min="0" required>

        <label for="max_price">Максимальная цена:</label>
        <input type="number" id="max_price" name="max_price" step="0.01" min="0" required>

        <input type="submit" value="Фильтровать"/>
    </form>
    <button onclick="window.location.href='index.php'">Сбросить</button>
</section>
<section class="lots">
    <div class="lots__header">
        <h2><?php echo $_GET['show'] === 'old' ? 'Закрытые лоты': 'Открытые лоты'?></h2>
    </div>
    <?php if(isset($search_array)) :?>
    <ul class="lots__list">
        <?php foreach($lots_list as $row => $elem) :?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src='<?=$elem['img_url']?>' width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=$elem['category_name']?></span>
                    <h3 class="lot__title"><a class="text-link" href='show_lot.php?id=<?=$elem['id']?>'><?= $elem['name']?></a></h3>
                    <p>Описание: <?=htmlspecialchars(mb_substr($elem['lot_message'], 0, 120)); ;?></p>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?=$elem['cur_price']?><b class="rub">р</b></span>
                        </div>
                        <div class="lot__timer timer">
                            <?php echo formattedDate($elem['lot_date']);?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>


    <?php
    if ($totalPages > 1) { ?>
        <p style="text-align: center">
            <?php
        for ($i = 1; $i <= $totalPages; $i++) {
            echo "<a href='?show=$type_of_lots_to_show&page=$i'>$i</a>";
        }
        ?>
    </p>
    <?php } ?>
</section>
