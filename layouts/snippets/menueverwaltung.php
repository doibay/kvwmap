<?php
  include(LAYOUTPATH.'languages/menueverwaltung_'.$this->user->rolle->language.'.php');
?>
<script>
function showForm(arg) {
	var menue_form = document.getElementById('menue_form'),
			menue_area = document.getElementById('menue_area');

	menue_form.setAttribute('style', "display: 'true'");
	menue_area.setAttribute('style', "display: none");
}

function saveForm(arg) {
	var menue_form = document.getElementById('menue_form'),
			menue_area = document.getElementById('menue_area');

	menue_form.setAttribute('style', "display: none");
	menue_area.setAttribute('style', "display: true");
}
</script>
<h2><?php echo $strTitle; ?></h2>
<p>
<div id="menue_area">
	<h3>Vorhandene Menüpunkt</h3>
	<input id="new_button" type="button" name="Neu" value="Neu" onclick="showForm(this);">
	<div id="menue_table" class="rTable" style="margin-top: 20">
		<div class="rTableRow">
			<div class="rTableHead large-20">Name</div>
			<div class="rTableHead large-40">Link</div>
			<div class="rTableHead large-10">Obermenü</div>
		</div>
	<?php
	foreach ($this->menues AS $menue) { ?>
		<div class="rTableRow">
			<div class="rTableCell large-20"><?php echo $menue->get('name'); ?></div>
			<div class="rTableCell large-40"><?php echo $menue->get('links'); ?></div>
			<div class="rTableCell large-10"><?php echo $menue->get('obermenue'); ?></div>
		</div>
	<?php
	}
	?>
	</div>
</div>

<div id="menue_form" style="display: none">
	<h3>Neuer Menüpunkt</h3>
	<div class="rTable">
		<div class="rTableRow">
			<div class="rTableHead large-20">Name</div>
			<div class="rTableCell large-60"><input type="Text" name="name" value="<?php echo $menue->get('name'); ?>" size="66"></div>
			<div class="rTableHead large-20">Link</div>
			<div class="rTableCell large-60"><input type="Text" name="name" value="<?php echo $menue->get('links'); ?>" size="66"></div>
			<div class="rTableHead large-20">Obermenü</div>
			<div class="rTableCell large-60"><input type="Text" name="name" value="<?php echo $menue->get('obermenue'); ?>" size="5"></div>
			<div class="rTableHead large-20">Target</div>
			<div class="rTableCell large-60"><input type="Text" name="name" value="<?php echo $menue->get('target'); ?>" size="66"></div>
			<div class="rTableHead large-20">order</div>
			<div class="rTableCell large-60"><input type="Text" name="name" value="<?php echo $menue->get('order'); ?>" size="5"></div>
		</div>
	</div>
	<input id="save_button" type="button" name="Speichern" value="Speichern" onclick="saveForm(this);" style="margin-top: 10px">
</div>
