<?PHP
/** 
 * 利用mcrypt做AES加密解密 
 * @author ts24<tsxw24@gmail.com> 
 */  
  
class Aes{  
    /** 
     * 算法,另外还有192和256两种长度 
     */  
   // const CIPHER = MCRYPT_RIJNDAEL_128;  
	//const CIPHER = MCRYPT_RIJNDAEL_192; 
	const CIPHER = MCRYPT_RIJNDAEL_256;  
    /** 
     * 模式  
     */  
//	const MODE = MCRYPT_MODE_NCFB; 
//	const MODE = MCRYPT_MODE_CBC; 
//	const MODE = MCRYPT_MODE_CFB; 
//	const MODE = MCRYPT_MODE_CTR; 
	const MODE = MCRYPT_MODE_ECB; 
//	const MODE = MCRYPT_MODE_OFB; 
//	const MODE = MCRYPT_MODE_NOFB; 
//	const MODE = MCRYPT_MODE_STEAM; 
  
    /** 
     * 加密 
     * @param string $key   密钥 
     * @param string $str   需加密的字符串 
     * @return type  
     */  
    static public function encode( $key, $str ){  
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(self::CIPHER,self::MODE),MCRYPT_RAND);  
        return mcrypt_encrypt(self::CIPHER, $key, $str, self::MODE, $iv);  
    }  
      
    /** 
     * 解密 
     * @param type $key 
     * @param type $str 
     * @return type  
     */  
    static public function decode( $key, $str ){  
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(self::CIPHER,self::MODE),MCRYPT_RAND);  
        return mcrypt_decrypt(self::CIPHER, $key, $str, self::MODE, $iv);  
    }  
}  
?>