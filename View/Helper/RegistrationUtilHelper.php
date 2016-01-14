<?php
/**
 * Questionnares App Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

//App::uses('AppHelper', 'View/Helper');

/**
 * Questionnares Utility Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Registrations\View\Helper
 */
class RegistrationUtilHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'NetCommonsHtml',
		'Html'
	);

/**
 * __construct
 *
 * @param View $view View
 * @param array $settings 設定値
 * @return void
 */
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
	}

/**
 * getAnswerButtons 回答済み 回答する テストのボタン表示
 *
 * @param array $registration 回答データ
 * @return string
 */
	public function getAnswerButtons($registration) {
		//
		//回答ボタンの(回答済み|回答する|テスト)の決定
		//
		// satus != 公開状態 つまり編集者が見ている場合は「テスト」
		//
		// 公開状態の場合が枝分かれする
		// 公開時期にマッチしていない = 回答前＝回答する（disabled） 回答後＝回答済み（disabled）
		//
		// 公開期間中
		// 繰り返しの回答を許さない = 回答前＝回答する　回答後＝回答済み（Disabled）
		// 繰り返しの回答を許す = いずれの状態でも「回答する」

		$key = $registration['Registration']['key'];

		// 編集権限がない人が閲覧しているとき、未公開登録フォームはFindされていないので対策する必要はない
		// ボタン表示ができるかできないか
		// 編集権限がないのに公開状態じゃない登録フォームの場合はボタンを表示しない
		//
		//if ($registration['Registration']['status'] != WorkflowComponent::STATUS_PUBLISHED && !$editable) {
		//	return '';
		//}

		$buttonStr = '<a class="btn btn-%s registration-listbtn %s" %s href="%s">%s</a>';
		$disabledButtonStr = '<span class="btn btn-%s registration-listbtn %s" %s href="%s">%s</span>';

		// ボタンの色
		// ボタンのラベル
		if ($registration['Registration']['status'] != WorkflowComponent::STATUS_PUBLISHED) {
			$answerButtonClass = 'info';
			$answerButtonLabel = __d('registrations', 'Test');
			$url = NetCommonsUrl::actionUrl(array(
				'controller' => 'registration_answers',
				'action' => 'test_mode',
				Current::read('Block.id'),
				$key,
				'frame_id' => Current::read('Frame.id'),
			));
			return sprintf($buttonStr, $answerButtonClass, '', '', $url, $answerButtonLabel);
		} else {
			$url = NetCommonsUrl::actionUrl(array(
				'controller' => 'registration_answers',
				'action' => 'view',
				Current::read('Block.id'),
				$key,
				'frame_id' => Current::read('Frame.id'),
			));
		}

		// 何事もなければ回答可能のボタン
		$answerButtonLabel = __d('registrations', 'Answer');
		$answerButtonClass = 'success';
		$answerButtonDisabled = '';

		// 操作できるかできないかの決定
		// 期間外だったら操作不可能
		// 繰り返し回答不可で回答済なら操作不可能
		if ($registration['Registration']['period_range_stat'] != RegistrationsComponent::REGISTRATION_PERIOD_STAT_IN
			|| (in_array($key, $this->_View->viewVars['ownAnsweredKeys'])
				&& $registration['Registration']['is_repeat_allow'] == RegistrationsComponent::PERMISSION_NOT_PERMIT)) {
			$answerButtonClass = 'default';
			$answerButtonDisabled = 'disabled';
			$buttonStr = $disabledButtonStr;
		}

		// ラベル名の決定
		if ($registration['Registration']['period_range_stat'] == RegistrationsComponent::REGISTRATION_PERIOD_STAT_BEFORE) {
			// 未公開
			$answerButtonLabel = __d('registrations', 'Unpublished');
		}
		if (in_array($key, $this->_View->viewVars['ownAnsweredKeys'])) {
			// 回答済み
			$answerButtonLabel = __d('registrations', 'Finished');
		}

		return sprintf($buttonStr, $answerButtonClass, '', $answerButtonDisabled, $url, $answerButtonLabel);
	}

/**
 * getAggregateButtons 集計のボタン表示
 *
 * @param array $registration 回答データ
 * @param array $options option
 * @return string
 */
	public function getAggregateButtons($registration, $options = array()) {
		//
		// 集計ボタン
		// 集計表示しない＝ボタン自体ださない
		// 集計表示する＝回答すみ、または回答期間終了　  集計ボタン
		// 　　　　　　　登録フォーム自体が公開状態にない(not editor)
		//			     未回答＆回答期間内　　　　　　　集計ボタン（disabled）
		$key = $registration['Registration']['key'];

		if ($registration['Registration']['is_total_show'] == RegistrationsComponent::EXPRESSION_NOT_SHOW) {
			return '';
		}

		$disabled = '';

		// 登録フォーム本体が始まってない
		if ($registration['Registration']['period_range_stat'] == RegistrationsComponent::REGISTRATION_PERIOD_STAT_BEFORE) {
			$disabled = 'disabled';
		} else {
			// 始まっている
			// 集計結果公開期間外である
			$nowTime = (new NetCommonsTime())->getNowDatetime();
			if ($registration['Registration']['total_show_timing'] == RegistrationsComponent::USES_USE &&
				strtotime($nowTime) < strtotime($registration['Registration']['total_show_start_period'])) {
				$disabled = 'disabled';
			} else {
				// 集計結果公開期間内である
				// 一つでも回答している
				if (!in_array($key, $this->_View->viewVars['ownAnsweredKeys'])) {
					// 未回答
					$disabled = 'disabled';
				}
			}
		}

		list($title, $icon, $btnClass) = $this->_getBtnAttributes($options);
		$url = NetCommonsUrl::actionUrl(array(
			'controller' => 'registration_answer_summaries',
			'action' => 'view',
			Current::read('Block.id'),
			$key,
			'frame_id' => Current::read('Frame.id'),
		));

		// この登録フォームの編集権限を持っているなら無条件で集計表示ボタン操作できる
		if ($this->_View->Workflow->canEdit('Registration', $registration)) {
			$disabled = '';
		}

		$html = $this->NetCommonsHtml->link($icon . $title,
			$url, array(
			'class' => $btnClass . ' ' . $disabled,
			'escape' => false
		));

		return $html;
	}
/**
 * _getBtnAttributes ボタン属性整理作成
 *
 * @param array $options option
 * @return array
 */
	protected function _getBtnAttributes($options) {
		$btnClass = 'btn btn-default registration-listbtn';
		if (isset($options['class'])) {
			$btnClass = 'btn btn-' . $options['class'];
		}
		if (isset($options['size'])) {
			$btnClass .= ' btn-' . $options['size'];
		}

		$title = '';
		if (isset($options['title'])) {
			$title = $options['title'];
		}
		$icon = '';
		if (isset($options['icon'])) {
			$icon = '<span class="glyphicon glyphicon-' . $options['icon'] . '" aria-hidden="true"></span>';
		}
		return array($title, $icon, $btnClass);
	}
}
