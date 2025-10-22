<?php
function ui_header($title='',$meta=[])
{

    $metahtml='';
    if(isset($meta['keywords'])) {
        $keywords = $meta['keywords'];
        $metahtml .= <<<_EOT_
<meta name = "keywords" content = "{$keywords}" />
<meta name = "og:keywords" content = "{$keywords}" />
_EOT_;
    }

    if(isset($meta['description'])) {
            $desc=$meta['description'];
            $metahtml.=<<<_EOT_
    <meta name="description" content="{$desc}"/>
    <meta name="og:description" content="{$desc}"/>        
_EOT_;
    }

    $menuhtml='';
    if(isset($meta['menu'])){
        if($meta['menu']==false ){
            // do nothing for the menu
        }
    } else {
        $menuhtml = <<<_EOT_
_EOT_;
    }

    $html=<<<_EOT_
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{$title}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/bootstrap.bundle.js"></script>

   
    {$metahtml}
</head>

<body>

<div class="container">
<div id="fb-root"></div>

<div class="row" id="menu">
    <div class="col-sm-12"">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">    <img src="/images/logo.png"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    {$menuhtml}
                </div>
            </div>
        </nav>
    </div>
</div>

<P/>
<div class="container">

_EOT_;
    echo $html;
}

function ui_footer()
{
$html=<<<_EOT_
</div>
<div id="footer">
    &copy;     
</div>

</body>
</html>
_EOT_;

    echo $html;
}

function ui_error($err)
{
    $err=<<<_EOT_
<div class="alert alert-danger alert-dismissible fade show">
{$err} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
_EOT_;
    echo $err;
}

function ui_success($err)
{
    $err=<<<_EOT_
<div class="alert alert-info">
{$err}
</div>
_EOT_;
    echo $err;
}

function ui_message($msg)
{
    $err=<<<_EOT_
<div class="alert alert-info">
{$msg}
</div>
_EOT_;
    echo $err;
}

function ui_startcard($header="", $subheader="")
{
    $ret=<<<_EOT_
<div class="card text-center">
  <div class="card-header">
    Featured
  </div>
  <div class="card-body">
_EOT_;
    return $ret;
}
function ui_endcard($footer="")
{
    $ret=<<<_EOT_
</div>
  <div class="card-footer text-muted">
    2 days ago
  </div>
</div>

_EOT_;
    return $ret;
}

function ui_card($header, $subheader, &$content,$footer="")
{
    $html = ui_startcard($header,$subheader);
    $html.=$subheader;
    $html.=ui_endcard();
    return $html;
}



function ui_placename($place)
{
    return $place;
    $html='<a href="/places/'.rawurlencode(strtolower(str_replace(" ","_",$place))).'/">'.$place.'</a>';
    return $html;
}