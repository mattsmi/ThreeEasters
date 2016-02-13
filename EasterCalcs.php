<?php

//set the default timezone
date_default_timezone_set('UTC');

//Constants required for the Easter functions
define('iEDM_JULIAN', 1);
define('iEDM_ORTHODOX', 2);
define('iEDM_WESTERN', 3);
define('iFIRST_EASTER_YEAR', 326);
define('iFIRST_VALID_GREGORIAN_YEAR', 1583);
define('iLAST_VALID_GREGORIAN_YEAR', 4099);

function pCJDNToMilankovic($iCJDN) {
	//The Chronological Julian Day Number (CJDN) is a whole number representing a day.
	// Its day begins at 00.00 Local Time.
	//Calculations from http://aa.quae.nl/en/reken/juliaansedag.html .
	
	$iK3 = (9 * ($iCJDN - 1721120)) + 2;
	$iX3 = floor($iK3 / 328718);
	$iK2 = (100 * (floor(($iK3 % 328718) / 9))) + 99;
	$iX2 = floor($iK2 / 36525);
	$iK1 = (floor(($iK2 % 36525) / 100) * 5) + 2;
	$iX1 = floor(((floor(($iK2 % 36525) / 100) * 5) + 2) / 153);
	$iC0 = floor(($iX1 * 2) / 12);
	$iYear = (100 * $iX3) + $iX2 + $iC0;
	$iMonth = ($iX1 - (1 * $iC0)) + 3;
	$iDay = floor(($iK1 % 153) / 5) + 1;
	
	$dDate = mktime(0, 0, 0, $iMonth, $iDay, $iYear);
	return $dDate;
}

function pJulianToCJDN($baseYear, $baseMonth, $baseDay) {
	
	$iJ0 = 1721117;
	$iC0 = floor(($baseMonth - 3) / 12);
	$iJ1 = floor((1461 * ($baseYear + $iC0)) / 4);
	$iJ2 = floor(((153 * $baseMonth) - (1836 * $iC0) - 457) / 5);
	$iJ = $iJ1 + $iJ2 + $baseDay + $iJ0;
	return round($iJ);
}

function pF10_CalcEaster($iYearArg, $iEDM = 3)
{
	//Check values of arguments
	if(($iYearArg < iFIRST_EASTER_YEAR) || ($iYearArg > iLAST_VALID_GREGORIAN_YEAR))
	{
		return FALSE;
	}
	if(($iEDM < iEDM_JULIAN) || ($iEDM > iEDM_WESTERN))
	{
		return FALSE;
	}
	
	$dDate = pF15_CalcDateOfEaster($iYearArg, $iEDM);
	if(! $dDate)
	{
		//We have an error
		return FALSE;
	} else 
	{
		return $dDate;
	}
}

function pF15_CalcDateOfEaster($iYearArg, $iEDM = 3)
{
	
	$iYearToFind = intval($iYearArg);
	$iDatingMethod = intval($iEDM);
	//Check values of arguments
	if(($iYearToFind < iFIRST_EASTER_YEAR) || ($iYearToFind > iLAST_VALID_GREGORIAN_YEAR))
	{
		return FALSE;
	}
	if(($iEDM < iEDM_JULIAN) || ($iEDM > iEDM_WESTERN))
	{
		return FALSE;
	}
	
	//Set up Default Values for calculations
	$imDay = 0;
	$imMonth = 0;
	$iFirstDig = 0;
	$iRemain19 = 0;
	$iTempNum = 0;
	$iTableA = 0;
	$iTableB = 0;
	$iTableC = 0;
	$iTableD = 0;
	$iTableE = 0;
	
	//Calculate Easter Sunday date
	// first 2 digits of year (integer division)
	$iFirstDig = floor($iYearToFind / 100);
	// remainder of year / 19
	$iRemain19 = $iYearToFind % 19;

	if(($iDatingMethod == iEDM_JULIAN) || ($iDatingMethod == iEDM_ORTHODOX))
	{
		//Calculate the Paschal Full Moon date
		$iTableA = ((225 - 11 * $iRemain19) % 30) + 21;
		
		//Find the next Sunday
		$iTableB = ($iTableA - 19) % 7;
		$iTableC = (40 - $iFirstDig) % 7;
		
		$iTempNum = $iYearToFind % 100;
		$iTableD = ($iTempNum + floor($iTempNum / 4)) % 7;
		
		$iTableE = ((20 - $iTableB - $iTableC - $iTableD) % 7) + 1;
		$imDay = $iTableA + $iTableE;
		
	} else {
		//That is $iDatingMethod == iEDM_WESTERN
		# Calculate the Paschal Full Moon Date
		$iTempNum = floor(($iFirstDig - 15) / 2) + 202 - (11 * $iRemain19);
		$lFirstList = array(21, 24, 25, 27, 28, 29, 30, 31, 32, 34, 35, 38);
		$lSecondList = array(33, 36, 37, 39, 40);
		if(isset($lFirstList[intval($iFirstDig)]))
		{
			$iTempNum = $iTempNum - 1;
		} elseif(isset($lSecondList[intval($iFirstDig)]))
		{
			$iTempNum = $iTempNum - 2;
		}
		$iTempNum = $iTempNum % 30;
		
		$iTableA = $iTempNum + 21;
		if($iTempNum == 29)
		{
			$iTableA = $iTableA - 1;
		}
		if(($iTempNum == 28) && ($iRemain19 > 10))
		{
			$iTableA = $iTableA - 1;
		}
	
		//Find the next Sunday
		$iTableB = ($iTableA - 19) % 7;
		
		$iTableC = (40 - $iFirstDig) % 4;
		if($iTableC == 3)
		{
			$iTableC = $iTableC + 1;
		}
		if($iTableC > 1)
		{
			$iTableC = $iTableC + 1;
		}
		
		$iTempNum = $iYearToFind % 100;
		$iTableD = ($iTempNum + floor($iTempNum / 4)) % 7;
		
		$iTableE = ((20 - $iTableB - $iTableC - $iTableD) % 7) + 1;
		$imDay = $iTableA + $iTableE;
	}
	
	//Return the date
	if($imDay > 61)
	{
		$imDay = $imDay - 61;
		$imMonth = 5;
		//Easter may occur in May for $iEDM_ORTHODOX
	} elseif($imDay > 31)
	{
		$imDay = $imDay - 31;
		$imMonth = 4;
	} else {
		$imMonth = 3;
	}
	
	//Convert Julian to Gregorian date
	if($iDatingMethod == iEDM_ORTHODOX)
	{
		$dDate = pCJDNToMilankovic(pJulianToCJDN($iYearToFind, $imMonth, $imDay));
	} else {
		$dDate = mktime(0, 0, 0, $imMonth, $imDay, $iYearToFind);
	}
	
	return $dDate;
}