<nav class="navbar navbar-expand fixed-top be-top-header">
    <div class="container-fluid">
        <div class="be-navbar-header"><a class="navbar-brand"></a> </div>
        <div class="be-right-navbar">
            <?php if( null !== session_get('isUserLoggedIn') && session_get('isUserLoggedIn') ): ?>
                <ul class="nav navbar-nav float-right be-user-nav">
                <li class="nav-item dropdown">
                    <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle">
                        <span class="user-name-top"><?php echo e(substr(session_get('customerName'),0,1)); ?></span>
                    </a>
                    <div role="menu" class="dropdown-menu">
                        <div class="user-info">
                            <div class="user-name">Hi, <?php echo e(session_get('customerName')); ?> </div>
                            <div class="user-position online"></div>
                        </div>
                        <a href="<?php echo e(url('logout')); ?>" class="dropdown-item"><span class="icon mdi mdi-power"></span> Logout</a>
                    </div>
                </li>
            </ul>
            <?php elseif(!empty(session_get('customerName'))): ?>
                <ul class="nav navbar-nav float-right be-icons-nav">
                    Welcome : <strong><?php echo e(session_get('customerName')); ?></strong>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>