<?php
/**
 * registration create view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->element('Registrations.scripts');
echo $this->NetCommonsHtml->script(array(
	'/registrations/js/registrations_edit.js'
));
$jsPastRegistrations = NetCommonsAppController::camelizeKeyRecursive($pastRegistrations);
?>

<div ng-controller="Registrations.add"
	 ng-init="initialize(<?php echo h(json_encode($jsPastRegistrations)); ?>,
						'<?php echo $this->data['ActionRegistrationAdd']['create_option']; ?>')">
	<div class="modal-body">
			<div class="row">

				<div class="col-lg-12">
					<p>
						<?php echo __d('registrations', 'You can create a new registration. Please choose how to create.'); ?>
					</p>
				</div>

				<?php /* ファイル送信は、FormHelperでform作成時、'type' => 'file' 必要。記述すると enctype="multipart/form-data" が追加される */ ?>
				<?php echo $this->NetCommonsForm->create('ActionRegistrationAdd', array(
				'type' => 'file',
				)); ?>
				<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
				<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>

				<?php echo $this->NetCommonsForm->error('create_option', null, array('class' => 'help-block')); ?>

				<div class="form-group col-lg-12">
					<?php echo $this->element('Registrations.RegistrationAdd/create_new'); ?>
				</div>

				<!--<div class="form-group col-lg-12">-->
				<!--	--><?php //echo $this->element('Registrations.RegistrationAdd/create_template'); ?>
				<!--</div>-->

				<div class="form-group col-lg-12">
					<?php echo $this->element('Registrations.RegistrationAdd/create_reuse'); ?>
				</div>

				<div class="text-center">
					<?php echo $this->BackTo->pageLinkButton(__d('net_commons', 'Cancel'), array('icon' => 'remove')); ?>
					<?php echo $this->Button->save(__d('net_commons', 'NEXT'), array('icon' => 'chevron-right')) ?>
				</div>

				<?php echo $this->NetCommonsForm->end(); ?>
			</div>
	</div>
</div>