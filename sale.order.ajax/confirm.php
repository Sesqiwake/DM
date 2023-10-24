<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var $APPLICATION CMain
 */

if ($arParams["SET_TITLE"] == "Y")
{
	$APPLICATION->SetTitle(Loc::getMessage("SOA_ORDER_COMPLETE"));
}
?>

<? if (!empty($arResult["ORDER"])): ?>
	<?
	// echo "<pre style='display:none'>";
	// print_r($arResult["ORDER"]);
	// echo "</pre>";
	// $arOrderParams = CSaleOrderPropsValue::GetOrderProps($arResult["ORDER"]["ID"]);
	// 				 CSaleOrderPropsValue::GetList($arResult["ORDER"]["ID"]
	// $arOrderParams = (array) $arOrderParams;

	// foreach ($arOrderParams['arResult'] as $param) {
	// 	if ($param['PROPERTY_NAME'] == "BONUSES"){
	// 		$bonuses_count = $param['PROXY_VALUE'];
	// 	}
	// }


	$dbOrderProps = CSaleOrderPropsValue::GetList(
        array("SORT" => "ASC"),
        array("ORDER_ID" => $arResult["ORDER"]["ID"], "CODE"=>array("BONUSES"))
    );
    $arOrderProps = $dbOrderProps->GetNext();
    // while ($arOrderProps = $dbOrderProps->GetNext()):
    //         echo "<pre style='display:none'>"; print_r($arOrderProps); echo "</pre>";
    // endwhile;
	?>
	<section class="order-success section-offset">
        <div class="container">
          	<div class="section-heade order-success__header">
	            <div class="section-header__wrap">
	              	<h2 class="section-title">Ваш заказ успешно оформлен!</h2>
	            </div>
          	</div>

			<div class="order-success__wrapper">
				<div class="order-success__item">
	              	<p class="order-success__text">
	                	Ваш заказ <span>№ <?=$arResult["ORDER"]['ID'];?></span> на сумму <span><?=number_format($arResult['ORDER']['PRICE'], 2, '.', ' ')?> руб.</span> успешно размещен и поступил в обработку.
	              	</p>

	              	<?/*<p class="order-success__text">
	                	Ваш заказ <span>№ <?= !empty($arResult["ORDER"]['ID']) ? current($arResult["ORDER"]['ID_1C']) : $arResult["ORDER"]['ID_1C']?></span> на сумму <span><?=number_format($arResult['ORDER']['PRICE'], 2, '.', ' ')?> руб.</span> успешно размещен и поступил в обработку.
	              	</p>*/?>


	              	<?$arDeliv = CSaleDelivery::GetByID($arResult["ORDER"]['DELIVERY_ID']);?>
	              	<p class="order-success__text">Вы выбрали способ доставки: <span><?=$arDeliv['NAME']?></span></p>
	              	<?$arPaySys = CSalePaySystem::GetByID($arResult["ORDER"]['PAY_SYSTEM_ID'])?>
	              	<p class="order-success__text">В качестве способа оплаты Вы указали: <span><?=$arPaySys['NAME']?></span></p>
	              	<?if($arOrderProps['VALUE']){?>
	              		<p class="order-success__text"> Списано бонусов: <span><?=$arOrderProps['~VALUE'];?></span></p>
	              	<?}?>
	            </div>

	            <div class="order-success__item">
	              	<p class="order-success__text"> Товар будет находится в резерве в течение трех рабочих дней, и в случае не поступления оплаты в это время заказ будет расформирован.
	              	</p>
	            </div>

	            <div class="order-success__item">
	            	<?$arStatus = CSaleStatus::GetByID($arResult["ORDER"]['STATUS_ID']);?>
	              	<p class="order-success__text">Сейчас заказ находится в статусе: <span><?=$arStatus['NAME']?></span></p>
	            </div>

	            <div class="order-success__item">
	              	<p class="order-success__text">
		                Обо всех дальнейших изменениях статуса заказа Вы будете получать уведомления по электронной почте, и этаже информация доступна на странице <a href="/personal/orders/">История заказов</a> Вашего <a href="/personal/">Личного кабинета</a>.
	              	</p>
	            </div>

	            <div class="order-success__item">
	              	<p class="order-success__text"> В Личном кабинете Вы можете перейти к оплате заказа или скачать счет,  также отменить размещение заказа до момента его фактической отгрузки в Ваш адрес. Кроме того, используйте Личный кабинет для повторного размещения ранее сделанных заказов.
	              	</p>
	            </div>

	            <div class="order-success__item">
	              	<p class="order-success__text">
	                	По всем вопросам относительно заказа обращайтесь в Клиентскую службу Dentlman:<br> 
	                	<a href="tel:88043338121">8-804-333-81-21</a>, <a href="mailto:order@dentlman.ru">order@dentlman.ru</a>
	              	</p>
	            </div>
			</div>

			<?

			if ($arResult["ORDER"]["IS_ALLOW_PAY"] === 'Y')
			{
				if (!empty($arResult["PAYMENT"]))
				{
					foreach ($arResult["PAYMENT"] as $payment)
					{
						if ($payment["PAID"] != 'Y')
						{
							if (!empty($arResult['PAY_SYSTEM_LIST'])
								&& array_key_exists($payment["PAY_SYSTEM_ID"], $arResult['PAY_SYSTEM_LIST'])
							)
							{
								$arPaySystem = $arResult['PAY_SYSTEM_LIST'][$payment["PAY_SYSTEM_ID"]];

								if (empty($arPaySystem["ERROR"]))
								{
									?>
									<? if (strlen($arPaySystem["ACTION_FILE"]) > 0 && $arPaySystem["NEW_WINDOW"] == "Y" && $arPaySystem["IS_CASH"] != "Y"): ?>
										<?
										$orderAccountNumber = urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]));
										$paymentAccountNumber = $payment["ACCOUNT_NUMBER"];
										?>
										<script>
											window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=$orderAccountNumber?>&PAYMENT_ID=<?=$paymentAccountNumber?>');
										</script>
										<?=Loc::getMessage("SOA_PAY_LINK", array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".$orderAccountNumber."&PAYMENT_ID=".$paymentAccountNumber))?>
									<? if (CSalePdf::isPdfAvailable() && $arPaySystem['IS_AFFORD_PDF']): ?>
									<br/>
										<?=Loc::getMessage("SOA_PAY_PDF", array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".$orderAccountNumber."&pdf=1&DOWNLOAD=Y"))?>
									<? endif ?>
									<? else: ?>
										<?=$arPaySystem["BUFFERED_OUTPUT"]?>
									<? endif ?>
									<?
								}
								else
								{
									?>
									<span style="color:red;"><?=Loc::getMessage("SOA_ORDER_PS_ERROR")?></span>
									<?
								}
							}
							else
							{
								?>
								<span style="color:red;"><?=Loc::getMessage("SOA_ORDER_PS_ERROR")?></span>
								<?
							}
						}
					}
				}
			}
			else
			{
				?>
				<br /><strong><?=$arParams['MESS_PAY_SYSTEM_PAYABLE_ERROR']?></strong>
				<?
			}
			?>
		</div>
	</section>

<? else: ?>
	<section class="order-success section-offset">
        <div class="container">
          	<div class="section-heade order-success__header">
	            <div class="section-header__wrap">
	              	<h2 class="section-title"><?=Loc::getMessage("SOA_ERROR_ORDER")?></h2>
	            </div>
          	</div>

			<div class="order-success__wrapper">
				<div class="order-success__item">
	              	<p class="order-success__text">
						<?=Loc::getMessage("SOA_ERROR_ORDER_LOST", array("#ORDER_ID#" => $arResult["ACCOUNT_NUMBER"]))?>
						<?=Loc::getMessage("SOA_ERROR_ORDER_LOST1")?>
					</p>
				</div>
			</div>
		</div>
	</section>

<? endif ?>