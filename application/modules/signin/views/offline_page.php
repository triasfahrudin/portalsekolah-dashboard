<!doctype html>
<title>Site Maintenance</title>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

<style>
  body { text-align: center; padding: 40px;  background: #343d46;color: rgb(234, 247, 247)}
  h1 { font-size: 50px; }
  body { font: 20px Helvetica, sans-serif;  }
  article { display: block; text-align: left; width: 850px; margin: 0 auto; }
  a { color: #dc8100; text-decoration: none; }
  /*a:hover { color: #333; text-decoration: none; }*/

  .box{
    margin: 10px auto;
    width: 400px;
    height: 50px;

  }
  .container-1{
    overflow: hidden;
    width: 400px;
    vertical-align: middle;
    white-space: nowrap;
  }

  .container-1 input#search{
    width: 400px;
    height: 50px;
    /*background: #2b303b;*/
    border: none;
    font-size: 10pt;
    float: left;
    color: #2b303b;
    padding-left: 15px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
  }

  .container-1 input#search::-webkit-input-placeholder {
      color: #65737e;
  }

  .container-1 input#search:-moz-placeholder { /* Firefox 18- */
     color: #65737e;
  }

  .container-1 input#search::-moz-placeholder {  /* Firefox 19+ */
     color: #65737e;
  }

  .container-1 input#search:-ms-input-placeholder {
     color: #65737e;
  }

  .container-1 button.icon{
    -webkit-border-top-right-radius: 5px;
    -webkit-border-bottom-right-radius: 5px;
    -moz-border-radius-topright: 5px;
    -moz-border-radius-bottomright: 5px;
    border-top-right-radius: 5px;
    border-bottom-right-radius: 5px;

    border: none;
    background: #232833;
    height: 50px;
    width: 100px;
    color: #4f5b66;
    opacity: 0;
    font-size: 10pt;

    -webkit-transition: all .55s ease;
    -moz-transition: all .55s ease;
    -ms-transition: all .55s ease;
    -o-transition: all .55s ease;
    transition: all .55s ease;
  }

  .container-1:hover button.icon,
  .container-1:active button.icon,
  .container-1:focus button.icon{
    outline: none;
    opacity: 1;
    margin-left: -100px;
  }

  .container-1:hover button.icon:hover{
    background: #1a0dab;
    color: white;
    cursor: pointer;
    cursor: hand;
  }

  .alert {
    padding: 8px 35px 8px 14px;
    margin-bottom: 18px;
    color: #c09853;
    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
    background-color: #fcf8e3;
    border: 1px solid #fbeed5;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
  }

  .alert-heading {
    color: inherit;
  }

  .alert .close {
    position: relative;
    top: -2px;
    right: -21px;
    line-height: 18px;
  }

  .alert-success {
    color: #468847;
    background-color: #dff0d8;
    border-color: #d6e9c6;
  }

  .alert-danger,
  .alert-error {
    color: #b94a48;
    background-color: #f2dede;
    border-color: #eed3d7;
  }

  .alert-info {
    color: #3a87ad;
    background-color: #d9edf7;
    border-color: #bce8f1;
  }

  .alert-block {
    padding-top: 14px;
    padding-bottom: 14px;
  }

  .alert-block > p,
  .alert-block > ul {
    margin-bottom: 0;
  }

  .alert-block p + p {
    margin-top: 5px;
  }
</style>

<article>
    <h1>We&rsquo;ll be back soon!</h1>
    <div>
        <p>Maaf untuk ketidaknyamanan ini, namun kami sedang melakukan beberapa perbaikan.</p>
        <p>
          Anda selalu bisa menghubungi kami di <a href=""><?php echo get_settings('email_cs')?></a>
        </p>
       
        <?php if(has_alert()):
          foreach(has_alert() as $type => $message): ?>
            <div class="alert alert-dismissible <?php echo $type; ?>">
              <?php echo $message; ?>
            </div>
          <?php endforeach;
        endif; ?>

          
        <p>&mdash; Team</p>
    </div>
</article>
