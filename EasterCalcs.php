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
	//Like CLIPS, years AD 3300 and 3500 do not appear to be accurate.
	//   Year AD 2810 wrong as above for the Gregorian calendar only.
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
		
		//Convert Julian to Gregorian date
		if($iDatingMethod == iEDM_ORTHODOX)
		{
			//Ten days were skipped in the Gregorian calendar 5–14 Oct 1582.
			$iTempNum = 10;
			//Only one in every four century years is a leap year.
			if($iYearToFind > 1600)
			{
				$iTempNum = $iTempNum + $iFirstDig - 16 - floor(($iFirstDig - 16) / 4);
			}
			$imDay = $imDay + $iTempNum;
		}
		
	} else {
		//That is $iDatingMethod == iEDM_WESTERN
		//Using calculations from Ian Stewart http://www.whydomath.org/Reading_Room_Material/ian_stewart/2000_03.html .
		$iA = $iYearToFind % 19;
		$iB = (int)($iYearToFind / 100);
		$iC = $iYearToFind % 100;
		$iD = (int)($iB / 4);
		$iE = $iB % 4;
		$iG = (int)(((8 * $iB) + 13) / 25);
		$iH = ((19 * $iA) + $iB - $iD - $iG + 15) % 30;
		$iM = (int)(($iA + (11 * $iH)) / 319);
		$iJ = (int)($iC / 4);
		$iK = $iC % 4;
		$iL = ((2 * $iE) + (2 * $iJ) - $iK - $iH + $iM + 32) % 7;
		$iN = (int)(($iH - $iM + $iL + 90) / 25);
		$iP = ($iH - $iM + $iL + $iN + 19) % 32;
		$imDay = $iP;
		$imMonth = $iN;
		$dDate = mktime(0, 0, 0, $imMonth, $imDay, $iYearToFind);
		return $dDate;
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
	$dDate = mktime(0, 0, 0, $imMonth, $imDay, $iYearToFind);
	
	return $dDate;
}