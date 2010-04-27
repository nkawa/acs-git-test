<?php
ini_set("display_errors", 0);
ini_set("error_reporting", false);
ini_restore("error_reporting");
ini_restore("display_errors");

/**
 * Actionの拡張クラス
 * @access public
 * @package webapp/lib
 * @category action
 * @author y-yuki
 * @sourcefile
 *
 */
abstract class BaseAction extends Action
{
	/**
	 * Controllerオブジェクト 
	 * @var WebController
	 */
	protected $controller = '';
	
	/**
	 * Requestオブジェクト
	 * @var WebRequest
	 */
	protected $request = '';
	
	/* フォーム登録キー */
	private $formRegKey = 'form1';

	/* フォーム登録リスト */
	private $formList = array();
	
	/* エラー登録キー */
	private $errRegKey = 'error1';
	
	/* userオブジェクト */
	private $user = '';
	
	/* HTTPリクエストタイプ */
	private $formMethod = 'POST';

	/* HTTPリクエストターゲット */
	private $formTarget = '';

	/* FORM属性 */
	private $formAttributes = '';

	/* FROM送信チェック */
	private $formTrackSubmit = false;
	
	/* 画面遷移先のモジュール名 */
	private $moduleName = '';

	/* 画面遷移先のアクション名 */
	private $actionName = '';

	/* メッセージデータ */
	private $messages = '';

