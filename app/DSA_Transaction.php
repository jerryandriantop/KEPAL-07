<?php

namespace Tools;

use phpseclib3\Crypt\DSA;
use phpseclib3\Crypt\PublicKeyLoader;

class DSA_Transaction
{
    public static $algorithm = 'aes-128-cbc';
    public $dbconn;
    public $message;
    public $signaiv;
    public $encryption_key;

    public function __construct($message = null, $signaiv = null, $encryption_key = null)
    {
        $this->message = $message;
        $this->signaiv = $signaiv;
        $this->encryption_key = $encryption_key;
        $this->dbconn = mysqli_connect("localhost","root","","electricks");
    }

    public function setMessage($message){
        $this->message = $message;
    }

    public function setSignaiv($signaiv){
        $this->signaiv = $signaiv;
    }

    public function setEncryption($encryption_key){
        $this->encryption_key = $encryption_key;
    }

    private function encryptMessage($message)
    {
        $cipher = "aes-128-cbc";
        //Generate a 128-bit encryption key 
        $encryption_key = openssl_random_pseudo_bytes(32);
        // Generate an initialization vector 
        $iv_size = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($iv_size);
        //Data to encrypt 
        $data = $message;
        $encrypted_data = openssl_encrypt($data, $cipher, $encryption_key, 0, $iv);
        // echo ("Encrypted Text: " . $encrypted_data);
        return [
            "encrypted_data" => $encrypted_data,
            "encryption_key" => $encryption_key,
            "iv" => base64_encode($iv),
        ];
    }

    public function makeDSA()
    {
        $key = DSA::createKey();
        $public = $key->getPublicKey()->toString("PKCS1");
        $private = $key->toString("PKCS1");
        $encryptedMessageData = $this->encryptMessage($this->message);
        $signature = $key->sign($this->message);
        $sgivnat = $encryptedMessageData['iv'] . "," . base64_encode($signature);
        // var_dump(explode(',',$data));
        return [
            'pblc_ky' => $public,
            'prvt_ky' => $private,
            'encrypt_ky' => base64_encode($encryptedMessageData['encryption_key']),
            'msg_crpt' => $encryptedMessageData['encrypted_data'],
            'sgivnat' => $sgivnat,
        ];
    }

    public function decrypt($encrypted_data)
    {
        $decrypted_data = openssl_decrypt($encrypted_data, $this->algorithm, $this->encryption_key, 0, $this->iv);
        return $decrypted_data;
    }

    public function verify($data)
    {
        $load_private = PublicKeyLoader::loadPrivateKey($data['private']);
        $signature = base64_decode(explode(',', $this->signaiv)[1]);
        $iv = base64_decode(explode(',', $this->signaiv)[0]);
        $decrypted_data = openssl_decrypt($data['cipher'], 'aes-128-cbc', base64_decode($this->encryption_key), 0, $iv); 
        $order_id = $data['order_id'];
        $result = $load_private->getPublicKey()->verify($decrypted_data, $signature) ?
        'valid signature' :
        'invalid signature';

        if($result == "valid signature"){
            $query = "UPDATE `order` SET status='Accept' WHERE order_id = $order_id";
            $result = mysqli_query($this->dbconn, $query);
            header('Location: orders.php');
        }
    }
}
