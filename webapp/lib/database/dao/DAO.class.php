<?php
/**
 * DAO���֥������Ȥδ��쥯�饹
 * @access public
 * @package database/dao
 * @category DAO
 * @author Tsutomu Wakuda <wakuda@withit.co.jp>
 * @sourcefile
 *
 */
class DAO
{
	/**
	 * �ǡ����١������ͥ������
	 * @var DB_common
	 */
	protected $db = null;
	
    /**
     * ���󥹥ȥ饯��
     * @access public
     * @param Object $db �ǡ����١������ͥ������
     */
	function __construct($db)
	{
		$this->db = $db;
	}
	
    /**
     * SQLʸ��¹Ԥ�������ǡ������������(SELECT����)
     *	(�ǡ�������) array([0]=>array([0]=>data, ��������)
     * ���롼�׽����ʤɤǤϻ��Ѥ��ʤ���
     * ��SQL�β��Ϥ������֤��Ԥʤ��뤿��������٤���
     * (SQL�η�̥��åȤ�����ǡ����Ǽ������������˻��Ѥ���)
     * ��������ơ�
     * ��SQL�β���(prepare)��ԤʤäƤ���SQLʸ��¹Ԥ���
     * ��SQLʸ�μ¹ԥ��顼��ȯ�������顢�ȥ�󥶥����������Хå�����
     * @access public
     * @param string $sql SQLʸ
     * @param array $params SQLʸ�˥��åȤ���ѥ�᡼��
     * @param string $fetchMode�ե��å��⡼��(�ǥե���ȡ�DB_FETCHMODE_ORDERED)
	 *  DB_FETCHMODE_ORDERED����������ǡ����򥤥�ǥå�������([0][1]��)�Ǽ�������
	 *  DB_FETCHMODE_ASSOC����������ǡ�����Ϣ������([id][name]��)�Ǽ�������
	 *  DB_FETCHMODE_OBJECT����������ǡ����򥪥֥�������($data->id��)�Ǽ�������
     * @return array SQLʸ�μ¹Է��(���쥳����)
     * 		(Select������������硢2�������󥪥֥������Ȥ򥻥åȤ���)
     * @throws DatabaseException SQLʸ�μ¹ԥ��顼��ȯ��������
     */
	function execQuerySqlAll($sql, $params = array(), 
		$fetchMode = DB_FETCHMODE_DEFAULT)
	{
		$rs = &$this->db->getAll($sql, $params, $fetchMode);

		if (DB::isError($rs)) {
			$this->db->rollback();
			throw new DatabaseException($rs->getMessage());
		}

		return $rs;
	}

    /**
     * SQLʸ��¹Ԥ���Ϣ������ǡ������������(SELECT����)
     * ���롼�׽����ʤɤǤϻ��Ѥ��ʤ���
     * ��SQL�β��Ϥ������֤��Ԥʤ��뤿��������٤���
     * (SQL�η�̥��åȤ�Ϣ������ǡ����Ǽ���������(�ץ������ꥹ��)����
     * ���Ѥ���)
     * ��������ơ�
     * ��select����Ƭ�����򥭡�(key)�ˡ��Ĥ�Υ�����������(array)�Ȥ���
     *  �������롣
     *	(�ǡ�������) array([key]=>array([0]=>data, ��������)
     * ��select�Υ���ब2�󤷤��ʤ����ϡ��ͤȤ��Ƽ������롣
     *	(�ǡ�������) array[key]=>data
     * ��SQL�β���(prepare)��ԤʤäƤ���SQLʸ��¹Ԥ���
     * ��SQLʸ�μ¹ԥ��顼��ȯ�������顢�ȥ�󥶥����������Хå�����
     * @access public
     * @param string $sql SQLʸ
     * @param boolean $force Ϣ������ǤϤʤ�����ˤ��뤫
     * 			(�ǥե���ȡ�false)
     * @param array $params SQLʸ�˥��åȤ���ѥ�᡼��
     * @param string $fetchMode�ե��å��⡼��(�ǥե���ȡ�DB_FETCHMODE_ORDERED)
	 *  DB_FETCHMODE_ORDERED����������ǡ����򥤥�ǥå�������([0][1]��)�Ǽ�������
	 *  DB_FETCHMODE_ASSOC����������ǡ�����Ϣ������([id][name]��)�Ǽ�������
	 *  DB_FETCHMODE_OBJECT����������ǡ����򥪥֥�������($data->id��)�Ǽ�������
     * @param boolean $group ������ʣ�����۲������������Ҥˤ��뤫
     * 			(�ǥե���ȡ�false)
     * @return array SQLʸ�μ¹Է��(���쥳����)
     * 		(Select������������硢Ϣ�����󥪥֥������Ȥ򥻥åȤ���)
     * @throws DatabaseException SQLʸ�μ¹ԥ��顼��ȯ��������
     */
	function execQuerySqlAssoc($sql, $force = false, $params = array(), 
		$fetchMode = DB_FETCHMODE_DEFAULT, $group = false)
	{
		$rs = &$this->db->getAssoc($sql, $force, $params, $fetchMode, $group);

		if (DB::isError($rs)) {
			$this->db->rollback();
			throw new DatabaseException($rs->getMessage());
		}

		return $rs;
	}

