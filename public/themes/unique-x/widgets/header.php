<!-- Navigation -->
<nav id="nav" class="">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header page-scroll">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
            </button>
            <a class="navbar-logo" href="<?=Gila\Config::base_url()?>">
                <img src="assets/gila-logo.png" style="max-width:32px;">&nbsp;Gila CMS
            </a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li class="hidden">
                    <a href="https://gilacms.com"></a>
                </li>
                <li>
                    <a class="page-scroll" href="#services"><?=__('Services')?></a>
                </li>
                <li>
                    <a class="page-scroll" href="blog">Blog</a>
                </li>
                <!--li class="">
                    <a class="page-scroll dropdown-toggle dropdown" role="button" id="dropdownMenuLink"  data-toggle="dropdown" href="#">Addons</a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <li>
                            <a class="page-scroll" href="addons/packages">Packages</a>
                        </li>
                        <li>
                            <a class="page-scroll" href="addons/themes">Themes</a>
                        </li>
                    </ul>
                </li-->
                <li class="">
                    <a class="page-scroll dropdown-toggle dropdown" role="button" id="dropdownMenuLink"  data-toggle="dropdown" href="#">Sources</a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <li>
                            <a class="page-scroll" href="https://gila-cms.readthedocs.io" target="_blank">Documentation</a>
                        </li>
                        <li>
                            <a class="page-scroll" href="category/2">Tutorials</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="page-scroll" href="#contact"><?=__('Contact')?></a>
                </li>
                <li class="">
                <?php $langCap=['en'=>'EN','es'=>'ES','el'=>'GR']; ?>
                    <a class="page-scroll dropdown-toggle dropdown" role="button" id="dropdownMenuLang"  data-toggle="dropdown" href="#"><?=strtoupper($langCap[session::key('lang')])?><!-- span class="caret"></span--></a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLang">
                        <li>
                            <a class="lang-btn <?=(session::key('lang')=='en'?'active':'')?>" href="?lang=en">EN</a>
                        </li>
                        <li>
                            <a class="lang-btn <?=(session::key('lang')=='es'?'active':'')?>" href="?lang=es">ES</a>
                        </li>
                        <li>
                            <a class="lang-btn <?=(session::key('lang')=='el'?'active':'')?>" href="?lang=el">GR</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>
