<h1><?=$title?></h1>

<?php if (isset($lots_list)) : ?>
            <ul class="lots__list">
                <?php foreach ($lots_list as $index => $item) :?>
                        <li class="lots__item lot">
                            <div class="lot__image">
                                <img src="<?= $item['img_url'] ?>" width="350" height="260"
                                     alt="<?= $item['lot_name'] ?>">
                            </div>
                            <div class="lot__info">
                                <h3 class="lot__title"><a class="text-link" href="show_lot.php?id=<?=$item['lot_id'];?>"> <?=$item['lot_name'] ?></a>
                                </h3>
                                <div class="lot__state">
                                    <div class="lot__rate">
                                        <span class="lot__amount">Текущая цена:</span>
                                        <span><b><?php echo $item['cur_price'];?></b></span><span>&nbsp;Р</span>
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
<p style="text-align: center">
    <?php
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($totalPages > 1)
        echo "<a href='?category=$category&page=$i'>$i</a> ";
    } ?>
</p>
