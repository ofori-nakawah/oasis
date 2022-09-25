<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>vork.</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Lilita One', cursive;
            }
            .outer {
                display: table;
                position: absolute;
                top: 0;
                left: 0;
                height: 100%;
                width: 100%;
            }

            .middle {
                display: table-cell;
                vertical-align: middle;
            }

            .inner {
                margin-left: auto;
                margin-right: auto;
                width: 50%;
                text-align: center;
                font-size: 64px;
                /* Whatever width you want */
            }
        </style>
    </head>
    <body>
    <div class="outer">
        <div class="middle">
            <div class="inner">
                <b>vork.</b>
            </div>
        </div>
    </div>
    </body>
</html>
