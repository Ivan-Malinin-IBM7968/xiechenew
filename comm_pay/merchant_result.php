<?php
// PHP version of merchant_result.jsp
//����B2CAPIͨ�ð��php�ͻ��˵��ò���
//��    �ߣ�bocomm
//����ʱ�䣺2012-4-10
?>
<html>
    <head>
        <title>��ͨ�����̻����Խ��ҳ��</title>

        <meta http-equiv = "Content-Type" content = "text/html; charset=GBK">
    </head>


    <body bgcolor = "#FFFFFF" text = "#000000">

<?php
	require_once ("config.inc");  //�����ļ�
	require_once("function.php");
	$tranCode = "cb2200_verify";
	$notifyMsg = $_REQUEST["notifyMsg"];   
	$lastIndex = strripos($notifyMsg,"|");
	$signMsg = substr($notifyMsg,$lastIndex+1); //ǩ����Ϣ
	$srcMsg = substr($notifyMsg,0,$lastIndex+1);//ԭ��

	//���ӵ�ַ
	$socketUrl = "tcp://".$socket_ip.":".$socket_port;
	$fp = stream_socket_client($socketUrl, $errno, $errstr, 30);
	$retMsg="";
	//
	if (!$fp) {
		echo "$errstr ($errno)<br />\n";
	} else 
	{
		$in  = "<?xml version='1.0' encoding='UTF-8'?>";
		$in .= "<Message>";
		$in .= "<TranCode>".$tranCode."</TranCode>";
		$in .= "<MsgContent>".$notifyMsg."</MsgContent>";
		$in .= "</Message>";
		fwrite($fp, $in);
		while (!feof($fp)) {
			$retMsg =$retMsg.fgets($fp, 1024);
			
		}
		fclose($fp);
	}	
	
	//��������xml
	$dom = new DOMDocument;
	$dom->loadXML($retMsg);

	$retCode = $dom->getElementsByTagName('retCode');
	$retCode_value = $retCode->item(0)->nodeValue;
	
	$errMsg = $dom->getElementsByTagName('errMsg');
	$errMsg_value = $errMsg->item(0)->nodeValue;

	//echo "retCode=".$retCode_value."  "."errMsg=".$errMsg_value;
	if($retCode_value != '0')
       {
           echo "���׷����룺".$retCode_value."<br>";
           echo "���״�����Ϣ��" .$errMsg_value."<br>";
       }
       else
       {
		   $arr = preg_split("/\|{1,}/",$srcMsg);
			print_r($arr);
			if($arr[9]==1){
				//------------------------------
				//����ҵ��ʼ
				//------------------------------
				//��ȡ��վ����ID
				//$leng =  strlen($out_trade_no);
				//$lengout_trade_no = substr($out_trade_no,14,$leng-14);
				//$lengout_trade_no = explode(',',$lengout_trade_no);
				$membercoupon_info = get_membercoupons($arr[1]);
				$gmt_payment=$arr[6].$arr[7];
				save_txmembercoupon_pay($arr[1],$arr[8],$gmt_payment,$arr[2],$arr[9],1);

				if($membercoupon_info){
					$is_pa_order = 0;
					foreach($membercoupon_info as $k=>$v){
						if($v['is_pay']==0){
								update_membercoupon_state($v['membercoupon_id'],1);
								coupon_send_sms($v['membercoupon_id']);
							if($v['coupon_id']){
								update_coupon_count($v['coupon_id']);
							}
						}
						if($v['pa'] == 1) {
							$is_pa_order = 1;
						}
					}
				}
				//�������ݿ��߼�
				//ע�⽻�׵���Ҫ�ظ�����
				//!!!ע���жϷ��ؽ��!!!
				
				//------------------------------
				//����ҵ�����
				//------------------------------
				if($is_pa_order == 1) {
					header("Location:http://baoyang.pahaoche.com/myhome/mycoupon".$membercoupon_info[0]['coupon_type'].".html");exit;
				}else {
					header("Location:http://www.xieche.com.cn/myhome/mycoupon".$membercoupon_info[0]['coupon_type'].".html");exit;
				}
				echo "<br/>" . "֧���ɹ�" . "<br/>";
			}
?> 

        <table width = "75%" border = "0" cellspacing = "0" cellpadding = "0">
            <tr>
                <td width = "14%">
                    �̻��ͻ���
                </td>

                <td width = "86%">

                    <?php
                    print $arr[0];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    �������
                </td>

                <td width = "86%">

                   <?php
                    print $arr[1];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ���׽��
                </td>

                <td width = "86%">

                   <?php
                    print $arr[2];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ���ױ���
                </td>

                <td width = "86%">

                  <?php
                    print $arr[3];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ƽ̨���κ�
                </td>

                <td width = "86%">

                   <?php
                    print $arr[4];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    �̻����κ�
                </td>

                <td width = "86%">

                   <?php
                    print $arr[5];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ��������
                </td>

                <td width = "86%">

                    <?php
                    print $arr[6];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ����ʱ��
                </td>

                <td width = "86%">

                    <?php
                    print $arr[7];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ������ˮ��
                </td>

                <td width = "86%">

                    <?php
                    print $arr[8];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ���׽��
                </td>

                <td width = "86%">
                   <?php
                    print $arr[9];
                    ?>

                    &nbsp;[1:�ɹ�]
                </td>
            </tr>

            <tr>
                <td width = "14%">
                    �������ܶ�
                </td>

                <td width = "86%">

                   <?php
                    print $arr[10];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ���п�����
                </td>

                <td width = "86%">
                   <?php
                    print $arr[11];
                    ?>

                    &nbsp;[0:��ǿ� 1��׼���ǿ� 2:���ǿ�]
                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ���б�ע
                </td>

                <td width = "86%">

                    <?php
                    print $arr[12];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ������Ϣ����
                </td>

                <td width = "86%">
                   <?php
                    print $arr[13];
                    ?>

                </td>
            </tr>

			 <tr>
                <td width = "14%">
                    IP
                </td>

                <td width = "86%">

                    <?php
                    print $arr[14];
                    ?>

                </td>
            </tr>
            <tr>
                <td width = "14%">
                    Referer
                </td>

                <td width = "86%">

                     <?php
                    print $arr[15];
                    ?>

                </td>
            </tr>
             <tr>
                <td width = "14%">
                    �̻���ע(base64������ַ���������Ҫ���ص���Ҫbase64����ԭ��)
                </td>

                <td width = "86%">

                   <?php
                    print $arr[16];
                    ?>


                </td>
            </tr>
        </table>
		<?php
			}
		?>
        <p>
            &nbsp;
        </p>
    </body>
</html>