	/**
	 * 初期処理
	 * @access public
	 * @param Object $context context
	 * @return boolean 処理結果
	 */
	public function initialize ($context)
	{
		parent::initialize($context);

		// アクションの共通処理を実装する
		/* 初期値をセットする */
		$this->controller =$context->getController();
		$this->request = $context->getRequest();
		$this->user = $context->getUser();

		$this->moduleName = $context->getModuleName();
		$this->actionName = $context->getActionName();
		$this->messages = &CommonMessages::getInstance();

		/* requestオブジェクトにフォーム登録リストをセットする */
		$this->request->setAttributeByRef('formList', $this->formList);
		
		$request = &$context->getRequest();
		$user = &$context->getUser();
		
		// セッションからユーザIDが取得できず、POSTでユーザIDが取得可能な場合
		$user_id = $user->getAttribute('login_user_id');
		$justLogin = false;
		if (($user_id == NULL || $user_id == "") 
				&& ($_POST['userid'] != NULL && $_POST['userid'] != "")) {
			$input_user_id = $_POST['userid'];
			$input_passwd = $_POST['passwd'];

			// エラーチェック（.htpasswd、LDAPの順）
			$user_id = ACSSystem::check_passwd($input_user_id, $input_passwd);
			if ($user_id) {
				$justLogin = true;
				$getLogoutDateEverytime = ACSSystemConfig::get_keyword_value(
						ACSMsg::get_mst('system_config_group','D08'), 'GET_LOGOUT_DATE_EVERYTIME');		
				$user->setAttribute('getLogoutDateEverytime', $getLogoutDateEverytime);
			}

		}
		
		// 言語設定の実行
		if ($request->getparameter('acsmsg')) {
			ACSMsg::set_lang($request->getparameter('acsmsg'));
			ACSMsg::set_lang_cookie($request->getparameter('acsmsg'));
		}
	
		// 権限クリア
		$user->clearCredentials();

		// 認証済みを登録
		$user->setAuthenticated(true);

		// $acs_user_info_rowを設定する //
		$acs_user_info_row = array();

		if ($user_id) {
			$acs_user_info_row = ACSUser::get_user_info_row_by_user_id($user_id);
			
			// ユーザ情報が無い
			if ($user_id && !$acs_user_info_row['user_id']) {
				$acs_user_info_row['user_id'] = $user_id;
				$acs_user_info_row['user_community_id'] = ACS_PUBLIC_USER_COMMUNITY_ID;
				$acs_user_info_row['is_acs_user'] = false;
			} else {
				$acs_user_info_row['is_acs_user'] = true;

				// 権限登録
				$user->addCredential('ACS_USER');
			}

			// ログインユーザ(認証を通過したユーザ)かどうか
			$acs_user_info_row['is_login_user'] = true;

			// システム管理者かどうか
			if ($acs_user_info_row['administrator_flag'] == 't') {
				// 権限登録
				$user->addCredential('SYSTEM_ADMIN_USER');
			}

			// LDAPユーザかどうか (ファイル認証ユーザでなければLDAPユーザとみなす)
			$acs_user_info_row['is_ldap_user'] = !ACSSystem::is_htpasswd_user($user_id);
			// LDAP認証以外の場合、パスワード変更権限を付与
			if ($acs_user_info_row['is_ldap_user']) {
				$user->addCredential('LDAP_USER');
			} else {
				$user->addCredential('NOT_LDAP_USER');
			}

			// 未登録のLDAPユーザの場合は氏名を調べる
			if (!$acs_user_info_row['is_acs_user'] && $acs_user_info_row['is_ldap_user']) {
				$ldap_user_info_row = ACSLDAP::get_ldap_user_info_row($acs_user_info_row['user_id']);
				$acs_user_info_row['user_name'] = $ldap_user_info_row['user_name'];
			}

			// フレンズIDの配列を取得する
			$acs_user_info_row['friends_id_array'] = ACSUser::get_friends_id_array($acs_user_info_row['user_community_id']);

			// 各機能ごとで必要な権限を判別・設定する
			// マイページ所有者、コミュニティ管理者、メンバの設定など
			if ($this->moduleName == 'User') {
				$id = $request->getParameter('id');
				if (!$id) {
					$id = $acs_user_info_row['user_community_id'];
				}

				// マイページ所有者かどうか
				if ($acs_user_info_row['user_community_id'] == $request->getParameter('id')) {
					$user->addCredential('USER_PAGE_OWNER');

				// 友人かどうか
				} elseif (!ACSUser::is_friends($id, $acs_user_info_row['user_community_id'])) {
					$user->addCredential('NOT_FRIENDS');
				}

			} elseif ($this->moduleName == 'Community') {
				$community_id = $request->getParameter('community_id');

				// コミュニティIDの指定がある場合のみ、権限設定を行う
				if ($community_id) {
					$is_community_member = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $community_id);
					$is_community_admin = ACSCommunity::is_community_admin($acs_user_info_row['user_community_id'], $community_id);

					// コミュニティメンバかどうか
					if ($is_community_member) {
						$user->addCredential('COMMUNITY_MEMBER');

						// コミュニティ管理者かどうか
						if ($is_community_admin) {
							$user->addCredential('COMMUNITY_ADMIN');
						}

					// コミュニティメンバではない
					} else {
						$user->addCredential('NOT_COMMUNITY_MEMBER');
					}
				}
			}
			$user->setAttribute('login_user_id', $user_id);

		} else {
			$acs_user_info_row['user_name'] = ACS_PUBLIC_USER_NAME;
			$acs_user_info_row['user_community_id'] = ACS_PUBLIC_USER_COMMUNITY_ID;
			$acs_user_info_row['is_acs_user'] = false;
			$acs_user_info_row['is_login_user'] = false;

			// 権限設定
			$user->addCredential('PUBLIC_USER');
		}

		$user->setAttribute('acs_user_info_row', $acs_user_info_row);

		// ログイン情報作成
		if ($justLogin) {
			ACSUser::set_login_date($user);
		}
		// アクセス毎のログアウト時間更新
		$getLogoutDateEverytime = $user->getAttribute('getLogoutDateEverytime');
		if ($getLogoutDateEverytime != NULL && $getLogoutDateEverytime == "1") {
			ACSUser::acs_login_date($user);
		}

