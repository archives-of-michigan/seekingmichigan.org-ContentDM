<?
$totalpages = ceil($search->total / $search->maxrecs);
$entries = array();
$i=0;
while($i<=$totalpages){
  $entries[]=$i;
  $i++;
}

$increase = 5;
$currentpage = ceil($search->start[1] / $search->maxrecs);
$disp = array_slice($entries, $search->start[0], $increase);
$end = $search->start[0] + $increase;
?>
<? if($isRes && $show_all != 'true'): ?>
<ul>
  <li class="previous">
    <? if($currentpage == 1): ?>
      Previous
    <? else: ?>
      <?
        if($currentpage == $search->start[0]){
          $prev_url = $self.'?'.htmlentities(stripUrlVar($querystr, 'CISOSTART')).'&amp;CISOSTART='.($search->start[0] - $increase).','.($search->start[1]-($search->maxrecs));
        } else {  
          $prev_url = $self.'?'.htmlentities(stripUrlVar($querystr, 'CISOSTART')).'&amp;CISOSTART='.$search->start[0].','.($search->start[1]-($search->maxrecs));
        }
      ?>
      <a href="<?= $prev_url ?>">Previous</a>
    <? endif; ?>
  </li>
  <? foreach ($disp as $d): ?>
    <? $page = (($d*$search->maxrecs)-($search->maxrecs-1)); ?>
    <li>
      <? if($search->start[1] == $page): ?>
        <?= $d ?>
      <? else: ?>
        <a href="<?= $self ?>?<?= htmlentities(stripUrlVar($querystr, 'CISOSTART')) ?>&amp;CISOSTART=<?= $search->start[0] ?>,<?= $page ?>"><?= $d ?></a>
      <? endif; ?>
    </li>
  <? endforeach; ?>
  <li class="next">
    <? if($currentpage == $totalpages): ?>
      Next
    <? else: ?>
      <?
        if($currentpage == $d){
          $next_url = $self.'?'.htmlentities(stripUrlVar($querystr, 'CISOSTART')).'&amp;CISOSTART='.($search->start[0] + $increase).','.($search->start[1]+($search->maxrecs));
        } else {
          $next_url = $self.'?'.htmlentities(stripUrlVar($querystr, 'CISOSTART')).'&amp;CISOSTART='.$search->start[0].','.($search->start[1]+($search->maxrecs));
        } 
      ?>
      <a href="<?= $next_url ?>">Next</a>
    <? endif; ?>
  </li>
</ul>
<? endif ?>
