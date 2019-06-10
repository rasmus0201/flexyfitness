<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
        <title>OBBC Holdtilmelding</title>
        <link href='https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons' rel="stylesheet">
        <link rel="stylesheet" href="{{ mix('/assets/css/app.css') }}">
    </head>
    <body>
        <div id="app">
            <v-app>
                <notification></notification>
                <navigation class="mb-2"></navigation>
                <router-view></router-view>
            </v-app>
        </div>

        <script src="{{ mix('/assets/js/app.js') }}"></script>
    </body>
</html>
