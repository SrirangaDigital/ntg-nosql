<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="https://cdn.plyr.io/2.0.16/plyr.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
</head>
<body>

    <div class="text-center">
        <img style="margin: 0 auto;" src="<?=$data['cover']?>" class="img-responsive">
        <audio controls>
          <source src="<?=DATA_URL . $data['id']?>/index.mp3" type="audio/mp3">
          <source src="<?=DATA_URL . $data['id']?>/index.ogg" type="audio/ogg">
        </audio>
    </div>
    <script src="https://cdn.plyr.io/2.0.16/plyr.js"></script>
    <script>plyr.setup();</script>

</body>
</html>