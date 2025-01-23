<div class="container">
    <section class="lots">
        <h2>История просмотров</h2>
        <?php if (isset($lots_list)) : ?>
            <ul class="lots__list">
                <?php foreach ($lots_list as $index => $item) :?>
                        <li class="lots__item lot">
                            <div class="lot__image">
                                <img src="../<?= $item['img_url'] ?>" width="350" height="260"
                                     alt="<?= $item['name'] ?>">
                            </div>
                            <div class="lot__info">
                                <span class="lot__category"><?= $item['category_name'] ?></span>
                                <h3 class="lot__title"><a class="text-link" href="show_lot.php?id=<?=$item['id']?>"> <?= $item['name']?></a>
                                </h3>
                                <div class="lot__state">
                                    <div class="lot__rate">
                                        <span class="lot__amount">Стартовая цена</span>
                                        <span class="lot__cost"><?= $item['price'] ?><b class="rub">р</b></span>
                                    </div>
                                    <div class="lot__timer timer">
                                        <?= formattedDate($item['lot_date'])?>
                                    </div>
                                </div>
                            </div>
                        </li>
                <?php endforeach;?>
            </ul>
        <?php endif; ?>
    </section>

