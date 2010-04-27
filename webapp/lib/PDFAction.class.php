<?php
require_once(MO_PEAR_DIR . '/Text/Password.php');
require_once(MO_LIB_DIR . '/pdf/mbfpdfi.php');

/**
 * PDFActionクラス
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
     * PDFファイルを出力する
     * （outputを呼び出す）
     * @access public
     * @return object VIEW定数
     */
	public function execute ()
	{
		$this->output();
		return View::NONE;
	}

	/**
	 * PDFインスタンスを生成する
	 * マルチバイトのPDFインスタンスを作成し、マルチバイトフォントを設定する。
	 * 使用するマルチバイトフォントは、下記クライアントOS別に自動設定する。
	 * (Windows) MS-Pゴシック
	 * (Mac, Linux）AcrobatReader日本語フォント(明朝体)
	 * (その他）MS-Pゴシック
	 * @access public
	 * @param string $orientation 用紙方向（P:縦、L:横）
	 * @param string $unit 単位（pt:ポイント、mm:ミリ、cm:センチ、in:インチ）
	 * @param string $format 用紙サイズ（A3、A4、A5、letter、legal）
	 * @return MBfpdi
	 */
	public function createPDF($orientation='P',$unit='mm',$format='A4') {
		/* マルチバイトPDFインスタンスを作成する */
	    $pdf = &new MBfpdi($orientation, $unit, $format);
	    $pdf->SetProtection(array('print'), '', 
	    	CommonEncryption::getHashdata(Text_password::create(32, "unpronounceable")));
	    $pdf->SetAuthor($_SERVER['HTTP_HOST']); // ドキュメントの著者をセットする
	    $pdf->SetFillColor(200, 200, 200); // 塗りつぶし色の設定

	    /* マルチバイトフォント設定 */
	    if (stripos($_SERVER['HTTP_USER_AGENT'], 'win') > 0) {
			$pdf->AddMBFont(GOTHIC, 'EUC-JP');
			$pdf->SetFont(GOTHIC); // フォントの設定
	    } elseif (stripos($_SERVER['HTTP_USER_AGENT'], 'mac') > 0) {
			$pdf->AddMBFont(KOZMIN, 'EUC-JP');
			$pdf->SetFont(KOZMIN); // フォントの設定
	    } elseif (stripos($_SERVER['HTTP_USER_AGENT'], 'linux') > 0) {
			$pdf->AddMBFont(KOZMIN, 'EUC-JP');
			$pdf->SetFont(KOZMIN); // フォントの設定
	    } else {
			$pdf->AddMBFont(GOTHIC, 'EUC-JP');
			$pdf->SetFont(GOTHIC); // フォントの設定
	    }
	    return $pdf;
	}
	
	/**
	 * PDFファイルを生成する
	 * createPDFを呼び出し、PDFインスタンスを取得する。
	 * outputにて、PDFファイルを生成する($pdf->Output())を呼び出す。
	 * @access public
	 */
	abstract function output();
}
?>