		if ($acs_user_info_row['is_acs_user'] && $acs_user_info_row['open_level_name'] 
				== ACSMsg::get_mst('open_level_master','D01')) {
		// OK
		} elseif ($acs_user_info_row['is_acs_user'] && 
				$acs_user_info_row['open_level_name'] == ACSMsg::get_mst('open_level_master','D03') 
				  || (!$acs_user_info_row['is_acs_user'] && $acs_user_info_row['is_ldap_user'])) {
			// マイページ全体が非公開のユーザ or 未登録のLDAPユーザ

			// マイページのプロフィール編集可能な権限を付与する
			$user->addCredential('USER_PAGE_OWNER');

			if ($this->moduleName == DEFAULT_MODULE && ($this->actionName == 'EditProfile' || $this->actionName == 'SetOpenLevelForProfile')) {
				// 未登録のLDAPユーザの場合、プロフィール設定画面へのアクセスを許可
			} else {
				$edit_profile_url = $this->getControllerPath(DEFAULT_MODULE, 'EditProfile');
				header("Location: $edit_profile_url");
			}
		} elseif (!$acs_user_info_row['is_acs_user'] && $acs_user_info_row['is_login_user'] && !$acs_user_info_row['is_ldap_user']) {
			echo "Forbidden";
			exit;
		}

		// form で enctype="multipart/form-data" の指定が合った場合の対処
		// エンコーディングを変換する
		if (count($_FILES) && !ini_get('mbstring.encoding_translation')) {
			$request->params = ACSLib::convert_post_data_encoding($request->params);
		}

		// 共通アクセス制御 //
		$access_control_info = $this->get_access_control_info($controller, $request, $user);
		$valid_flag = true;
		if ($access_control_info) {
			$valid_flag = false;

			if ($access_control_info['role_array'] && $access_control_info['contents_row_array']) {
				foreach ($access_control_info['contents_row_array'] as $contents_row) {
					if ($contents_row['community_type_name'] == ACSMsg::get_mst('community_type_master','D40')) {
						if (ACSAccessControl::is_valid_user_for_community($acs_user_info_row, $access_control_info['role_array'], $contents_row)) {
							$valid_flag = true;
						} else {
							$valid_flag = false;
							break;
						}
					} elseif ($contents_row['community_type_name'] == ACSMsg::get_mst('community_type_master','D10')) {
						if (ACSAccessControl::is_valid_user_for_user_community($acs_user_info_row, $access_control_info['role_array'], $contents_row)) {
							$valid_flag = true;
						} else {
							$valid_flag = false;
							break;
						}
					}
				}
			}
		}
		if (!$valid_flag) {
			$this->controller->forward(SECURE_MODULE, SECURE_ACTION);
			exit;
		}

		// 各機能固有の権限判別を取得
		if ($this->get_execute_privilege($controller, $request, $user)) {
			$user->addCredential('EXECUTE');
		}

