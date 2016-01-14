<?php
/**
 * registration page setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->element('Registrations.scripts'); ?>

<div ng-controller="Registrations">

	<article>

		<?php echo $this->element('Registrations.Registrations/add_button'); ?>

		<div class="pull-left">
			<?php echo $this->element('Registrations.Registrations/answer_status'); ?>
		</div>

		<div class="clearfix"></div>

		<p>
			<?php echo __d('registrations', 'no registration'); ?>
		</p>

		<?php if (Current::permission('content_creatable')) : ?>
			<p>
				<?php echo __d('registrations', 'Please create new registration by pressing the "+" button.'); ?>
			</p>
		<?php endif ?>

	</article>

</div>

