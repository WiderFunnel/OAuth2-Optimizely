<?php include_once __DIR__ . '/header.php';?>
    <div class="container">
        <?php if($token):?>
        <div class="col-xs-6 col-xs-offset-3">
            <div class="panel <?= $refreshed ? 'panel-success' : 'panel-info';?>">
                <div class="panel-heading">
                    <h4 class="panel-title">Refresh token</h4>
                </div>
                <div class="panel-body text-center">
                    <?php if($refreshed):?>
                        <p class="lead">Your token has been refreshed!</p>
                    <?php else: ?>
                        <p class="lead">
                            Your token was still valid. <br>
                            <small class="text-info">It will expire <?php echo \Carbon\Carbon::createFromTimestamp($token->getExpires())->diffForHumans() ?></small>
                        </p>
                    <?php endif;?>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Your API token</h4>
                </div>
                <div class="panel-body text-center">
                    <code><?= isset($_SESSION['oauth2token']) ? $_SESSION['oauth2token'] : ''; ?></code>
                </div>
            </div>
        </div>
        <?php else:?>
        <div class="col-xs-6 col-xs-offset-3">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title">Refresh token</h4>
                </div>
                <div class="panel-body text-center">
                    <p class="lead">You didn't have any token in session.</p>
                    <p>
                        <a href="/">
                            Create one now >
                        </a>
                    </p>
                </div>
            </div>
        </div>
        <?php endif;?>
    </div>
<?php include_once __DIR__ . '/footer.php';?>