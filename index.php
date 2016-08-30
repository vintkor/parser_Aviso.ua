<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Avizo-Parser</title>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
    <style>
        img {
            /*display: none;*/
        }
        .price {
            background: yellow;
            display: inline-block;
        }
        .purpur2 {
            display: none;
        }
        .price>span {
            background: #fff;
            padding: 2px 0;
        }
        .all_count_result {
            display: block;
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 15px;
            font-size: 12px;
            background: #ffff72;
            color: #000;
            font-weight: 500;
            opacity: .2;
            transition: .2s;
        }
        .all_count_result:hover {
            opacity: 1;
        }
        .creame {
            background: rgba(255, 128, 35, 0.11);
        }
        .silver {
            background: rgba(38, 255, 76, 0.11);
        }
        a:visited {
            color: #f00;
        }

        a.back {
            color: #000;
            font-weight: 700;
            font-size: 20px;
        }
    </style>
</head>
<body>

<?php

if (isset($_GET['FROM']) && isset($_GET['TO'])) {

    $from = $_GET['FROM'];
    $to   = $_GET['TO'];

    // Скрипт ищет 1-но комнатные квартиры в Киеве на сайте Aviso.ua
    // в диапазоне цен от ($from) до ($to)

    /**
     * Created by PhpStorm.
     * User: Vintkor
     * Date: 18.08.2016
     * Time: 13:11
     */

    $start = microtime(true);

    require_once 'phpQuery.php';

    $count = 1;
    $hentry = array();

    $url = 'http://www.aviso.ua/kiev/list.php?r=121&s=&distr=0&room%5B1%5D=1&spacefrom=&spaceto=&pricefrom=&priceto=&curr=5&livefrom=&liveto=&kitchfrom=&kitchto=&flrfrom=&flrto=&flrsfrom=&flrsto=&source=0&relevance=0&text=&p=';

    $aviso = $url . $count;
    $aviso_url = file_get_contents($aviso);
    $document = phpQuery::newDocument($aviso_url);
    $all_count_pages = $document->find('span.bold_orange:eq(2)');
    preg_match_all('#[0-9]#', $all_count_pages, $current_pages);

    foreach ($current_pages[0] as $item2) {
        $int_current_pages .= $item2;
    }

    static $count_all_result = 0;
    static $all_count_result = 0;

    while ($count <= (int)$int_current_pages) {

        $aviso2 = $url . $count;
        $aviso_url2 = file_get_contents($aviso2);
        $document2 = phpQuery::newDocument($aviso_url2);

        for ($i = 0; $i < 36; $i++){

            $all_count_result++;

            $all_result = "div.line_ads:eq($i)";
            $one_result = $document2->find($all_result);

            $in_result = pq($one_result);
            $find_price = 'div.price';

            $stringPrice = explode('</span>', $in_result->find($find_price));
            preg_match_all('#[0-9]#', $stringPrice[1], $cena);
            $current_price = '';

            foreach ($cena[0] as $item) {
                $current_price .= $item;
            }

            $current_price = (int)$current_price;

            if ($current_price >= $from && $current_price <= $to ) {
                $hentry[$count] = $one_result;
                $result .= $hentry[$count];
                $count_all_result++;
            }

        }

        $count++;

    }

    ?>

    <div class="container">
        <div class="row">
            <?php echo $result; ?>
        </div>
    </div>
    <div class="all_count_result">
        <?php
        echo 'Показано результатов - ' . $count_all_result . '<br>';
        echo 'Всего результатов - ' . $all_count_result . '<br>';
        echo 'Время поиска - '.(microtime(true) - $start).' сек.';
        ?>
        <br>
        <a href="/" class="back">Новый поиск</a>
    </div>

    <script>

        $(function() {
            $("img").lazyload({
                effect : "fadeIn"
            });
        });

    </script>

<?php

} else {

?>

    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h1>Введите парараметры поиска</h1>
                <form role="form" action="/" method="get">
                    <div class="form-group">
                        <label for="from">От грн.</label>
                        <input type="number" class="form-control" name="FROM" id="from">
                    </div>
                    <div class="form-group">
                        <label for="to">До грн.</label>
                        <input type="number" class="form-control" name="TO" id="to">
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-success">
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
}

?>

</body>
</html>
