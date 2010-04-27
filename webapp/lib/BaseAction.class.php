<?php
ini_set("display_errors", 0);
ini_set("error_reporting", false);
ini_restore("error_reporting");
ini_restore("display_errors");

/**
 * Action�γ�ĥ���饹
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
	 * Controller���֥������� 
	 * @var WebController
	 */
	protected $controller = '';
	
	/**
	 * Request���֥�������
	 * @var WebRequest
	 */
	protected $request = '';
	
	/* �ե�������Ͽ���� */
	private $formRegKey = 'form1';

	/* �ե�������Ͽ�ꥹ�� */
	private $formList = array();
	
	/* ���顼��Ͽ���� */
	private $errRegKey = 'error1';
	
	/* user���֥������� */
	private $user = '';
	
	/* HTTP�ꥯ�����ȥ����� */
	private $formMethod = 'POST';

	/* HTTP�ꥯ�����ȥ������å� */
	private $formTarget = '';

	/* FORM°�� */
	private $formAttributes = '';

	/* FROM���������å� */
	private $formTrackSubmit = false;
	
	/* ����������Υ⥸�塼��̾ */
	private $moduleName = '';

	/* ����������Υ��������̾ */
	private $actionName = '';

	/* ��å������ǡ��� */
	private $messages = '';

	/**
	 * �������
	 * @access public
	 * @param Object $context context
	 * @return boolean �������
	 */
	public function initialize ($context)
	{
		parent::initialize($context);

		// ���������ζ��̽������������
		/* ����ͤ򥻥åȤ��� */
		$this->controller =$context->getController();
		$this->request = $context->getRequest();
		$this->user = $context->getUser();

		$this->moduleName = $context->getModuleName();
		$this->actionName = $context->getActionName();
		$this->messages = &CommonMessages::getInstance();

		/* request���֥������Ȥ˥ե�������Ͽ�ꥹ�Ȥ򥻥åȤ��� */
		$this->request->setAttributeByRef('formList', $this->formList);
		
		$request = &$context->getRequest();
		$user = &$context->getUser();
		
		// ���å���󤫤�桼��ID�������Ǥ�����POST�ǥ桼��ID��������ǽ�ʾ��
		$user_id = $user->getAttribute('login_user_id');
		$justLogin = false;
		if (($user_id == NULL || $user_id == "") 
				&& ($_POST['userid'] != NULL && $_POST['userid'] != "")) {
			$input_user_id = $_POST['userid'];
			$input_passwd = $_POST['passwd'];

			// ���顼�����å���.htpasswd��LDAP�ν��
			$user_id = ACSSystem::check_passwd($input_user_id, $input_passwd);
			if ($user_id) {
				$justLogin = true;
				$getLogoutDateEverytime = ACSSystemConfig::get_keyword_value(
						ACSMsg::get_mst('system_config_group','D08'), 'GET_LOGOUT_DATE_EVERYTIME');		
				$user->setAttribute('getLogoutDateEverytime', $getLogoutDateEverytime);
			}

		}
		
		// ��������μ¹�
		if ($request->getparameter('acsmsg')) {
			ACSMsg::set_lang($request->getparameter('acsmsg'));
			ACSMsg::set_lang_cookie($request->getparameter('acsmsg'));
		}
	
		// ���¥��ꥢ
		$user->clearCredentials();

		// ǧ�ںѤߤ���Ͽ
		$user->setAuthenticated(true);

		// $acs_user_info_row�����ꤹ�� //
		$acs_user_info_row = array();

		if ($user_id) {
			$acs_user_info_row = ACSUser::get_user_info_row_by_user_id($user_id);
			
			// �桼������̵��
			if ($user_id && !$acs_user_info_row['user_id']) {
				$acs_user_info_row['user_id'] = $user_id;
				$acs_user_info_row['user_community_id'] = ACS_PUBLIC_USER_COMMUNITY_ID;
				$acs_user_info_row['is_acs_user'] = false;
			} else {
				$acs_user_info_row['is_acs_user'] = true;

				// ������Ͽ
				$user->addCredential('ACS_USER');
			}

			// ������桼��(ǧ�ڤ��̲ᤷ���桼��)���ɤ���
			$acs_user_info_row['is_login_user'] = true;

			// �����ƥ�����Ԥ��ɤ���
			if ($acs_user_info_row['administrator_flag'] == 't') {
				// ������Ͽ
				$user->addCredential('SYSTEM_ADMIN_USER');
			}

			// LDAP�桼�����ɤ��� (�ե�����ǧ�ڥ桼���Ǥʤ����LDAP�桼���Ȥߤʤ�)
			$acs_user_info_row['is_ldap_user'] = !ACSSystem::is_htpasswd_user($user_id);
			// LDAPǧ�ڰʳ��ξ�硢�ѥ�����ѹ����¤���Ϳ
			if ($acs_user_info_row['is_ldap_user']) {
				$user->addCredential('LDAP_USER');
			} else {
				$user->addCredential('NOT_LDAP_USER');
			}

			// ̤��Ͽ��LDAP�桼���ξ��ϻ�̾��Ĵ�٤�
			if (!$acs_user_info_row['is_acs_user'] && $acs_user_info_row['is_ldap_user']) {
				$ldap_user_info_row = ACSLDAP::get_ldap_user_info_row($acs_user_info_row['user_id']);
				$acs_user_info_row['user_name'] = $ldap_user_info_row['user_name'];
			}

			// �ե��ID��������������
			$acs_user_info_row['friends_id_array'] = ACSUser::get_friends_id_array($acs_user_info_row['user_community_id']);

			// �Ƶ�ǽ���Ȥ�ɬ�פʸ��¤�Ƚ�̡����ꤹ��
			// �ޥ��ڡ�����ͭ�ԡ����ߥ�˥ƥ������ԡ����Ф�����ʤ�
			if ($this->moduleName == 'User') {
				$id = $request->getParameter('id');
				if (!$id) {
					$id = $acs_user_info_row['user_community_id'];
				}

				// �ޥ��ڡ�����ͭ�Ԥ��ɤ���
				if ($acs_user_info_row['user_community_id'] == $request->getParameter('id')) {
					$user->addCredential('USER_PAGE_OWNER');

				// ͧ�ͤ��ɤ���
				} elseif (!ACSUser::is_friends($id, $acs_user_info_row['user_community_id'])) {
					$user->addCredential('NOT_FRIENDS');
				}

			} elseif ($this->moduleName == 'Community') {
				$community_id = $request->getParameter('community_id');

				// ���ߥ�˥ƥ�ID�λ��꤬������Τߡ����������Ԥ�
				if ($community_id) {
					$is_community_member = ACSCommunity::is_community_member($acs_user_info_row['user_community_id'], $community_id);
					$is_community_admin = ACSCommunity::is_community_admin($acs_user_info_row['user_community_id'], $community_id);

					// ���ߥ�˥ƥ����Ф��ɤ���
					if ($is_community_member) {
						$user->addCredential('COMMUNITY_MEMBER');

						// ���ߥ�˥ƥ������Ԥ��ɤ���
						if ($is_community_admin) {
							$user->addCredential('COMMUNITY_ADMIN');
						}

					// ���ߥ�˥ƥ����ФǤϤʤ�
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

			// ��������
			$user->addCredential('PUBLIC_USER');
		}

		$user->setAttribute('acs_user_info_row', $acs_user_info_row);

		// ������������
		if ($justLogin) {
			ACSUser::set_login_date($user);
		}
		// ����������Υ������Ȼ��ֹ���
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
			// �ޥ��ڡ������Τ�������Υ桼�� or ̤��Ͽ��LDAP�桼��

			// �ޥ��ڡ����Υץ�ե������Խ���ǽ�ʸ��¤���Ϳ����
			$user->addCredential('USER_PAGE_OWNER');

			if ($this->moduleName == DEFAULT_MODULE && ($this->actionName == 'EditProfile' || $this->actionName == 'SetOpenLevelForProfile')) {
				// ̤��Ͽ��LDAP�桼���ξ�硢�ץ�ե�����������̤ؤΥ������������
			} else {
				$edit_profile_url = $this->getControllerPath(DEFAULT_MODULE, 'EditProfile');
				header("Location: $edit_profile_url");
			}
		} elseif (!$acs_user_info_row['is_acs_user'] && $acs_user_info_row['is_login_user'] && !$acs_user_info_row['is_ldap_user']) {
			echo "Forbidden";
			exit;
		}

		// form �� enctype="multipart/form-data" �λ��꤬��ä������н�
		// ���󥳡��ǥ��󥰤��Ѵ�����
		if (count($_FILES) && !ini_get('mbstring.encoding_translation')) {
			$request->params = ACSLib::convert_post_data_encoding($request->params);
		}

		// ���̥����������� //
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

		// �Ƶ�ǽ��ͭ�θ���Ƚ�̤����
		if ($this->get_execute_privilege($controller, $request, $user)) {
			$user->addCredential('EXECUTE');
		}

		//return parent::initialize($controller);
		return true;
	}
	
	/**
	 * �ǥե���ȥե�������������
	 * @access public
	 * @return HTML_QuickForm
	 */
	public function createDefaultForm()
	{
		return $this->createForm();
	}

	/**
	 * �ե�������������
	 * ���ե�����°�����ɲä����硢setFormAttributes����˼¹Ԥ����ɲä���ե�����°���򥻥åȤ��Ƥ�������
	 * @access public
	 * @param String $moduleName �⥸�塼��̾
	 * @param String $actionName ���������̾
	 * @param String $formName �ե�����̾
	 * @param Array $parameters ���������ѥ�᡼��
	 * @param string $fragment �ե饰����
	 * @param boolean $secure �����奢�ե饰(HTTPS��
	 * @return HTML_QuickForm
	 */
	public function createForm($moduleName = '', $actionName = '', $formName = '', $parameters = array(), 
		$fragment = '', $secure = false)
	{
		/* ����ͤ򥻥åȤ��� */
		if (empty($moduleName)) {
			$moduleName = $this->moduleName;
		}
		if (empty($actionName)) {
			$actionName = $this->actionName;
		}
		if (empty($formName)) {
			$formName = $this->createFormRegKey();
		}

		/* URL��������� */
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
		
		/* �ե������������� */
		$form = new HTML_QuickForm($formName, $this->formMethod, $actionURL, 
			$this->formTarget, $this->formAttributes, $this->formTrackSubmit);
		
		/* �ե������request���֥������Ȥ˥��åȤ��� */
		array_push($this->formList, $formName);
		$this->request->setAttribute($formName, $form);

		return $form;
	}
	
	/**
	 * �ǥե���ȥե�������������
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
	 * �ե�������������
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
	 * �ե�������Ͽ�������������
	 * @access public
	 * @return string �ե�������Ͽ����
	 */
	public function createFormRegKey()
	{
		$formRegKey = $this->formRegKey;
		$this->formRegKey += 1;

		return $formRegKey;
	}

	/**
	 * �ե�����°���ͤ򥻥åȤ���
	 * ��createForm��ƤӽФ����˼»ܤ��뤳��
	 * @access public
	 * @param string /array $formAttributes �ե�����°��
	 */
	public function setFormAttributes($formAttributes)
	{
		$this->formAttributes = $formAttributes;
	}
	
	/**
	 * ���̥�å������򥻥åȤ���
	 * @access public
	 * @param String $message ��å�����
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
	 * ���顼��å������򥻥åȤ���
	 * @access public
	 * @param String $message ��å�����
	 * @param string $name HTML_QuickForm������̾
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
	 * ���̥�å��������������
	 * �ѥ�᡼��������Ϥ��ȡ���å�������%s�˥ѥ�᡼���򥻥åȤ��ֵѤ��ޤ���
	 * @access public
	 * @param string $id ��å�����ID
	 * @param object $args ���̥�å������Υѥ�᡼��(����ޤ��ϡ��ѿ�)
	 * @return string ���̥�å�����
	 */
	public function getMessage($id, $args = null)
	{
		/* ��å��������� */
		$msg = $this->messages->getParameter($id);
		if (empty($msg)) {
			throw new ApplicationException("Not found message! id=$id");
		}
		
		/* �ѥ�᡼�����å� */
		if (is_array($args)) {
			$msg = vsprintf($msg, $args);
		} else {
			$msg = sprintf($msg, $args);
		}
		
		return $msg;
	}

	/**
	 * �Х�ǡ��������ܤ���Ͽ����
	 * �Х�ǡ������ޥ͡�����ˡ��Х�ǡ��������ܤ���Ͽ����
	 * @access public
	 * @param object $validatorManager �Х�ǡ����ޥ͡�����
	 * @param string $name �Х�ǡ���������̾
	 * @param boolean $required ɬ�ܹ��ܥե饰(true:ɬ�ܹ��ܡ�false:���Ϲ���)
	 * @param string $message ɬ�ܥ��顼��å�����
	 */
	public function regValidateName($validatorManager, $name, $required = false, $message = null)
	{
		$validatorManager->registerName($name, $required, $message);
	}
	
	/**
	 * �Х�ǡ��������ܤ����ϥ����å�������(�ɲ���Ͽ)����
	 * �Х�ǡ��������ܤ��Ф��ơ����ϥ����å�������(�ɲ���Ͽ)����
	 * @access public
	 * @param object $validatorManager �Х�ǡ����ޥ͡�����
	 * @param string $name �Х�ǡ���������̾
	 * @param string $class ���ϥ����å����饹̾(�Х�ǡ���������)
	 * @param array $params ���ϥ����å��ѥ�᡼��
	 */
	public function setValidator($validatorManager, $name, $class, $params)
	{
		$validator = new $class;
		$validator->initialize($this->getContext(), $params);
		$validatorManager->registerValidator($name, $validator);
	}

	/**
	 * �¹Ը��¤��������
	 * ����������¹Ԥ���Τ�ɬ�פʸ��¤����ꤹ��
	 * @return int �¹Ը���
	 */
	public function getCredential()
	{
		return S4_AUTH_NOBODY;
	}

	/**
	 * ǧ�ڥ����å���Ԥ���
	 * ����������¹Ԥ������ˡ�ǧ�ڥ����å���ɬ�פ����ꤹ��
	 * @access  public
	 * @return  boolean ǧ�ڥ����å�̵ͭ��true:ɬ�ס�false:���ס�
	 */
	public function isSecure()
	{
		return true;
	}
	
	/**
	 * �����ƥ�ǻ��Ѥ���ե�����̾���������
	 * ���åץ��ɥե�����ʤɤ˻��Ѥ���ե�����̾���������
	 * @access  public
	 * @param string $path  �ե�����ѥ�
	 * @return string �ե�����̾
	 */
	public function createFilename()
	{
		/* �ե�����̾��������� */
		list($usec, $sec) = explode(" ", microtime());
		$filename = sprintf("%s%d", date('YmdHis', $sec), $usec * 1000);
		usleep(1); //�ե�����̾����ʣ���ʤ��褦�ˣ��ޥ������ý�����ߤ��

		return  $filename;
	}
	
	/**
	 * �ե������ĥ�Ҥ��������
	 * ���ե�������󤫤�ե������ĥ�Ҥ��������
	 * �ʼ�ưȽ�ꤹ��ե������ĥ�ҡ�jpg, gif, png, zip, xls, pdf, doc, ppt, lzh��
	 * ���ե����륿���פ���ưȽ�ꤵ��ʤ��ä����ϡ��ե�����̾�����ĥ�Ҥ���Ф�
	 * ���ե�������¸����Ƥ���ե�����γ�ĥ�Ҥ����������˻��Ѥ���
	 * @access  public
	 * @param string $path  �ե�����ѥ�
	 * @return string �ե������ĥ��
	 */
	public function getExtension($path)
	{
		// �ѥ�᡼�������å�
		if (empty($path)) {
			throw new ApplicationException('Invalid parameter! $path=' . $path);
		}
		$realpath = realpath($path);
		if ($path === false) {
			throw new ApplicationException('Invalid parameter! $path=' . $path);
		} elseif (strncmp($path, $realpath, strlen($realpath)) !== 0) {
			throw new ApplicationException('Invalid parameter! $path=' . $path);
		}
		
		// ��ĥ�Ҽ�ưȽ��
		$mime_type = MIME_Type::autoDetect($path);
		$ext = $this->getExtentionEx($mime_type, $path);
		$temp = array();
		if (empty($ext)) {
			// �ե�����̾�����ĥ�Ҥ��������
			preg_match("/^(.*)\.(.*)$/i", $path, $temp); 
			$ext = $temp[2];
		}
		
		return $ext;
	}
	
	/**
	 * �ե������ĥ�Ҥ��������
	 * MIME�����פȥե�������󤫤�ե������ĥ�Ҥ��������
	 * �ʼ�ưȽ�ꤹ��ե������ĥ�ҡ�jpg, gif, png, zip, xls, pdf, doc, ppt, lzh��
	 * ���ե����륢�åץ��ɤ��줿����ե�����γ�ĥ�Ҥ����������˻��Ѥ���
	 * @access  public
	 * @param string $path �ե�����ѥ�
	 * @param string $mime_type MIME������
	 * @return string �ե������ĥ��
	 */
	public function getExtentionEx($path, $mime_type)
	{
		// �ѥ�᡼�������å�
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
	 * �ե�����ɽ���������������
	 * �ե�����ѥ��򥻥å����˳�Ǽ�����ե�����ɽ���������������
	 * @access  public
	 * @param string $path  �ե�����ѥ�
	 * @param boolean $download  �ե������������ɤ��뤫�ʥǥե���ȡ�false��
	 * @param string $fileName �ե�����̾�ʥǥե���ȡ�ʪ���ե�����̾��
	 * @return string �ե�����ɽ������
	 */
	public function createDispFilekey($path, $download = false, $fileName = null)
	{
		$key = sha1($path);
		
		// �ե�����̾�����åȤ���Ƥ��ʤ���硢ʪ���ե�����̾�򥻥åȤ���
		if (empty($fileName)) {
			$fileName = basename($path);
		}
		
		/* �ե�����ѥ��ȥե������������ɥե饰�ȥե�����̾�򥻥å����˳�Ǽ���� */
		$this->user->setAttribute($key, array($path, $download, $fileName));

		return  $key;
	}
	
	/**
	 * �ե�����ѥ����������
	 * �ե����륭����ɳ�դ���줿�ե�����ѥ����������
	 * @access  public
	 * @param string $key �ե�����ɽ������
	 * @return string �ե�����ѥ�
	 */
	public function getDispFilepath($key)
	{
		return $this->user->getAttribute($key);
	}
	
	/**
	 * �ե�����ɽ����󥯤��������
	 * �ե�����ѥ��򥻥åȤ����ե�����ɽ����󥯤�������롣
	 * ���ե������������ɤ򤹤���ϡ����������$download�ˤ�true�򥻥åȤ��뤳��
	 * @access  public
	 * @param string $path�ե�����ɽ������
	 * @param boolean $download  �ե������������ɤ��뤫�ʥǥե���ȡ�false��
	 * @param string $moduleName�⥸�塼��̾
	 * @param string $actionName ���������̾
	 * @param string $fileName �ե�����̾�ʥǥե���ȡ�ʪ���ե�����̾��
	 * @return string �ե�����ɽ�����
	 */
	public function createDispFilelink($path, $download = false, $moduleName = 'Default', $actionName = 'DispFile', 
		$fileName = null)
	{
		/* �����ͤ�����å����� */
		if (empty($path)) {
			throw new ApplicationException('Invalid parameter! $path is null');
		}
		if (empty($moduleName)) {
			throw new ApplicationException('Invalid parameter! $moduleName is null');
		}
		if (empty($actionName)) {
			throw new ApplicationException('Invalid parameter! $actionName is null');
		}

		// �ե�����̾�����åȤ���Ƥ��ʤ���硢ʪ���ե�����̾�򥻥åȤ���
		if (empty($fileName)) {
			$fileName = basename($path);
		}
		
		/* URL��������� */
		$key = $this->createDispFilekey($path, $download, $fileName);
		$parameters = array('dispkey'=>$key);
		
		$parameters['module'] = $moduleName;
		$parameters['action'] = $actionName;
		
		$actionURL = $this->controller->genURL(null, $parameters);
		
		return $actionURL;
	}
	
	/**
	 * �⥸�塼��̾���������
	 * @access  public
	 * @return string �⥸�塼��̾
	 */
	public function getModuleName() {
		return $this->moduleName;
	}
	
	/**
	 * ���������̾���������
	 * @access  public
	 * @return string �⥸�塼��̾
	 */
	public function getActionName() {
		return $this->actionName;
	}
	
	/**
	 * �ꥯ������������Ƥ��ʤ����˼¹Ԥ���
	 * �����ꤷ�ʤ��ꥯ�����Ȥ��Ϥ�����硢�������ƥ��塢���٤ƥ��顼�Ȥ��ư�����
	 * @throws ApplicationException ���ꤷ�ʤ��ꥯ�����Ȥ��Ԥ�줿��
	 */
	public function getDefaultView() {
		throw new ApplicationException('�������줿�ǡ�����̵���Ǥ���������������ԤäƤ���������');
	}

	/**
	 * �Ŀ;��󥢥������Υ���Ͽ����.
	 * ��Ͽ���Ƥϡ�������������ID��IP���ɥ쥹��������������ǡ����������������Ŀ;���μ祭���Ȥ��롣
	 * ����å������ˤϡ��ǡ������������˻��Ѥ����������ʤɤ򥻥åȤ��롣
	 * ���Ŀ;���μ祭���ˤϡ�DB������������Ŀ;���μ祭��������⤷����ʸ����ˤ򥻥åȤ��롣
	 * ��������������ID��IP���ɥ쥹������ϡ��ե졼�����ˤƼ�ư���åȤ��롣
	 * 
	 * @access  public
	 * @param string $message ��å����� �ʸ������ʤɤ򥻥åȤ����
	 * @param mixed $data �Ŀ;���μ祭����string/array��ξ�������åȲ�ǽ��
	 * @param string $class ���饹̾
	 * @param string $function �ե��󥯥����̾
	 * @param string $file �ץ����ե�����̾
	 * @param string $line �ץ����饤���ֹ�
	 */
	public function recPersonalAccessLog($message = null, $data = null, 
		$class = __CLASS__, $function = __FUNCTION__, $file = __FILE__, $line = __LINE__) 
	{
		// �������������
		$logger = $this->controller->getLogger();
		
		// ��å��������������
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
		
		// ������Ϥ���
		$logger->log($msg);
	}

	/*---------- ACS Original method ----------*/

	/**
	 * �Ƶ�ǽ��ͭ�θ���Ƚ�̼���
	 *   ��ͭ�Τ�Τ�������Τߡ������С��饤�ɤ��뤳��
	 */
	function get_execute_privilege (&$controller, &$request, &$user) {
		return false;
	}

	/**
	 * ���顼���󥻥å�
	 *
	 * @param  &$controller
	 * @param  &$request
	 * @param  &$user
	 * @param  $error_item
	 * @param  $error_message
	 * @return handleError �η��
	 */
	function setError (&$controller, &$request, &$user, $error_item, $error_message) {
		$request->setError($error_item, $error_message);

		return $this->handleError();
		//return $this->handleError(&$controller, &$request, &$user);
	}

	/**
	 * ���顼����򥻥å����˥��å�
	 * handleError �ؿ��ˤ� header() �ǲ������ܤ�����ƤФ��
	 *
	 * @param &$controller
	 * @param &$request
	 * @param &$user
	 */
	function sendError (&$controller, &$request, &$user) {
		$user->setAttribute('error_row' , $request->getErrors());
	}

	/**
	 * ���顼���󤬤��뤫�ɤ���
	 *
	 * @param  &$controller
	 * @param  &$request
	 * @param  &$user
	 * @return true / false
	 */
	function hasErrors (&$controller, &$request, &$user) {
		// PHP5�б� 2009.03.02 
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

	// ���������������
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
