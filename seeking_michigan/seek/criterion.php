<? $num = isset($num) ? $num : 0; ?>
<fieldset class="mod">
  <legend class="hidden">Initial Search Options:</legend>
  <select name="CISOOP<?= $num ?>" class="search-modifier">
    <option value="all" <? if($num == 1): ?>selected="selected"<? endif; ?>>All of the word</option>
    <option value="exact" <? if($num == 2): ?>selected="selected"<? endif; ?>>The exact phrase</option>
    <option value="any" <? if($num == 3): ?>selected="selected"<? endif; ?>>Any of the words</option>
    <option value="none" <? if($num == 4): ?>selected="selected"<? endif; ?>>None of the words</option>
  </select>
  <input type="text" name="CISOBOX<?= $num ?>" class="input-text" value="" />
  <select name="CISOFIELD<?= $num ?>" class="search-type">
    <option value="CISOSEARCHALL">All Fields</option>
    <option value="title">Title</option>
    <option value="subjec">Subject</option>
    <option value="descri">Description</option>
    <option value="creato">Creator</option>
    <option value="publis">Publisher</option>
    <option value="contri">Contributors</option>
    <option value="date">Date</option>
    <option value="type">Type</option>
    <option value="format">Format</option>
    <option value="identi">Identifier</option>
    <option value="source">Source</option>
    <option value="langua">Language</option>
    <option value="relati">Relation</option>
    <option value="covera">Coverage</option>
    <option value="rights">Rights</option>
    <option value="audien">Audience</option>
    <option id="death_1" value="descri">First Name (Death Records)</option>
    <option id="death_2" value="creato">Last Name (Death Records)</option>
    <option id="death_3" value="subjec">City/Village/Township (Death Records)</option>
    <option id="death_4" value="title">County (Death Records)</option>
    <option id="death_5" value="format">Death Year (Death Records)</option>
    <option id="death_6" value="publis">Birth Year (Death Records)</option>
    <option id="death_7" value="contri">Age (Death Records)</option>
    <option id="death_8" value="source">Father's Last Name (Death Records)</option>
    <option id="death_9" value="identi">Father's Given Name (Death Records)</option>
  </select>
</fieldset>