		//return parent::initialize($controller);
		return true;
	}
	
	/**
	 * デフォルトフォームを取得する
	 * @access public
	 * @return HTML_QuickForm
	 */
	public function createDefaultForm()
	{
		return $this->createForm();
	}

	/**
	 * フォームを作成する
	 * ※フォーム属性を追加する場合、setFormAttributesを先に実行し、追加するフォーム属性をセットしておくこと
	 * @access public
	 * @param String $moduleName モジュール名
	 * @param String $actionName アクション名
	 * @param String $formName フォーム名
	 * @param Array $parameters アクションパラメータ
	 * @param string $fragment フラグメント
	 * @param boolean $secure セキュアフラグ(HTTPS）
	 * @return HTML_QuickForm
	 */
	public function createForm($moduleName = '', $actionName = '', $formName = '', $parameters = array(), 
		$fragment = '', $secure = false)
	{
		/* 初期値をセットする */
		if (empty($moduleName)) {
			$moduleName = $this->moduleName;
		}
		if (empty($actionName)) {
			$actionName = $this->actionName;
		}
		if (empty($formName)) {
			$formName = $this->createFormRegKey();
		}

		/* URLを作成する */
		if (!is_array($parameters)) {
			throw new ApplicationException('Invalid Parameter! $parameters');
		}
		if (!is_string($fragment)) {
			throw new ApplicationException('Invalid Parameter! $fragment');
		}
		$parameters['module'] = $moduleName;
		$parameters['action'] = $actionName;
		
		$actionURL = $this->controller->genURL(null, $parameters);
		$actionURL .= $fragment;
		
		/* フォームを作成する */
		$form = new HTML_QuickForm($formName, $this->formMethod, $actionURL, 
			$this->formTarget, $this->formAttributes, $this->formTrackSubmit);
		
		/* フォームをrequestオブジェクトにセットする */
		array_push($this->formList, $formName);
		$this->request->setAttribute($formName, $form);

		return $form;
	}
	
	/**
	 * デフォルトフォームを取得する
	 * @access public
	 * @return HTML_QuickForm
	 */
	public function getDefaultForm()
	{
		if (0 >= count($this->formList)) {
			throw new ApplicationException('No data form');
		}
		
		return $this->getForm('form1');
	}

	/**
	 * フォームを取得する
	 * @access public
	 * @return HTML_QuickForm
	 */
	public function getForm($formName = '')
	{
		if (!$this->request->hasAttribute($formName)) {
			throw new ApplicationException("No data form! form=$formName");
		}
		
		return $this->request->getAttribute($formName);
	}

	/**
	 * フォーム登録キーを作成する
	 * @access public
	 * @return string フォーム登録キー
	 */
	public function createFormRegKey()
	{
		$formRegKey = $this->formRegKey;
		$this->formRegKey += 1;

		return $formRegKey;
	}

	/**
	 * フォーム属性値をセットする
	 * ※createFormを呼び出す前に実施すること
	 * @access public
	 * @param string /array $formAttributes フォーム属性
	 */
	public function setFormAttributes($formAttributes)
	{
		$this->formAttributes = $formAttributes;
	}
	
	/**
	 * 画面メッセージをセットする
	 * @access public
	 * @param String $message メッセージ
	 */
	public function setMessages($message)
	{
		$messages = array();
		if ($this->request->hasAttribute('messages')) {
			$messages = $this->request->getAttribute('messages');
		}
		array_push($messages, $message);
		$this->request->setAttribute('messages', $messages);

	}

	/**
	 * エラーメッセージをセットする
	 * @access public
	 * @param String $message メッセージ
	 * @param string $name HTML_QuickFormの要素名
	 */
	public function setErrMessages($message, $name = null)
	{
		if (empty($name)) {
			$name = $this->errRegKey;
			$this->errRegKey += 1;
		}
		$this->request->setError($name, $message);
	}
	
	/**
	 * 共通メッセージを取得する
	 * パラメータを引き渡すと、メッセージの%sにパラメータをセットし返却します。
	 * @access public
	 * @param string $id メッセージID
	 * @param object $args 共通メッセージのパラメータ(配列または、変数)
	 * @return string 共通メッセージ
	 */
	public function getMessage($id, $args = null)
	{
		/* メッセージ取得 */
		$msg = $this->messages->getParameter($id);
		if (empty($msg)) {
			throw new ApplicationException("Not found message! id=$id");
		}
		
		/* パラメータセット */
		if (is_array($args)) {
			$msg = vsprintf($msg, $args);
		} else {
			$msg = sprintf($msg, $args);
		}
		
		return $msg;
	}

	/**
	 * バリデーション項目を登録する
	 * バリデーションマネージャに、バリデーション項目を登録する
	 * @access public
	 * @param object $validatorManager バリデータマネージャ
	 * @param string $name バリデーション項目名
	 * @param boolean $required 必須項目フラグ(true:必須項目、false:入力項目)
	 * @param string $message 必須エラーメッセージ
	 */
	public function regValidateName($validatorManager, $name, $required = false, $message = null)
	{
		$validatorManager->registerName($name, $required, $message);
	}
	
	/**
	 * バリデーション項目に入力チェックを設定(追加登録)する
	 * バリデーション項目に対して、入力チェックを設定(追加登録)する
	 * @access public
	 * @param object $validatorManager バリデータマネージャ
	 * @param string $name バリデーション項目名
	 * @param string $class 入力チェッククラス名(バリデータタイプ)
	 * @param array $params 入力チェックパラメータ
	 */
	public function setValidator($validatorManager, $name, $class, $params)
	{
		$validator = new $class;
		$validator->initialize($this->getContext(), $params);
		$validatorManager->registerValidator($name, $validator);
	}

	/**
	 * 実行権限を取得する
	 * アクションを実行するのに必要な権限を設定する
	 * @return int 実行権限
	 */
	public function getCredential()
	{
		return S4_AUTH_NOBODY;
	}

	/**
	 * 認証チェックを行うか
	 * アクションを実行する前に、認証チェックが必要か設定する
	 * @access  public
	 * @return  boolean 認証チェック有無（true:必要、false:不要）
	 */
	public function isSecure()
	{
		return true;
	}
	
	/**
	 * システムで使用するファイル名を作成する
	 * アップロードファイルなどに使用するファイル名を作成する
	 * @access  public
	 * @param string $path  ファイルパス
	 * @return string ファイル名
	 */
	public function createFilename()
	{
		/* ファイル名を作成する */
		list($usec, $sec) = explode(" ", microtime());
		$filename = sprintf("%s%d", date('YmdHis', $sec), $usec * 1000);
		usleep(1); //ファイル名が重複しないように１マイクロ秒処理を止める

		return  $filename;
	}
	
	/**
	 * ファイル拡張子を取得する
	 * ・ファイル情報からファイル拡張子を取得する
	 * （自動判定するファイル拡張子：jpg, gif, png, zip, xls, pdf, doc, ppt, lzh）
	 * ・ファイルタイプが自動判定されなかった場合は、ファイル名から拡張子を取り出す
	 * ※ファイル保存されているファイルの拡張子を取得する場合に使用する
	 * @access  public
	 * @param string $path  ファイルパス
	 * @return string ファイル拡張子
	 */
	public function getExtension($path)
	{
		// パラメータチェック
		if (empty($path)) {
			throw new ApplicationException('Invalid parameter! $path=' . $path);
		}
		$realpath = realpath($path);
		if ($path === false) {
			throw new ApplicationException('Invalid parameter! $path=' . $path);
		} elseif (strncmp($path, $realpath, strlen($realpath)) !== 0) {
			throw new ApplicationException('Invalid parameter! $path=' . $path);
		}
		
		// 拡張子自動判定
		$mime_type = MIME_Type::autoDetect($path);
		$ext = $this->getExtentionEx($mime_type, $path);
		$temp = array();
		if (empty($ext)) {
			// ファイル名から拡張子を取得する
			preg_match("/^(.*)\.(.*)$/i", $path, $temp); 
			$ext = $temp[2];
		}
		
		return $ext;
	}
	
	/**
	 * ファイル拡張子を取得する
	 * MIMEタイプとファイル情報からファイル拡張子を取得する
	 * （自動判定するファイル拡張子：jpg, gif, png, zip, xls, pdf, doc, ppt, lzh）
	 * ※ファイルアップロードされた一時ファイルの拡張子を取得する場合に使用する
	 * @access  public
	 * @param string $path ファイルパス
	 * @param string $mime_type MIMEタイプ
	 * @return string ファイル拡張子
	 */
	public function getExtentionEx($path, $mime_type)
	{
		// パラメータチェック
		if (empty($path)) {
			throw new ApplicationException('Invalid parameter! $path=' . $path);
		}
		$realpath = realpath($path);
		if ($path === false) {
			throw new ApplicationException('Invalid parameter! $path=' . $path);
		} elseif (strncmp($path, $realpath, strlen($realpath)) !== 0) {
			throw new ApplicationException('Invalid parameter! $path=' . $path);
		}

		switch ($mime_type) {
			// jpg
			case 'image/jpg':
			case 'image/jpeg':
			case 'image/pjpg':
			case 'image/pjpeg':
				$ext = 'jpg';
				break;
			// gif
			case 'image/gif':
				$ext = 'gif';
				break;
			// png
			case 'image/png':
			case 'image/x-png':
				$ext = 'png';
				break;
			//zip
			case 'application/x-zip-compressed':
				$ext = 'zip';
				break;
			//excel
			case 'application/vnd.ms-excel':
				$ext = 'xls';
				break;
			//pdf	
			case 'application/pdf':
				$ext = 'pdf';
				break;
			//word
			case 'application/msword':
				$ext = 'doc';
				break;
			//ppt
			case 'application/vnd.ms-powerpoint':
				$ext = 'ppt';
				break;
			//lzh
			case 'application/octet-stream':
				$aryTemp = explode('.', $path);
				if (is_array($aryTemp) && strtolower($aryTemp[ count($aryTemp)-1 ])=='lzh' ) {
					$ext = 'lzh';
				}else{
					$ext = $aryTemp[1];
				}
				break;
			default :
				$ext = '';
				break;
		}
		
		return $ext;
		
	}
	
	/**
	 * ファイル表示キーを作成する
	 * ファイルパスをセッションに格納し、ファイル表示キーを取得する
	 * @access  public
	 * @param string $path  ファイルパス
	 * @param boolean $download  ファイルダウンロードするか（デフォルト：false）
	 * @param string $fileName ファイル名（デフォルト：物理ファイル名）
	 * @return string ファイル表示キー
	 */
	public function createDispFilekey($path, $download = false, $fileName = null)
	{
		$key = sha1($path);
		
		// ファイル名がセットされていない場合、物理ファイル名をセットする
		if (empty($fileName)) {
			$fileName = basename($path);
		}
		
		/* ファイルパスとファイルダウンロードフラグとファイル名をセッションに格納する */
		$this->user->setAttribute($key, array($path, $download, $fileName));

		return  $key;
	}
	
	/**
	 * ファイルパスを取得する
	 * ファイルキーに紐付けられたファイルパスを取得する
	 * @access  public
	 * @param string $key ファイル表示キー
	 * @return string ファイルパス
	 */
	public function getDispFilepath($key)
	{
		return $this->user->getAttribute($key);
	}
	
	/**
	 * ファイル表示リンクを作成する
	 * ファイルパスをセットし、ファイル表示リンクを取得する。
	 * ※ファイルダウンロードをする場合は、第二引数（$download）にtrueをセットすること
	 * @access  public
	 * @param string $pathファイル表示キー
	 * @param boolean $download  ファイルダウンロードするか（デフォルト：false）
	 * @param string $moduleNameモジュール名
	 * @param string $actionName アクション名
	 * @param string $fileName ファイル名（デフォルト：物理ファイル名）
	 * @return string ファイル表示リンク
	 */
	public function createDispFilelink($path, $download = false, $moduleName = 'Default', $actionName = 'DispFile', 
		$fileName = null)
	{
		/* 入力値をチェックする */
		if (empty($path)) {
			throw new ApplicationException('Invalid parameter! $path is null');
		}
		if (empty($moduleName)) {
			throw new ApplicationException('Invalid parameter! $moduleName is null');
		}
		if (empty($actionName)) {
			throw new ApplicationException('Invalid parameter! $actionName is null');
		}

		// ファイル名がセットされていない場合、物理ファイル名をセットする
		if (empty($fileName)) {
			$fileName = basename($path);
		}
		
		/* URLを作成する */
		$key = $this->createDispFilekey($path, $download, $fileName);
		$parameters = array('dispkey'=>$key);
		
		$parameters['module'] = $moduleName;
		$parameters['action'] = $actionName;
		
		$actionURL = $this->controller->genURL(null, $parameters);
		
		return $actionURL;
	}
	
	/**
	 * モジュール名を取得する
	 * @access  public
	 * @return string モジュール名
	 */
	public function getModuleName() {
		return $this->moduleName;
	}
	
	/**
	 * アクション名を取得する
	 * @access  public
	 * @return string モジュール名
	 */
	public function getActionName() {
		return $this->actionName;
	}
	
	/**
	 * リクエスト定義していない場合に実行する
	 * （想定しないリクエストが届いた場合、セキュリティ上、すべてエラーとして扱う）
	 * @throws ApplicationException 想定しないリクエストが行われた時
	 */
	public function getDefaultView() {
		throw new ApplicationException('送信されたデータは無効です。正しく処理を行ってください。');
	}

	/**
	 * 個人情報アクセスのログを記録する.
	 * 記録内容は、日時、ログインID、IPアドレス、件数、検索条件、データアクセスした個人情報の主キーとする。
	 * ・メッセージには、データアクセスに使用した検索条件などをセットする。
	 * ・個人情報の主キーには、DBから取得した個人情報の主キー（配列もしくは文字列）をセットする。
	 * ・日時、ログインID、IPアドレス、件数は、フレームワークにて自動セットする。
	 * 
	 * @access  public
	 * @param string $message メッセージ （検索条件などをセットする）
	 * @param mixed $data 個人情報の主キー（string/arrayの両方がセット可能）
	 * @param string $class クラス名
	 * @param string $function ファンクション名
	 * @param string $file プログラムファイル名
	 * @param string $line プログラムライン番号
	 */
	public function recPersonalAccessLog($message = null, $data = null, 
		$class = __CLASS__, $function = __FUNCTION__, $file = __FILE__, $line = __LINE__) 
	{
		// ロガーを取得する
		$logger = $this->controller->getLogger();
		
		// メッセージを作成する
		if (!is_null($data)) {
			if (is_array($data)) {
				$data = "\n" . implode("\n", $data);
			} else {
				$data = "\n" . $data;
			}
		}
		
		$msg = new Message(array('m' => $message,
								  'c' => $class,
								  'F' => $function,
								  'f' => $file,
								  'l' => $line,
								  'N' => 'P_INFO',
								  'p' => Logger::P_INFO,
								  'ip_address' => $_SERVER['REMOTE_ADDR'],
								  'data' => $data));
		
		// ログを出力する
		$logger->log($msg);
	}

	/*---------- ACS Original method ----------*/

	/**
	 * 各機能固有の権限判別取得
	 *   固有のものがある場合のみ、オーバーライドすること
	 */
	function get_execute_privilege (&$controller, &$request, &$user) {
		return false;
	}

	/**
	 * エラー情報セット
	 *
	 * @param  &$controller
	 * @param  &$request
	 * @param  &$user
	 * @param  $error_item
	 * @param  $error_message
	 * @return handleError の結果
	 */
	function setError (&$controller, &$request, &$user, $error_item, $error_message) {
		$request->setError($error_item, $error_message);

		return $this->handleError();
		//return $this->handleError(&$controller, &$request, &$user);
	}

	/**
	 * エラー情報をセッションにセット
	 * handleError 関数にて header() で画面遷移する場合呼ばれる
	 *
	 * @param &$controller
	 * @param &$request
	 * @param &$user
	 */
	function sendError (&$controller, &$request, &$user) {
		$user->setAttribute('error_row' , $request->getErrors());
	}

	/**
	 * エラー情報があるかどうか
	 *
	 * @param  &$controller
	 * @param  &$request
	 * @param  &$user
	 * @return true / false
	 */
	function hasErrors (&$controller, &$request, &$user) {
		// PHP5対応 2009.03.02 
		if ($user->getAttribute('error_row') == NULL || $request->getErrors() == NULL) {
			return false;
		}
		
		$error_row_array = array_merge($user->getAttribute('error_row'), $request->getErrors());
		if (count($error_row_array) > 0) {
			return true;
		} else {
			return false;
		}
	}

	// アクセス制御情報
	function get_access_control_info(&$controller, &$request, &$user) {
		$access_control_info = array();
		return $access_control_info;
	}
	
	function getControllerPath($module, $action) {
		$index = "index.php?";
		$moduleNm = "module=";
		$actionNm = "&action=";
		return $index . $moduleNm . $module . $actionNm . $action;

	}
}
?>
