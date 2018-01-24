<?php echo $this->Html->script(array('script_dynamic_content'));?>
<?php
	$currencies = [
        "RWF"=>"RWF - Rwanda Franc",
        "RF"=>"RF - RWF -Rwanda Franc",
        "SDK"=>"SDK - SDK",
        "MRGT"=>"MRGT - MRGT",
        "USDS"=>"USDS - USD Small",
		"SPDS"=>"SPDS - SPDS",
		"SR"=>"SR - SR SAUDI RIYA",
        "CHF"=>"CHF - Switzerland Franc",
        "SWF"=>"SWF - Switzerland Franc",
        "AED"=>"AED - United Arab Emirates Dirham",
        "AFN"=>"AFN - Afghanistan Afghani",
        "ALL"=>"ALL - Albania Lek",
        "AMD"=>"AMD - Armenia Dram",
        "ANG"=>"ANG - Netherlands Antilles Guilder",
        "AOA"=>"AOA - Angola Kwanza",
        "ARS"=>"ARS - Argentina Peso",
        "AUD"=>"AUD - Australia Dollar",
        "AWG"=>"AWG - Aruba Guilder",
        "AZN"=>"AZN - Azerbaijan New Manat",
        "BAM"=>"BAM - Bosnia and Herzegovina",
        "BBD"=>"BBD - Barbados Dollar",
        "BDT"=>"BDT - Bangladesh Taka",
        "BGN"=>"BGN - Bulgaria Lev",
        "BHD"=>"BHD - Bahrain Dinar",
        "BIF"=>"BIF - Burundi Franc",
        "BMD"=>"BMD - Bermuda Dollar",
        "BND"=>"BND - Brunei Darussalam Dollar",
        "BOB"=>"BOB - Bolivia BolÃ­viano",
        "BRL"=>"BRL - Brazil Real",
        "BSD"=>"BSD - Bahamas Dollar",
        "BTN"=>"BTN - Bhutan Ngultrum",
        "BWP"=>"BWP - Botswana Pula",
        "BYN"=>"BYN - Belarus Ruble",
        "BZD"=>"BZD - Belize Dollar",
        "CAD"=>"CAD - Canada Dollar",
        "CDF"=>"CDF - Congo/Kinshasa Franc",
        "CLP"=>"CLP - Chile Peso",
        "CNY"=>"CNY - China Yuan Renminbi",
        "COP"=>"COP - Colombia Peso",
        "CRC"=>"CRC - Costa Rica Colon",
        "CUC"=>"CUC - Cuba Convertible Peso",
        "CUP"=>"CUP - Cuba Peso",
        "CVE"=>"CVE - Cape Verde Escudo",
        "CZK"=>"CZK - Czech Republic Koruna",
        "DJF"=>"DJF - Djibouti Franc",
        "DKK"=>"DKK - Denmark Krone",
        "DOP"=>"DOP - Dominican Republic Peso",
        "DZD"=>"DZD - Algeria Dinar",
        "EGP"=>"EGP - Egypt Pound",
        "ERN"=>"ERN - Eritrea Nakfa",
        "ETB"=>"ETB - Ethiopia Birr",
        "EUR"=>"EUR - Euro Member Countries",
        "FJD"=>"FJD - Fiji Dollar",
        "FKP"=>"FKP - Falkland Islands (Malvinas) Pound",
        "GBP"=>"GBP - United Kingdom Pound",
        "GEL"=>"GEL - Georgia Lari",
        "GGP"=>"GGP - Guernsey Pound",
        "GHS"=>"GHS - Ghana Cedi",
        "GIP"=>"GIP - Gibraltar Pound",
        "GMD"=>"GMD - Gambia Dalasi",
        "GNF"=>"GNF - Guinea Franc",
        "GTQ"=>"GTQ - Guatemala Quetzal",
        "GYD"=>"GYD - Guyana Dollar",
        "HKD"=>"HKD - Hong Kong Dollar",
        "HNL"=>"HNL - Honduras Lempira",
        "HRK"=>"HRK - Croatia Kuna",
        "HTG"=>"HTG - Haiti Gourde",
        "HUF"=>"HUF - Hungary Forint",
        "IDR"=>"IDR - Indonesia Rupiah",
        "ILS"=>"ILS - Israel Shekel",
        "IMP"=>"IMP - Isle of Man Pound",
        "INR"=>"INR - India Rupee",
        "IQD"=>"IQD - Iraq Dinar",
        "IRR"=>"IRR - Iran Rial",
        "ISK"=>"ISK - Iceland Krona",
        "JEP"=>"JEP - Jersey Pound",
        "JMD"=>"JMD - Jamaica Dollar",
        "JOD"=>"JOD - Jordan Dinar",
        "JPY"=>"JPY - Japan Yen",
        "KES"=>"KES - Kenya Shilling",
        "KGS"=>"KGS - Kyrgyzstan Som",
        "KHR"=>"KHR - Cambodia Riel",
        "KMF"=>"KMF - Comoros Franc",
        "KPW"=>"KPW - Korea (North) Won",
        "KRW"=>"KRW - Korea (South) Won",
        "KUW"=>"KUW - Kuwait Dinar",
        "KYD"=>"KYD - Cayman Islands Dollar",
        "KZT"=>"KZT - Kazakhstan Tenge",
        "LAK"=>"LAK - Laos Kip",
        "LBP"=>"LBP - Lebanon Pound",
        "LKR"=>"LKR - Sri Lanka Rupee",
        "LRD"=>"LRD - Liberia Dollar",
        "LSL"=>"LSL - Lesotho Loti",
        "LYD"=>"LYD - Libya Dinar",
        "MAD"=>"MAD - Morocco Dirham",
        "MDL"=>"MDL - Moldova Leu",
        "MGA"=>"MGA - Madagascar Ariary",
        "MKD"=>"MKD - Macedonia Denar",
        "MMK"=>"MMK - Myanmar (Burma) Kyat",
        "MNT"=>"MNT - Mongolia Tughrik",
        "MOP"=>"MOP - Macau Pataca",
        "MRO"=>"MRO - Mauritania Ouguiya",
        "MUR"=>"MUR - Mauritius Rupee",
        "MVR"=>"MVR - Maldives (Maldive Islands) Rufiyaa",
        "MWK"=>"MWK - Malawi Kwacha",
        "MXN"=>"MXN - Mexico Peso",
        "MYR"=>"MYR - Malaysia Ringgit",
        "MZN"=>"MZN - Mozambique Metical",
        "NAD"=>"NAD - Namibia Dollar",
        "NGN"=>"NGN - Nigeria Naira",
        "NIO"=>"NIO - Nicaragua Cordoba",
        "NOK"=>"NOK - Norway Krone",
        "NPR"=>"NPR - Nepal Rupee",
        "NZD"=>"NZD - New Zealand Dollar",
        "OMR"=>"OMR - Oman Rial",
        "PAB"=>"PAB - Panama Balboa",
        "PEN"=>"PEN - Peru Sol",
        "PGK"=>"PGK - Papua New Guinea Kina",
        "PHP"=>"PHP - Philippines Peso",
        "PKR"=>"PKR - Pakistan Rupee",
        "PLN"=>"PLN - Poland Zloty",
        "PYG"=>"PYG - Paraguay Guarani",
        "QAR"=>"QAR - Qatar Riyal",
        "RON"=>"RON - Romania New Leu",
        "RSD"=>"RSD - Serbia Dinar",
        "RUB"=>"RUB - Russia Ruble",
        "SAR"=>"SAR - Saudi Arabia Riyal",
        "SBD"=>"SBD - Solomon Islands Dollar",
        "SCR"=>"SCR - Seychelles Rupee",
        "SDG"=>"SDG - Sudan Pound",
        "SEK"=>"SEK - Sweden Krona",
        "SGD"=>"SGD - Singapore Dollar",
        "SHP"=>"SHP - Saint Helena Pound",
        "SLL"=>"SLL - Sierra Leone Leone",
        "SOS"=>"SOS - Somalia Shilling",
        "SPL*"=>"SPL* - Seborga Luigino",
        "SRD"=>"SRD - Suriname Dollar",
        "STD"=>"STD - SÃ£o TomÃ© and PrÃ­ncipe Dobra",
        "SVC"=>"SVC - El Salvador Colon",
        "SYP"=>"SYP - Syria Pound",
        "SZL"=>"SZL - Swaziland Lilangeni",
        "THB"=>"THB - Thailand Baht",
        "TJS"=>"TJS - Tajikistan Somoni",
        "TMT"=>"TMT - Turkmenistan Manat",
        "TND"=>"TND - Tunisia Dinar",
        "TOP"=>"TOP - Tonga Pa'anga",
        "TRY"=>"TRY - Turkey Lira",
        "TTD"=>"TTD - Trinidad and Tobago Dollar",
        "TVD"=>"TVD - Tuvalu Dollar",
        "TWD"=>"TWD - Taiwan New Dollar",
        "TZS"=>"TZS - Tanzania Shilling",
        "UAH"=>"UAH - Ukraine Hryvnia",
        "UGX"=>"UGX - Uganda Shilling",
        "USD"=>"USD - United States Dollar",
        "UYU"=>"UYU - Uruguay Peso",
        "UZS"=>"UZS - Uzbekistan Som",
        "VEF"=>"VEF - Venezuela Bolivar",
        "VND"=>"VND - Viet Nam Dong",
        "VUV"=>"VUV - Vanuatu Vatu",
        "WST"=>"WST - Samoa Tala",
        "XAF"=>"XAF - Africaine Franc BEAC",
        "XCD"=>"XCD - East Caribbean Dollar",
        "XDR"=>"XDR - International Monetary Fund (IMF)",
        "XOF"=>"XOF - (BCEAO) Franc",
        "XPF"=>"XPF - (CFP) Franc",
        "YER"=>"YER - Yemen Rial",
        "ZAR"=>"ZAR - South Africa Rand",
        "ZMW"=>"ZMW - Zambia Kwacha",
        "ZWD"=>"ZWD - Zimbabwe Dollar",

        // Ancient currencies
        "EEK"=>"EEK - Estonian Kroon",
        "XAU"=>'XAU - Troy Ounce Of gold,',
        "COU"=>"COU - Unidad-de-Valor-Real",
        "BYR"=>"BYR - Belarusian Ruble",
        "XAG"=>"XAG - Silver Ounce",
        "XPT"=>"XPT - Platinum Ounce",
        "XPT"=>"VEB - Venezuelan Bolivar",
        "VEB"=>"VEB - Venezuelan Bolivar",
        "CHW"=>"CHW",
        "XBB"=>"XBB - European Monetary Unit (EMU)",
        "USN"=>"USN - US Dollar (Next day)",
        "CYP"=>"CYP - Cyprus Pound",
        "MXV"=>"MXV - Mexican Unidad De Inversion",
        "ROL"=>"ROL - Romanian leu",
        "BOV"=>"BOV - Bolivian Mvdol",
        "LTL"=>"LTL - Lithuanian Litas",
        "XTS"=>"XTS",
        "ZMK"=>"ZMK - Zambian Kwacha",
        "XXX"=>"XXX",
        "CHE"=>"CHE",
        "XPD"=>"XPD - XPD - Palladium Ounces",
        "SKK"=>"SKK - Slovak Koruna",
        "XBD"=>"XBD",
        "XBC"=>"XBC",
        "USS"=>"USS",
        "XBA"=>"XBA",
        "MTL"=>"MTL - Maltese Lira ",
        "TMT"=>"TMT - Turkmenistan New Manat",
        "LVL"=>"LVL - Latvian Lats",
        "CLF"=>"CLF",
    ];
?>

<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="otherCurrencies form well">
<?php echo $this->Form->create('Currency'); ?>
	<fieldset>
		<legend><?php echo __('Add Currency'); ?></legend>
	<?php
		echo $this->Form->input('id',['type'=>'select','options'=>$currencies]);
		echo $this->Form->input('name',array('label'=>'Name eg. USD,GBP,Kshs'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<script>
	$(document).ready(function(){
		$('#CurrencyId').click(function(){
			$('#CurrencyName').val($("#CurrencyId option:selected").text());
		});
	});
</script>