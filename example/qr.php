<?php
ini_set('date.timezone', 'America/Vancouver');
require_once "../lib/FlashPay.Api.php";
header("Content-Type:text/html;charset=utf-8");
/**
 * 流程：
 * 1、创建QRCode支付单，取得code_url，生成二维码
 * 2、用户扫描二维码，进行支付
 * 3、支付完成之后，FlashPay服务器会通知支付成功
 * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 */
//获取扫码
$input = new FlashPayUnifiedOrder();
$input->setOrderId(FlashPayConfig::PARTNER_CODE . date("YmdHis"));
$input->setDescription("test");
$input->setPrice("1");
$input->setCurrency("CAD");
$input->setNotifyUrl("https://pay.alphapay.ca//notify_url");
$input->setOperator("123456");
$currency = $input->getCurrency();
if (!empty($currency) && $currency == 'CNY') {
    //建议缓存汇率,每天更新一次,遇节假日或其他无汇率更新情况,可取最近一个工作日的汇率
    $inputRate = new FlashPayExchangeRate();
    $rate = FlashPayApi::exchangeRate($inputRate);
    if ($rate['return_code'] == 'SUCCESS') {
        $real_pay_amt = $input->getPrice() / $rate['rate'];
        if ($real_pay_amt < 0.01) {
            echo 'CNY转换CAD后必须大于0.01CAD';
            exit();
        }
    }
}

$result = FlashPayApi::qrOrder($input);
$url2 = $result["code_url"];

//跳转
$inputObj = new FlashPayRedirect();
$inputObj->setRedirect(urlencode('http://demo.alphapay.ca/success.php?order_id=' . strval($input->getOrderId())));
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>AlphaPay支付样例-扫码</title>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
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
<header >
	
	</header>
    

    <div id="fh5co-tour" data-section="tour">
		<div class="container">
			<div class="row row-bottom-padded-sm animate-box" data-animate-effect="fadeIn">
				<div class="col-md-12 section-heading text-center">
						<h2>QR Code</h2>
				
				</div>
			</div>
		
			<div class="row row-bottom-padded-lg">
				<div class="col-md-10 col-md-offset-1">
					<div class="col-md-4 animate-box" data-animate-effect="fadeInLeft">
                    <img alt="扫码支付" src="qrcode.php?data=<?php echo urlencode($url2); ?>" style="width:150px;height:150px;"/>
					</div>
					<div class="col-md-7 col-md-push-1">
						<h2>Method 1</h2>
						<p>Using Wechat to scan QR code for payment.</p>
					
					</div>
					
				</div>
			</div>

			<div class="row row-bottom-padded-lg">
				<div class="col-md-10 col-md-offset-1">
					<div class="col-md-4 col-md-push-8 animate-box" data-animate-effect="fadeInRight">
							<p><a href=<?php echo FlashPayApi::getQRRedirectUrl($result['pay_url'], $inputObj); ?> class="btn btn-primary">Redirect</a></p>	
					</div>
					<div class="col-md-7 col-md-pull-4">
						<h2>Method 2</h2>
						<p>Redirect to Alphap Pay service page to do payment</p>
					
					</div>
					
				</div>
			</div>

		</div>
	</div>




	<!-- jQuery -->
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