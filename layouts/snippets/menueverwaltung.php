<?php
  include(LAYOUTPATH.'languages/menueverwaltung_'.$this->user->rolle->language.'.php');
?>
<h2><?php echo $strTitle; ?></h2>
<p>
<div class="rTable">
<?php
foreach ($this->menues AS $menue) { ?>
	<div class="rTableRow">
		<div class="rTableHead">Name</div>
		<div class="rTableCell"><input type="Text" name="name" value="<?php echo $menue->get('name'); ?>"></div>
		<div class="rTableHead">Link</div>
		<div class="rTableCell"><input type="Text" name="name" value="<?php echo $menue->get('links'); ?>"></div>
		<div class="rTableHead">Obermenü</div>
		<div class="rTableCell"><input type="Text" name="name" value="<?php echo $menue->get('obermenue'); ?>"></div>
		<div class="rTableHead">Target</div>
		<div class="rTableCell"><input type="Text" name="name" value="<?php echo $menue->get('target'); ?>"></div>
		<div class="rTableHead">order</div>
		<div class="rTableCell"><input type="Text" name="name" value="<?php echo $menue->get('order'); ?>"></div>
	</div>
<?php
}
?>
</div>
<div id="menue_form" style="display: none">
	<h3>Neuer Menüpunkt</h3>
	<div class="rTable">
		<div class="rTableRow">
			<div class="rTableHead">Name</div>
			<div class="rTableCell"><input type="Text" name="name" value="<?php echo $menue->get('name'); ?>"></div>
			<div class="rTableHead">Link</div>
			<div class="rTableCell"><input type="Text" name="name" value="<?php echo $menue->get('links'); ?>"></div>
			<div class="rTableHead">Obermenü</div>
			<div class="rTableCell"><input type="Text" name="name" value="<?php echo $menue->get('obermenue'); ?>"></div>
			<div class="rTableHead">Target</div>
			<div class="rTableCell"><input type="Text" name="name" value="<?php echo $menue->get('target'); ?>"></div>
			<div class="rTableHead">order</div>
			<div class="rTableCell"><input type="Text" name="name" value="<?php echo $menue->get('order'); ?>"></div>
		</div>
	</div>
</div>
