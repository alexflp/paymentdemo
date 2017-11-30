<?php
require_once "../lib/FlashPay.Api.php";
// require_once 'Log.php';

//初始化日志
// $logHandler = new CLogFileHandler("../logs/" . date('Y-m-d') . '.log');
// $log = Log::Init($logHandler, 15);

//打印输出数组信息
function printf_info($data)
{
    foreach ($data as $key => $value) {
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}

$input = new FlashPayRetailQRCode();
$input->setOrderId(FlashPayConfig::PARTNER_CODE . date("YmdHis"));
$input->setDescription("test");
$input->setPrice("100");
$input->setCurrency("CAD");
$input->setNotifyUrl("https://pay.alphapay.ca//notify_url");
$input->setDeviceId("18651874535");
$input->setOperator("123456");
$currency = $input->getCurrency();
if (!empty($currency) && $currency == 'CNY') {
    //建议缓存汇率,每天更新一次,遇节假日或其他无汇率更新情况,可取最近一个工作日的汇率
    $inputRate = new FlashPayExchangeRate();
    $rate = FlashPayApi::exchangeRate($inputRate);
    if ($rate['return_code'] == 'SUCCESS') {
        $real_pay_amt = $input->getPrice() / $rate['rate'] ;
        if ($real_pay_amt < 0.01) {
            echo 'CNY转换CAD后必须大于0.01CAD';
            exit();
        }
    }
}

//支付下单
$result = FlashPayApi::retailQRCodeOrder($input);
$url2 = $result["code_url"];

?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>AlphaPay支付样例-线下QRCode支付</title>
      <link rel="shortcut icon" href="../../images/favicon.ico">
    
    <link href='https://fonts.googleapis.com/css?family=Work+Sans:400,300,700|Montserrat:400,700' rel='stylesheet' type='text/css'>
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="../../css/animate.css">
    <!-- Icomoon Icon Fonts-->
    <link rel="stylesheet" href="../../css/icomoon.css">
    <!-- Simple Line Icons -->
    <link rel="stylesheet" href="../../css/simple-line-icons.css">
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="../../css/owl.carousel.min.css">
    <link rel="stylesheet" href="../../css/owl.theme.default.min.css">
    <!-- Bootstrap  -->
    <link rel="stylesheet" href="../../css/bootstrap.css">

    
    <link rel="stylesheet" href="../../css/style.css">


    <!-- Modernizr JS -->
    <script src="../../js/modernizr-2.6.2.min.js"></script>
</head>

<body>


<div id="fh5co-tour" data-section="tour">
        <div class="container">
            <div class="row row-bottom-padded-sm animate-box" data-animate-effect="fadeIn">
                <div class="col-md-12 section-heading text-center">
                        <h2>Retail API</h2>

        
            <img alt="扫码支付" src="qrcode.php?data=<?php echo urlencode($url2); ?>" style="width:150px;height:150px;"/>
            
      

                
                </div>
            </div>
        
         

        </div>
</div>

 <script src="../../js/jquery.min.js"></script>
    <!-- jQuery Easing -->
    <script src="../../js/jquery.easing.1.3.js"></script>
    <!-- Bootstrap -->
    <script src="../../js/bootstrap.min.js"></script>
    <!-- Waypoints -->
    <script src="../../js/jquery.waypoints.min.js"></script>
    <!-- Owl Carousel -->
    <script src="../../js/owl.carousel.min.js"></script>
    <!-- Main JS (Do not remove) -->
    <script src="../../js/main.js"></script>
</body>
</html>