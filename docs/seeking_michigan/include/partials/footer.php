      </div>
    </div>
    <div id="footer">
      <div id="main-whitebox-bottom"></div>
      <div class="michigan-circle">
        <a href="http://mi.gov">
          <img src="<?= SEEKING_MICHIGAN_HOST ?>/images/michigan-state-circle.png" alt="michigan seal" />
        </a>
      </div>
      <div class="wrapper">
        <div class="links">
          <ul class="first">
            <li><a href="<?= SEEKING_MICHIGAN_HOST ?>">Home</a></li>
            <li><a href="seek_advanced.php">Seek</a></li>
            <li><a href="<?= SEEKING_MICHIGAN_HOST ?>/discover">Discover</a></li>
            <li><a href="<?= SEEKING_MICHIGAN_HOST ?>/look">Look</a></li>
            <li><a href="<?= SEEKING_MICHIGAN_HOST ?>/teach">Teach</a></li>
          </ul>
          <ul class="second">
            <li><a href="<?= SEEKING_MICHIGAN_HOST ?>/about">About</a></li>
            <li><a href="<?= SEEKING_MICHIGAN_HOST ?>/resources">Resources</a></li>
            <li><a href="<?= SEEKING_MICHIGAN_HOST ?>/archive">Archives</a></li>
            <li><a href="<?= SEEKING_MICHIGAN_HOST ?>/copyright">Copyright</a></li>
          </ul>
          <p>&copy; 2008-<?= date('Y'); ?> <a href="<?= SEEKING_MICHIGAN_HOST ?>">SeekingMichigan.org</a>. All Rights Reserved.  </p>
        </div>
      </div>
    </div>
  </div>
  <? if($hidden_partials): ?>
    <? foreach($hidden_partials as $partial => $locals): ?>
      <div id="<?= $partial ?>" style="display:none">
        <? app()->partial($partial, $locals); ?>
      </div>
    <? endforeach; ?>
  <? endif; ?>
</body>
</html>
