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
 * Datetime Behavior
 *
 * @package  Registrations\Registrations\Model\Befavior\Answer
 * @author Allcreator <info@allcreator.net>
 */
class RegistrationAnswerDatetimeBehavior extends RegistrationAnswerBehavior {

/**
 * this answer type
 *
 * @var int
 */
	protected $_myType = RegistrationsComponent::TYPE_DATE_AND_TIME;

/**
 * datetime validate check type
 *
 * @var array
 */
	protected $_datetmValidateType = array(
		RegistrationsComponent::TYPE_DATE_AND_TIME
	);

/**
 * Answer datetime format
 *
 * @var array
 */
	protected $_datetimeFormat = array(
		RegistrationsComponent::TYPE_OPTION_DATE => 'Y-m-d',
		RegistrationsComponent::TYPE_OPTION_TIME => 'H:i',
		RegistrationsComponent::TYPE_OPTION_DATE_TIME => 'Y-m-d H:i',
	);

/**
 * answerValidation 登録内容の正当性
 *
 * @param object &$model use model
 * @param array $data Validation対象データ
 * @param array $question 登録データに対応する項目
 * @param array $allAnswers 入力された登録すべて
 * @return bool
 */
	public function answerDatetimeValidation(&$model, $data, $question, $allAnswers) {
		if (! in_array($question['question_type'], $this->_datetmValidateType)) {
			return true;
		}

		$answer = $data['answer_value'];
		$ret = true;
		if ($question['is_require'] === true || $data['answer_value']) {
			if (!$this->_validateDatetime($model, $question['question_type_option'], $answer)) {
				$ret = false;
			}
			if (!$this->_validateTimeRange($model, $question, $answer)) {
				$ret = false;
			}
		}
		return $ret;
	}
/**
 * _validateDatetime 日付・時間の正当性
 *
 * @param object &$model use model
 * @param int $questionTypeOption 時間・日付オプション
 * @param string $answer 登録データ
 * @return bool
 */
	protected function _validateDatetime(&$model, $questionTypeOption, $answer) {
		if ($questionTypeOption == RegistrationsComponent::TYPE_OPTION_DATE) {
			if (! Validation::date($answer, 'ymd')) {
				$model->validationErrors['answer_value'][] =
					__d('registrations', 'Please enter a valid date in YY-MM-DD format.');
				return false;
			}
		} elseif ($questionTypeOption == RegistrationsComponent::TYPE_OPTION_TIME) {
			if (! Validation::time($answer)) {
				$model->validationErrors['answer_value'][] =
					__d('registrations', 'Please enter the time.');
				return false;
			}
		} elseif ($questionTypeOption == RegistrationsComponent::TYPE_OPTION_DATE_TIME) {
			if (! Validation::datetime($answer, 'ymd')) {
				$model->validationErrors['answer_value'][] =
					__d('registrations', 'Please enter a valid date and time.');
				return false;
			}
		} else {
			$model->validationErrors['answer_value'][] =
				__d('net_commons', 'Invalid request.');
			return false;
		}
		return true;
	}
/**
 * _validateDatetime 日付・時間の正当性
 *
 * @param object &$model use model
 * @param array $question 登録データに対応する項目
 * @param string $answer 登録データ
 * @return bool
 */
	protected function _validateTimeRange(&$model, $question, $answer) {
		if ($question['is_range'] != RegistrationsComponent::USES_USE) {
			return true;
		}
		$rangeResult = Validation::range(
			strtotime($answer),
			strtotime($question['min']) - 1,
			strtotime($question['max']) + 1);
		if ($rangeResult) {
			return true;
		}
		$model->validationErrors['answer_value'][] = sprintf(
			__d('registrations', 'Please enter the answer between %s and %s.'),
			date($this->_datetimeFormat[$question['question_type_option']], strtotime($question['min'])),
			date($this->_datetimeFormat[$question['question_type_option']], strtotime($question['max'])));
		return false;
	}
}