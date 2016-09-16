<?php include_once __DIR__ . '/header.php';?>
<div class="container">
    <div class="col-xs-6 col-xs-offset-3">
        <img src="img/logo.png" alt="Optimizely" class="img-responsive logo center-block" width="200">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">Your projects</h4>
            </div>
            <ul class="list-group">
                <?php foreach($projects as $project): ?>
                    <li class="list-group-item">
                        <?= $project['project_name']; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Your API token</h4>
            </div>
            <div class="panel-body text-center">
                <p>
                    <code><?= isset($_SESSION['oauth2token']) ? $_SESSION['oauth2token'] : ''; ?></code>
                </p>
                <p>
                    <a href="refresh.php">
                        Refresh now
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/footer.php';?>