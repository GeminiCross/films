<?php

use Films\Modules\View;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<style media="screen">
    .pagination {
        width: 34%!important;
        margin: auto;
        padding-top: 25px;
    }
    .navbar-nav {
        padding-left: 10px !important;
    }
    .page-item {
        margin: auto;
    }
    .card {
        margin-bottom: 15px;
    }
</style>
<body>
<div class="container">
    <div class="row">
        <div class="col-12">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">

                <div class="collapse navbar-collapse row">
                    <ul class="navbar-nav mr-auto col-3">
                        <li class="nav-item active">
                            <a class="nav-link" href="<?= View::makeUrl(); ?>">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= View::makeUrl(['controller' => 'film', 'action' => 'create']); ?>">Add</a>
                        </li>

                    </ul>
<!--                    <div class="btn-group col-6">-->
                        <form id="search" class="col-6" action="<?= View::makeUrl(['controller' => 'film', 'action' => 'search']); ?>" method="get">
                            <div class="input-group justify-content-end">
                                <input type="text" name="name" class="form-control col-9" placeholder="Name">
                                <select name="by" class="custom-select col-3">
                                    <option selected value="name">by name</option>
                                    <option value="actor">by actor</option>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" form="search" id="submit" type="submit">Button</button>
                                </div>
                            </div>
                        </form>
<!--                    </div>-->
                </div>
            </nav>
            <div class="d-flex justify-content-space-evenly">
                <br>
                <br>
            </div>
        </div>
    </div>
    <div>
            <p class="col-7"><?=@$this->message ?></p>
            <?= $this->content ?>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>