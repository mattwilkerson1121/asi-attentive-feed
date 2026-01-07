<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Catalog Uploads</title>
  <style type="text/css">
    html,body {
      padding:0;
      margin:0;
    }
    body {
      font-size:16px;
      color:#333;
      font-family: _sans-serif, sans-serif;
    }
    #main {
      position:relative;
      display:block;
      width:100%;
      height:100%;
      padding:0;
      margin:0;
    }
    #page {
      display:block;
      margin:0 auto;
      padding:12px;
    }
    h1 {
      font-size: 1.5rem;
      line-height:1.2;
      border-bottom: 1px solid #999;
    }
    p {padding-top:1.225rem; color: #555;font-size:0.875rem; line-height:1.5}

    @media(min-width:769px){
      #page { padding:16px;}
      h1 {font-size: 2rem;}
      p {font-size:1rem;}
    }

</style>
</head>
<body>
  <header id="header"></header>
  <main id="main">
    <div id="page">
      <h1>ASI/Attentive Catalog Uploads</h1>
      <p>
        <?php 
          $output = shell_exec("python catalog-upload.py asi-attentive-product-feed-xs.json --validateOnly true --apiKey bW9PeWh1WDVVd3U5YzBmNUtZeWcwclZQQjJkTXdiU2YxcVVw");
          echo 'Response:' . $output; 
        ?>  
      </p>
    </div>
  </main>
  <footer id="footer"></footer>
  <!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script> !-->
</body>
</html>
