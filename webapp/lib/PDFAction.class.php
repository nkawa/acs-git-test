<?php
require_once(MO_PEAR_DIR . '/Text/Password.php');
require_once(MO_LIB_DIR . '/pdf/mbfpdfi.php');

/**
 * PDFAction���饹
 * @access public
 * @package webapp/lib
 * @category action
 * @author Tsutomu Wakuda <wakuda@withit.co.jp>
 * @sourcefile
 *
 */
abstract class PDFAction extends ActionEx
{
    /**
     * PDF�ե��������Ϥ���
     * ��output��ƤӽФ���
     * @access public
     * @return object VIEW���
     */
	public function execute ()
	{
		$this->output();
		return View::NONE;
	}

	/**
	 * PDF���󥹥��󥹤���������
	 * �ޥ���Х��Ȥ�PDF���󥹥��󥹤���������ޥ���Х��ȥե���Ȥ����ꤹ�롣
	 * ���Ѥ���ޥ���Х��ȥե���Ȥϡ��������饤�����OS�̤˼�ư���ꤹ�롣
	 * (Windows) MS-P�����å�
	 * (Mac, Linux��AcrobatReader���ܸ�ե����(��ī��)
	 * (����¾��MS-P�����å�
	 * @access public
	 * @param string $orientation �ѻ�������P:�ġ�L:����
	 * @param string $unit ñ�̡�pt:�ݥ���ȡ�mm:�ߥꡢcm:�������in:�������
	 * @param string $format �ѻ極������A3��A4��A5��letter��legal��
	 * @return MBfpdi
	 */
	public function createPDF($orientation='P',$unit='mm',$format='A4') {
		/* �ޥ���Х���PDF���󥹥��󥹤�������� */
	    $pdf = &new MBfpdi($orientation, $unit, $format);
	    $pdf->SetProtection(array('print'), '', 
	    	CommonEncryption::getHashdata(Text_password::create(32, "unpronounceable")));
	    $pdf->SetAuthor($_SERVER['HTTP_HOST']); // �ɥ�����Ȥ����Ԥ򥻥åȤ���
	    $pdf->SetFillColor(200, 200, 200); // �ɤ�Ĥ֤���������

	    /* �ޥ���Х��ȥե�������� */
	    if (stripos($_SERVER['HTTP_USER_AGENT'], 'win') > 0) {
			$pdf->AddMBFont(GOTHIC, 'EUC-JP');
			$pdf->SetFont(GOTHIC); // �ե���Ȥ�����
	    } elseif (stripos($_SERVER['HTTP_USER_AGENT'], 'mac') > 0) {
			$pdf->AddMBFont(KOZMIN, 'EUC-JP');
			$pdf->SetFont(KOZMIN); // �ե���Ȥ�����
	    } elseif (stripos($_SERVER['HTTP_USER_AGENT'], 'linux') > 0) {
			$pdf->AddMBFont(KOZMIN, 'EUC-JP');
			$pdf->SetFont(KOZMIN); // �ե���Ȥ�����
	    } else {
			$pdf->AddMBFont(GOTHIC, 'EUC-JP');
			$pdf->SetFont(GOTHIC); // �ե���Ȥ�����
	    }
	    return $pdf;
	}
	
	/**
	 * PDF�ե��������������
	 * createPDF��ƤӽФ���PDF���󥹥��󥹤�������롣
	 * output�ˤơ�PDF�ե��������������($pdf->Output())��ƤӽФ���
	 * @access public
	 */
	abstract function output();
}
?>