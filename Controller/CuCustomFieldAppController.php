<?php

/**
 * CuCustomFieldAppController
 *
 * @copyright        Copyright, Catchup, Inc.
 * @link            https://catchup.co.jp
 * @package            CuCustomField
 * @license            MIT
 */
class CuCustomFieldAppController extends AppController
{

	/**
	 * Helper
	 *
	 * @var array
	 */
	public $helpers = ['Blog.Blog'];

	/**
	 * Component
	 *
	 * @var     array
	 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

	/**
	 * サブメニューエレメント
	 *
	 * @var array
	 */
	public $subMenuElements = ['petit_custom_field'];

	/**
	 * ぱんくずナビ
	 *
	 * @var string
	 */
	public $crumbs = [
		['name' => 'プラグイン管理', 'url' => ['plugin' => '', 'controller' => 'plugins', 'action' => 'index']]
	];

	/**
	 * 管理画面タイトル
	 *
	 * @var string
	 */
	public $adminTitle = '';

	/**
	 * ブログコンテンツデータ
	 *
	 * @var array
	 */
	public $blogContentDatas = [];

	/**
	 * Before Filter
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		// ブログ設定データを取得
		if (ClassRegistry::isKeySet('Content')) {
			$ContentModel = ClassRegistry::getObject('Content');
		} else {
			$ContentModel = ClassRegistry::init('Content');
		}
		$this->blogContentDatas = $ContentModel->find('list', [
			'fields' => [
				'entity_id',
				'title',
			],
			'conditions' => [
				'plugin' => 'Blog',
				'type' => 'BlogContent',
			],
			'recursive' => -1,
		]);
		$this->set('customFieldConfig', Configure::read('cuCustomField'));
	}

	/**
	 * [ADMIN] 新規登録
	 *
	 */
	public function admin_add()
	{
		if ($this->request->data) {
			if ($this->{$this->modelClass}->save($this->request->data)) {
				$message = $this->name . '「' . $this->request->data[$this->modelClass]['name'] . '」を追加しました。';
				$this->setMessage($message, false, true);
				$this->redirect(['action' => 'index']);
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		$this->set('blogContentDatas', ['0' => '指定しない'] + $this->blogContentDatas);
		$this->render('form');
	}

	/**
	 * [ADMIN] 編集
	 *
	 * @param int $id
	 */
	public function admin_edit($id = null)
	{
		if (!$id) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(['action' => 'index']);
		}

		if (empty($this->request->data)) {
			$this->{$this->modelClass}->id = $id;
			$this->request->data = $this->{$this->modelClass}->read();
		} else {
			if ($this->{$this->modelClass}->save($this->request->data)) {
				$message = $this->name . ' ID:' . $this->request->data[$this->modelClass]['id'] . '」を更新しました。';
				$this->setMessage($message, false, true);
				$this->redirect(['action' => 'index']);
			} else {
				$this->setMessage('入力エラーです。内容を修正して下さい。', true);
			}
		}

		$this->set('blogContentDatas', ['0' => '指定しない'] + $this->blogContentDatas);
		$this->render('form');
	}

	/**
	 * [ADMIN] 削除
	 *
	 * @param int $id
	 */
	public function admin_delete($id = null)
	{
		if (!$id) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(['action' => 'index']);
		}

		if ($this->{$this->modelClass}->delete($id)) {
			$message = $this->name . ' ID:' . $id . ' を削除しました。';
			$this->setMessage($message, false, true);
			$this->redirect(['action' => 'index']);
		} else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}
		$this->redirect(['action' => 'index']);
	}

	/**
	 * [ADMIN] 削除処理　(ajax)
	 *
	 * @param int $id
	 */
	public function admin_ajax_delete($id = null)
	{
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		// 削除実行
		if ($this->_delete($id)) {
			clearViewCache();
			exit(true);
		}
		exit();
	}

	/**
	 * データを削除する
	 *
	 * @param int $id
	 * @return boolean
	 */
	protected function _delete($id)
	{
		// メッセージ用にデータを取得
		$data = $this->{$this->modelClass}->read(null, $id);
		// 削除実行
		if ($this->{$this->modelClass}->delete($data[$this->modelClass]['id'])) {
			$this->{$this->modelClass}->saveDbLog($this->name . ' ID:' . $data[$this->modelClass]['id'] . ' を削除しました。');
			return true;
		} else {
			return false;
		}
	}

	/**
	 * [ADMIN] 無効状態にする
	 *
	 * @param int $id
	 */
	public function admin_unpublish($id)
	{
		if (!$id) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(['action' => 'index']);
		}
		if ($this->_changeStatus($id, false)) {
			$this->setMessage($this->name . ' ID:' . $id . 'を「無効」状態に変更しました。', false, true);
			$this->redirect(['action' => 'index']);
		}
		$this->setMessage('処理に失敗しました。', true);
		$this->redirect(['action' => 'index']);
	}

	/**
	 * [ADMIN] 有効状態にする
	 *
	 * @param int $id
	 */
	public function admin_publish($id)
	{
		if (!$id) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(['action' => 'index']);
		}
		if ($this->_changeStatus($id, true)) {
			$this->setMessage($this->name . ' ID:' . $id . 'を「有効」状態に変更しました。', false, true);
			$this->redirect(['action' => 'index']);
		}
		$this->setMessage('処理に失敗しました。', true);
		$this->redirect(['action' => 'index']);
	}

	/**
	 * [ADMIN] 無効状態にする（AJAX）
	 *
	 * @param int $id
	 */
	public function admin_ajax_unpublish($id)
	{
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, false)) {
			clearViewCache();
			exit(true);
		} else {
			$this->ajaxError(500, $this->{$this->modelClass}->validationErrors);
		}
		exit();
	}

	/**
	 * [ADMIN] 有効状態にする（AJAX）
	 *
	 * @param int $id
	 */
	public function admin_ajax_publish($id)
	{
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, true)) {
			clearViewCache();
			exit(true);
		} else {
			$this->ajaxError(500, $this->{$this->modelClass}->validationErrors);
		}
		exit();
	}

	/**
	 * ステータスを変更する
	 *
	 * @param int $id
	 * @param boolean $status
	 * @return boolean
	 */
	protected function _changeStatus($id, $status)
	{
		$data = $this->{$this->modelClass}->find('first', [
			'conditions' => ['id' => $id],
			'recursive' => -1
		]);
		$data[$this->modelClass]['status'] = $status;
		if ($status) {
			$data[$this->modelClass]['status'] = true;
		} else {
			$data[$this->modelClass]['status'] = false;
		}
		if ($this->{$this->modelClass}->save($data)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * [ADMIN] ListBehavior利用中のデータ並び順を割り振る
	 *
	 */
	function admin_reposition()
	{
		if ($this->{$this->modelClass}->Behaviors->enabled('List')) {
			if ($this->{$this->modelClass}->fixListOrder()) {
				$message = $this->name . ' データに並び順（position）を割り振りました。';
				$this->setMessage($message, false, true);
				$this->redirect(['action' => 'index']);
			} else {
				$this->setMessage('データベース処理中にエラーが発生しました。', true);
			}
		} else {
			$this->setMessage('ListBehaviorが無効のモデルです。', true);
		}
		$this->redirect(['action' => 'index']);
	}

}
