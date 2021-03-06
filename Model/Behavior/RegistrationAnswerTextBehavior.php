<?php
/**
 * RegistrationValidate Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RegistrationAnswerBehavior', 'Registrations.Model/Behavior');

/**
 * Text Behavior
 *
 * @package  Registrations\Registrations\Model\Befavior\Answer
 * @author Allcreator <info@allcreator.net>
 */
class RegistrationAnswerTextBehavior extends RegistrationAnswerBehavior {

/**
 * this answer type
 *
 * @var int
 */
	protected $_myType = RegistrationsComponent::TYPE_TEXT;

/**
 * max length check type
 *
 * @var array
 */
	protected $_maxLengthCheckType = array(
		RegistrationsComponent::TYPE_TEXT,
		RegistrationsComponent::TYPE_TEXT_AREA
	);

/**
 * text validate check type
 *
 * @var array
 */
	protected $_textValidateType = array(
		RegistrationsComponent::TYPE_TEXT,
	);

/**
 * answerMaxLength 登録が登録フォームが許す最大長を超えていないかの確認
 *
 * @param object &$model use model
 * @param array $data Validation対象データ
 * @param array $question 登録データに対応する項目
 * @param int $max 最大長
 * @return bool
 */
	public function answerMaxLength(&$model, $data, $question, $max) {
		if (! in_array($question['question_type'], $this->_maxLengthCheckType)) {
			return true;
		}
		return Validation::maxLength($data['answer_value'], $max);
	}

/**
 * answerValidation 登録内容の正当性
 *
 * @param object &$model use model
 * @param array $data Validation対象データ
 * @param array $question 登録データに対応する項目
 * @param array $allAnswers 入力された登録すべて
 * @return bool
 */
	public function answerTextValidation(&$model, $data, $question, $allAnswers) {
		if (! in_array($question['question_type'], $this->_textValidateType)) {
			return true;
		}
		$ret = true;
		// 数値型登録を望まれている場合
		if ($question['question_type_option'] == RegistrationsComponent::TYPE_OPTION_NUMERIC) {
			if (!Validation::numeric($data['answer_value'])) {
				$ret = false;
				$model->validationErrors['answer_value'][] = __d('registrations', 'Number required');
			}
			if ($question['is_range'] == RegistrationsComponent::USES_USE) {
				$rangeRes = Validation::range(
					$data['answer_value'],
					intval($question['min']),
					intval($question['max']));
				if (!$rangeRes) {
					$ret = false;
					$model->validationErrors['answer_value'][] = sprintf(
						__d('registrations', 'Please enter the answer between %s and %s.'),
						$question['min'],
						$question['max']);
				}
			}
		} else {
			if ($question['is_range'] == RegistrationsComponent::USES_USE) {
				if (! Validation::minLength($data['answer_value'], intval($question['min'])) ||
					! Validation::maxLength($data['answer_value'], intval($question['max']))) {
					$ret = false;
					$model->validationErrors['answer_value'][] = sprintf(
						__d('registrations', 'Please enter the answer between %s letters and %s letters.'),
						$question['min'],
						$question['max']);
				}
			}
		}
		return $ret;
	}
}