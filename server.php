<?php
    require_once "mysqlDB.php";

$db = new MySQLDB('localhost', 'root', '', 'gipay');

if(isset($_POST['login_user'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $query = "SELECT * FROM pemiliktoko WHERE username=";

    if(isset($username) && $username != ""){
        $username = $db->escapeString($username);
        $query .= "'".$username."' AND ";
    }
    if(isset($password) && $password != ""){
        $password = $db->escapeString($password);
        $query .= "password='".$password."'";
    }

    $res = $db->executeSelectQuery($query);

    if(count($res) == 0){
        $query2 = "SELECT * FROM penggunapublik WHERE username=";
        $query2 .= "'".$username."' AND ";
        $query2 .= "password='".$password."'";
        $res2 = $db->executeSelectQuery($query2);
        if(count($res2) != 0){
            session_start();
                $_SESSION['username'] = $username;
                $_SESSION['nama'] = $res2[0][3];
                $_SESSION['saldo'] = $res2[0][6];
                $_SESSION['noHp'] = $res2[0][5];
                $_SESSION['email'] = $res2[0][4];
            session_write_close();
            header('Location: profPub.php');
        }
        else{
            $query3 = "SELECT * FROM admin WHERE username=";
            $query3 .= "'".$username."' AND ";
            $query3 .= "password='".$password."'";
            $res3 = $db->executeSelectQuery($query3);
            if(count($res3) != 0){
                session_start();
                    $_SESSION['username'] = $username;
                session_write_close();
                header('Location: adminLPub.php');
            }
            else{
                echo "<script type='text/javascript'>alert('Username atau Password salah');window.location.href='index.php';</script>";
            }
        }
    }
    else{
        session_start();
            $_SESSION['username'] = $username;
            $_SESSION['nama'] = $res[0][3];
            $_SESSION['saldo'] = $res[0][8];
            $_SESSION['noHp'] = $res[0][7];
            $_SESSION['email'] = $res[0][6];
            $_SESSION['namaToko'] = $res[0][4];
            $_SESSION['alamatToko'] = $res[0][5];
            $idKota = $res[0][10];
            $query4 = "SELECT namaKota FROM kota WHERE idKota=$idKota";
            $res4 = $db->executeSelectQuery($query4);
            $_SESSION['kota'] = $res4[0][0];
        session_write_close();
        header('Location: profToko.php');
    }
    
}

if(isset($_POST['reg_toko'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama = $_POST['nama'];
    $namaToko = $_POST['namaToko'];
    $alamatToko = $_POST['alamatToko'];
    $kota = $_POST['kota'];
    $noHp = $_POST['noHp'];
    $email = $_POST['email'];
    $username = $db->escapeString($username);
    $password = $db->escapeString($password);
    $nama = $db->escapeString($nama);
    $namaToko = $db->escapeString($namaToko);
    $alamatToko = $db->escapeString($alamatToko);
    $email = $db->escapeString($email);
    $date = new DateTime('NOW', timezone_open("Asia/Bangkok"));
    $tanggal = date_format($date, "Y-m-j");
    $query = "SELECT idKota FROM kota WHERE namaKota=";
    $query .= "'".$kota."'";
    $res = $db->executeSelectQuery($query);
    if(count($res) == 0){
        $query2 = "INSERT INTO Kota(namaKota)
                   VALUES('$kota')";
        $res2 = $db->executeNonSelectQuery($query2);
        $query3 = "SELECT idKota FROM kota WHERE namaKota=";
        $query3 .= "'".$kota."'";
        $res3 = $db->executeSelectQuery($query3);
        $idKota = $res3[0][0];
    }
    else{
        $idKota = $res[0][0];
    }
    $query4 = "INSERT INTO pemiliktoko(username, password, nama, namaToko, alamatToko, email, noHp, saldo, tanggalSignUp, idKota)
              VALUES('$username', '$password', '$nama', '$namaToko', '$alamatToko', '$email', '$noHp', 0, '$tanggal', '$idKota')";
    $query_result = $db->executeNonSelectQuery($query4);
    echo "<script type='text/javascript'>alert('Register berhasil! Silahkan login');window.location.href='index.php';</script>";
}

if(isset($_POST['reg_pub'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama = $_POST['nama'];
    $noHp = $_POST['noHp'];
    $email = $_POST['email'];
    $username = $db->escapeString($username);
    $password = $db->escapeString($password);
    $nama = $db->escapeString($nama);
    $email = $db->escapeString($email);
    $date = new DateTime('NOW', timezone_open("Asia/Bangkok"));
    $tanggal = date_format($date, "Y-m-j");
    $query = "INSERT INTO penggunapublik(username, password, nama, email, noHp, saldo, tanggalSignUp)
              VALUES('$username', '$password', '$nama', '$email', '$noHp', 0, '$tanggal')";
    $query_result = $db->executeNonSelectQuery($query);
    echo "<script type='text/javascript'>alert('Register berhasil! Silahkan login');window.location.href='index.php';</script>";
}

if(isset($_POST['topup'])){
    $jumlah = $_POST['jumlah'];
    $nama = $_SESSION['nama'];
    $nama = $db->escapeString($nama);
    $query = "SELECT idUser FROM penggunapublik WHERE nama=";
    $query .= "'".$nama."'";
    $res = $db->executeSelectQuery($query);
    $idUser = $res[0][0];
    $query2 = "INSERT INTO verifikasi
               VALUES('$idUser', '$jumlah')";
    $query_result = $db->executeNonSelectQuery($query2);
    echo "<script type='text/javascript'>alert('Topup request berhasil');</script>";
}

function upSaldoPub($uname){
    $db = new MySQLDB('localhost', 'root', '', 'gipay');
    $username = $uname;
    $username = $db->escapeString($username);
    $query = "SELECT saldo FROM penggunapublik WHERE username=";
    $query .= "'".$username."'";
    $res = $db->executeSelectQuery($query);
    $saldo = $res[0][0];
    return $saldo;
}

function upSaldoToko($uname){
    $db = new MySQLDB('localhost', 'root', '', 'gipay');
    $username = $uname;
    $username = $db->escapeString($username);
    $query = "SELECT saldo FROM pemiliktoko WHERE username=";
    $query .= "'".$username."'";
    $res = $db->executeSelectQuery($query);
    $saldo = $res[0][0];
    return $saldo;
}

function getTopup($uname){
    $db = new MySQLDB('localhost', 'root', '', 'gipay');
    $username = $uname;
    $username = $db->escapeString($username);
    $query = "SELECT idUser FROM penggunapublik WHERE username=";
    $query .= "'".$username."'";
    $res = $db->executeSelectQuery($query);
    $idUser = $res[0][0];
    $query2 = "SELECT idTopup, jumlah, tanggal FROM historytopup WHERE idUser=$idUser";
    $query_result = $db->executeSelectQuery($query2);
    return $query_result;
}

function getTrans($uname){
    $db = new MySQLDB('localhost', 'root', '', 'gipay');
    $username = $uname;
    $username = $db->escapeString($username);
    $query = "SELECT idUser FROM penggunapublik WHERE username=";
    $query .= "'".$username."'";
    $query_result = $db->executeSelectQuery($query);
    $idUser = $query_result[0][0];
    $query2 = "SELECT namaToko, jumlah, tanggal, waktu FROM historytransaksi INNER JOIN pemiliktoko ON historytransaksi.idToko = pemiliktoko.idUser WHERE historytransaksi.idUser = $idUser";
    $res = $db->executeSelectQuery($query2);
    return $res;
}

function getTransToko($uname){
    $db = new MySQLDB('localhost', 'root', '', 'gipay');
    $username = $uname;
    $username = $db->escapeString($username);
    $query = "SELECT idUser FROM pemiliktoko WHERE username=";
    $query .= "'".$username."'";
    $query_result = $db->executeSelectQuery($query);
    $idUser = $query_result[0][0];
    $query2 = "SELECT jumlah, tanggal, waktu FROM historytransaksi WHERE idToko = $idUser";
    $res = $db->executeSelectQuery($query2);
    $query3 = "SELECT * FROM persentasipotongan";
    $res2 = $db->executeSelectQuery($query3);
    $potongan = $res2[0][0];
    $_SESSION['potongan'] = $potongan;
    return $res;
}

function getPenToko($uname){
    $db = new MySQLDB('localhost', 'root', '', 'gipay');
    $username = $uname;
    $username = $db->escapeString($username);
    $query = "SELECT idUser FROM pemiliktoko WHERE username=";
    $query .= "'".$username."'";
    $query_result = $db->executeSelectQuery($query);
    $idUser = $query_result[0][0];
    $query2 = "SELECT noRekening, jumlah, tanggal FROM historypenarikan WHERE idToko = $idUser";
    $res = $db->executeSelectQuery($query2);
    return $res;
}

function getListPub(){
    $db = new MySQLDB('localhost', 'root', '', 'gipay');
    $query = "SELECT idUser, username, password, nama, email, noHp, saldo FROM penggunapublik";
    $res = $db->executeSelectQuery($query);
    return $res;
}

function getListToko(){
    $db = new MySQLDB('localhost', 'root', '', 'gipay');
    $query = "SELECT idUser, username, password, nama, namaToko, alamatToko, email, noHp, saldo FROM pemiliktoko";
    $res = $db->executeSelectQuery($query);
    return $res;
}

function getListVer(){
    $db = new MySQLDB('localhost', 'root', '', 'gipay');
    $query = "SELECT * FROM verifikasi";
    $res = $db->executeSelectQuery($query);
    return $res;
}

function getPersen(){
    $db = new MySQLDB('localhost', 'root', '', 'gipay');
    $query = "SELECT * FROM persentasipotongan";
    $query_result = $db->executeSelectQuery($query);
    $res = $query_result[0][0];
    return $res;
}

function getSignPub(){
    $db = new MySQLDB('localhost', 'root', '', 'gipay');
    $query = "SELECT idUser, nama, tanggalSignUp FROM penggunapublik";
    $res = $db->executeSelectQuery($query);
    return $res;
}

function getSignToko(){
    $db = new MySQLDB('localhost', 'root', '', 'gipay');
    $query = "SELECT idUser, nama, tanggalSignUp FROM pemiliktoko";
    $res = $db->executeSelectQuery($query);
    return $res;
}

if(isset($_POST['pay'])){
    if(empty($_POST['idToko']) || empty($_POST['jumlah']) || empty($_POST['password'])){
        echo "<script type='text/javascript'>alert('Harap isi form dengan lengkap');window.location.href='payPub.php';</script>";
    }
    else{
        $username = $_SESSION['username'];
        $username = $db->escapeString($username);
        $query = "SELECT idUser FROM penggunapublik WHERE username=";
        $query .= "'".$username."'";
        $res = $db->executeSelectQuery($query);
        $idUser = $res[0][0];
        $password = $_POST['password'];
        $query = "SELECT * FROM penggunapublik WHERE idUser=$idUser AND password=";
        $query .= "'".$password."'";
        $query_result = $db->executeSelectQuery($query);
        if(count($query_result) != 0){
            $idToko = $_POST['idToko'];
            $query = "SELECT namaToko, alamatToko FROM pemiliktoko WHERE idUser=$idToko";
            $res = $db->executeSelectQuery($query);
            if(count($res) != 0){
                $_SESSION['idToko'] = $idToko;
                $_SESSION['namaToko'] = $res[0][0];
                $_SESSION['alamatToko'] = $res[0][1];
                $date = new DateTime('NOW', timezone_open("Asia/Bangkok"));
                $tanggalLok = date_format($date, "Y-m-j");
                $waktuLok = date_format($date, "H:i:s");
                $_SESSION['tanggal'] = $tanggalLok;
                $_SESSION['waktu'] = $waktuLok;
                $_SESSION['jumlah'] = $_POST['jumlah'];
                session_write_close();
                header('Location: konfirmPub.php');
            }
            else{
                echo "<script type='text/javascript'>alert('Id merchant salah');window.location.href='payPub.php';</script>";
            }
        }
        else{
            echo "<script type='text/javascript'>alert('Password anda salah');window.location.href='payPub.php';</script>";
        }
    }
}

if(isset($_POST['konfir_pay'])){
    $username = $_SESSION['username'];
    $query = "SELECT idUser, saldo FROM penggunapublik WHERE username=";
    $query .= "'".$username."'";
    $res = $db->executeSelectQuery($query);
    $idUser = $res[0][0];
    $saldo = $res[0][1];
    $jumlah = $_SESSION['jumlah'];
    if($saldo < $jumlah){
        echo "<script type='text/javascript'>alert('Saldo tidak cukup');window.location.href='payPub.php';</script>";
    }
    else{
        $idToko = $_SESSION['idToko'];
        $jumlah = $_SESSION['jumlah'];
        $tanggal = $_SESSION['tanggal'];
        $waktu = $_SESSION['waktu'];
        $query2 = "INSERT INTO historytransaksi
                VALUES('$idUser', '$idToko', '$jumlah', '$tanggal', '$waktu')";
        $res2 = $db->executeNonSelectQuery($query2);
        $upSaldo = $saldo - $jumlah;
        $query3 = "UPDATE penggunapublik
                   SET saldo = $upSaldo
                   WHERE idUser = $idUser";
        $res3 = $db->executeNonSelectQuery($query3);
        $query4 = "SELECT saldo FROM pemiliktoko WHERE idUser=$idToko";
        $res4 = $db->executeSelectQuery($query4);
        $saldoToko = $res4[0][0];
        $query5 = "SELECT * FROM persentasipotongan";
        $res5 = $db->executeSelectQuery($query5);
        $potongan = $res5[0][0];
        $persen = 100 - $potongan;
        $temp = $jumlah * $persen / 100;
        $upSaldo2 = $saldoToko + $temp;
        $query6 = "UPDATE pemiliktoko
                   SET saldo = $upSaldo2
                   WHERE idUser = $idToko";
        $res6 = $db->executeNonSelectQuery($query6);
        echo "<script type='text/javascript'>alert('Pembayaran berhasil');window.location.href='payPub.php';</script>";
    }
}

if(isset($_POST['cancel_pay'])){
    unset($_SESSION['idToko']);
    unset($_SESSION['namaToko']);
    unset($_SESSION['alamatToko']);
    unset($_SESSION['tanggal']);
    unset($_SESSION['waktu']);
    unset($_SESSION['jumlah']);
    header('Location: payPub.php');
}

if(isset($_POST['tarik_dana'])){
    $username = $_SESSION['username'];
    $query = "SELECT idUser, saldo FROM pemiliktoko WHERE username=";
    $query .= "'".$username."'";
    $res = $db->executeSelectQuery($query);
    $idUser = $res[0][0];
    $saldo = $res[0][1];
    $noRek = $_POST['noRek'];
    $jumlah = $_POST['jumlahDana'];
    $date = new DateTime('NOW', timezone_open("Asia/Bangkok"));
    $tanggal = date_format($date, "Y-m-j");
    if($saldo < $jumlah){
        echo "<script type='text/javascript'>alert('Saldo tidak cukup');window.location.href='penarikanToko.php';</script>";
    }
    else{
        $query2 = "INSERT INTO historypenarikan(noRekening, jumlah, tanggal, idToko)
                   VALUES('$noRek', '$jumlah', '$tanggal', '$idUser')";
        $res2 = $db->executeNonSelectQuery($query2);
        $newSaldo = $saldo - $jumlah;
        $query3 = "UPDATE pemiliktoko
                   SET saldo = $newSaldo
                   WHERE idUser = $idUser";
        $res3 = $db->executeNonSelectQuery($query3);
        echo "<script type='text/javascript'>alert('Penarikan berhasil');window.location.href='penarikanToko.php';</script>";
    }
}

if(isset($_POST['delete_pub'])){
    $idUser = $_POST['idUser'];
    $query = "DELETE FROM penggunapublik
              WHERE idUser = $idUser";
    $query_result = $db->executeNonSelectQuery($query);
    echo "<script type='text/javascript'>alert('User telah dihapus');window.location.href='adminLPub.php';</script>";
}

if(isset($_POST['delete_toko'])){
    $idUser = $_POST['idUser'];
    $query = "DELETE FROM pemiliktoko
              WHERE idUser = $idUser";
    $query_result = $db->executeNonSelectQuery($query);
    echo "<script type='text/javascript'>alert('User telah dihapus');window.location.href='adminLToko.php';</script>";
}

if(isset($_POST['verifikasi'])){
    $idUser = $_POST['idUser'];
    $query = "SELECT * FROM verifikasi WHERE idUser=$idUser LIMIT 1";
    $res = $db->executeSelectQuery($query);
    $idUserVer = $res[0][0];
    $jumlah = $res[0][1];
    $query2 = "SELECT saldo FROM penggunapublik WHERE idUser=$idUser";
    $res2 = $db->executeSelectQuery($query2);
    $saldo = $res2[0][0];
    $newSaldo = $saldo + $jumlah;
    $query3 = "UPDATE penggunapublik
               SET saldo = $newSaldo
               WHERE idUser = $idUser";
    $res3 = $db->executeNonSelectQuery($query3);
    $date = new DateTime('NOW', timezone_open("Asia/Bangkok"));
    $tanggal = date_format($date, "Y-m-j H:i:s");
    $query4 = "INSERT INTO historytopup(jumlah, tanggal, idUser)
               VALUES('$jumlah', '$tanggal', '$idUserVer')";
    $res4 = $db->executeNonSelectQuery($query4);
    $query5 = "DELETE FROM verifikasi
               WHERE idUser = $idUserVer AND jumlah = $jumlah
               LIMIT 1";
    $res5 = $db->executeNonSelectQuery($query5);
    echo "<script type='text/javascript'>alert('Verifikasi berhasil');window.location.href='adminVerif.php';</script>";
}

if(isset($_POST['persentasi'])){
    $persentasi = $_POST['potongan'];
    $query = "UPDATE persentasipotongan
              SET persentasi = $persentasi";
    $res = $db->executeNonSelectQuery($query);
    echo "<script type='text/javascript'>alert('Update berhasil');window.location.href='adminPotong.php';</script>";
}
?>