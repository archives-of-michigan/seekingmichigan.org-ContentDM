<?
$increase = S_RESULTS_MENU_LENGTH;
$currentpage = ceil($start[1] / $maxrecs);
$disp = array_slice($entries, $start[0], $increase);
$end = $start[0] + $increase;
?>
<? if($isRes && $_GET['show_all'] != 'true'): ?>
<ul>
  <li class="previous">
    <? if($currentpage == 1): ?>
      Previous
    <? else: ?>
      <?
        if($currentpage == $start[0]){
          $prev_url = $self.'?'.htmlentities(stripUrlVar($querystr, 'CISOSTART')).'&amp;CISOSTART='.($start[0] - $increase).','.($start[1]-($maxrecs));
        } else {  
          $prev_url = $self.'?'.htmlentities(stripUrlVar($querystr, 'CISOSTART')).'&amp;CISOSTART='.$start[0].','.($start[1]-($maxrecs));
        }
      ?>
      <a href="<?= $prev_url ?>">Previous</a>
    <? endif; ?>
  </li>
  <? foreach ($disp as $d): ?>
    <? $page = (($d*$maxrecs)-($maxrecs-1)); ?>
    <li>
      <? if($start[1] == $page): ?>
        <?= $d ?>
      <? else: ?>
        <a href="<?= $self ?>?<?= htmlentities(stripUrlVar($querystr, 'CISOSTART')) ?>&amp;CISOSTART=<?= $start[0] ?>,<?= $page ?>"><?= $d ?></a>
      <? endif; ?>
    </li>
  <? endforeach; ?>
  <li class="next">
    <? if($currentpage == $totalpages): ?>
      Next
    <? else: ?>
      <?
        if($currentpage == $d){
          $next_url = $self.'?'.htmlentities(stripUrlVar($querystr, 'CISOSTART')).'&amp;CISOSTART='.($start[0] + $increase).','.($start[1]+($maxrecs));
        } else {
          $next_url = $self.'?'.htmlentities(stripUrlVar($querystr, 'CISOSTART')).'&amp;CISOSTART='.$start[0].','.($start[1]+($maxrecs));
        } 
      ?>
      <a href="<?= $next_url ?>">Next</a>
    <? endif; ?>
  </li>
</ul>
<? endif ?>