    /**
     * SQLʸ��¹Ԥ����ǽ������ǡ������������(SELECT����)
     * ���롼�׽����ʤɤǤϻ��Ѥ��ʤ���
     * ��SQL�β��Ϥ������֤��Ԥʤ��뤿��������٤���
     * (SQL�η�̥��åȤ�����ǡ����Ǽ������������˻��Ѥ���)
     * ���ǽ������ǡ�������������塢��̥��åȤ�������
     * ��������ơ�
     * ��SQL�β���(prepare)��ԤʤäƤ���SQLʸ��¹Ԥ���
     * ��SQLʸ�μ¹ԥ��顼��ȯ�������顢�ȥ�󥶥����������Хå�����
     * @access public
     * @param string $sql SQLʸ
     * @param array $params SQLʸ�˥��åȤ���ѥ�᡼��
     * @param string $fetchMode�ե��å��⡼��(�ǥե���ȡ�DB_FETCHMODE_ORDERED)
	 *  DB_FETCHMODE_ORDERED����������ǡ����򥤥�ǥå�������([0][1]��)�Ǽ�������
	 *  DB_FETCHMODE_ASSOC����������ǡ�����Ϣ������([id][name]��)�Ǽ�������
	 *  DB_FETCHMODE_OBJECT����������ǡ����򥪥֥�������($data->id��)�Ǽ�������
     * @return array SQLʸ�μ¹Է��(1�쥳����)
     * 		(Select������������硢1�������󥪥֥������Ȥ򥻥åȤ���)
     * @throws DatabaseException SQLʸ�μ¹ԥ��顼��ȯ��������
     */
	function execQuerySqlRow($sql, $params = array(), 
		$fetchMode = DB_FETCHMODE_DEFAULT)
	{
		$rs = &$this->db->getRow($sql, $params, $fetchMode);

		if (DB::isError($rs)) {
			$this->db->rollback();
			throw new DatabaseException($rs->getMessage());
		}

		return $rs;
	}
			
    /**
     * SQLʸ��¹Ԥ���
     * ���롼�׽����ʤɤǤϻ��Ѥ��ʤ���
     * ��SQL�β��Ϥ������֤��Ԥʤ��뤿��������٤���
     * (SQL���٤ⷫ���֤��¹Ԥ��ʤ����˻��Ѥ���)
     * ��������ơ�
     * ��SQL�β���(prepare)��ԤʤäƤ���SQLʸ��¹Ԥ���
     * ��SQLʸ�μ¹ԥ��顼��ȯ�������顢�ȥ�󥶥����������Хå�����
     * @access public
     * @param string $sql SQLʸ
     * @param array $params SQLʸ�˥��åȤ���ѥ�᡼��
     * @return object SQLʸ�μ¹Է��
     * 		(Select������������硢DB_Result���֥������Ȥ򥻥åȤ���)
     * 		(INSERT/UPDATE/DELETE������������硢DB_OK�򥻥åȤ���)
     * @throws DatabaseException SQLʸ�μ¹ԥ��顼��ȯ��������
     */
	function execQuerySql($sql, $params = array())
	{
		$rs = &$this->db->query($sql, $params);

		if (DB::isError($rs)) {
			$this->db->rollback();

			throw new DatabaseException($rs->getMessage());
		}

		return $rs;
	}

