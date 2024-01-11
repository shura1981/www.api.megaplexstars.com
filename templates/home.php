<!DOCTYPE html>
<html>

<head>
   <meta charset='utf-8'>
   <meta http-equiv='X-UA-Compatible' content='IE=edge'>
   <title>Home</title>
   <meta name='viewport' content='width=device-width, initial-scale=1'>
   <link rel="stylesheet" href="public/css/style.css">
</head>

<body>
   <h1>Hello,
      <?= htmlspecialchars($name) ?>!
   </h1>
   <div>
    <?= $htmlContent ?>
</div>

</body>

</html>