    /**
     * SQL������Ǽ�������Կ�����ꤹ��(SELECT����)
     * (SQL������Ǽ�������Կ������¤�����˻��Ѥ���)
     * ��������ơ�
     * ��SQL�β���(prepare)��ԤʤäƤ���SQLʸ��¹Ԥ���
     * ��SQLʸ�μ¹ԥ��顼��ȯ�������顢�ȥ�󥶥����������Хå�����
     * @access public
     * @param string $sql SQLʸ
     * @param int $from �������Ϲ�
     * @param int $cnt ����������
     * @param array $params SQLʸ�˥��åȤ���ѥ�᡼��
     * @return object SQLʸ�μ¹Է��
     * 		(Select������������硢DB_Result���֥������Ȥ򥻥åȤ���)
     * @throws DatabaseException SQLʸ�μ¹ԥ��顼��ȯ��������
     */
	function execLimitQuerySql($sql, $from, $cnt, $params = array())
	{
		$rs = &$this->db->limitQuery($sql, $from, $cnt, $params);

		if (DB::isError($rs)) {
			$this->db->rollback();
			throw new DatabaseException($rs->getMessage());
		}

		return $rs;
	}
	
    /**
     * �����Ѥ�SQLʸ���������
     * (executeSQL��¹Ԥ��뤿����Ѥ���)
     * ��������ơ�
     * ��SQLʸ�β��ϥ��顼��ȯ�������顢�ȥ�󥶥����������Хå�����
     * @access public
     * @param string $sql SQLʸ
     * @return object �����Ѥ�SQLʸ
     * @throws DatabaseException SQLʸ�β��ϥ��顼��ȯ��������
     */
	function prepareSql($sql)
	{
		$sth = &$this->db->prepare($sql);

		if (DB::isError($sth)) {
			$this->db->rollback();
			throw new DatabaseException($sth->getMessage());
        }

		return $sth;
	}

    /**
     * �����Ѥ�SQLʸ��¹Ԥ���
     * ���롼�׽����ʤɤˤƻ��Ѥ��롣
     * ��SQL�β��Ϥ������֤��Ԥʤ��ʤ�����������ᤤ��
     * (Ʊ��SQL�򷫤��֤��¹Ԥ�����˻��Ѥ���)
     * ��������ơ�
     * ��SQL�β���(prepare)�Ϥ����˽����Ѥߤ�SQLʸ��¹Ԥ���
     * ��SQLʸ�μ¹ԥ��顼��ȯ�������顢�ȥ�󥶥����������Хå�����
     * @access public
     * @param string $sql SQLʸ
     * @param array $params SQLʸ�˥��åȤ���ѥ�᡼��
     * @return object SQLʸ�μ¹Է��
     * 		(Select������������硢DB_Result���֥������Ȥ򥻥åȤ���)
     * 		(INSERT/UPDATE/DELETE������������硢DB_OK�򥻥åȤ���)
     * @throws DatabaseException SQLʸ�μ¹ԥ��顼��ȯ��������
     */
	function execSql($sth, $params = array())
	{
		$rs = &$this->db->execute($sth, $params);

		if (DB::isError($rs)) {
			$this->db->rollback();
			throw new DatabaseException($rs->getMessage());
        }

		return $rs;
	}

    /**
     * �����Ѥ�SQLʸ��ޤȤ�Ƽ¹Ԥ���(INSERT/UPDATE/DELETE����)
     * ������ǡ���������Ͽ����ʤɤ˻��Ѥ��롣
     * ��SQL�β��Ϥ������֤��Ԥʤ��ʤ�����������ᤤ��
     * (Ʊ��SQL�򷫤��֤��¹Ԥ�����˻��Ѥ���)
     * ��������ơ�
	 * ��SQL�β���(prepare)�Ϥ����˽����Ѥߤ�SQLʸ��¹Ԥ���
     * ���¹�����ǽ��������Ԥ�����硢�ʹߤΥǡ����Ͻ�������ޤ���
     * ��SQLʸ�μ¹ԥ��顼��ȯ�������顢�ȥ�󥶥����������Хå�����
     * @access public
     * @param string $sql SQLʸ
     * @param array $data SQLʸ�˥��åȤ�������ǡ���
     * @return object SQLʸ�μ¹Է��
     * 		(INSERT/UPDATE/DELETE������������硢DB_OK�򥻥åȤ���)
     * @throws DatabaseException SQLʸ�μ¹ԥ��顼��ȯ��������
     */
	function execMultipleSql($sth, $data = array())
	{
		$rs = &$this->db->executeMultiple($sth, $data);

		if (DB::isError($rs)) {
			$this->db->rollback();
			throw new DatabaseException($rs->getMessage());
        }

		return $rs;
	}
}
?>