<?php

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set("log_errors", 1);
ini_set("error_log", "/charty/php-error.log");

function safeEscapeString($inp)
{
  if(is_array($inp))
    return array_map(__METHOD__, $inp);

// replace HTML tags '<>' with '[]'
  $temp1 = str_replace("<", "[", $inp);
  $temp2 = str_replace(">", "]", $temp1);

// but keep <br> or <br />
// turn <br> into <br /> so later it will be turned into ""
// using just <br> will add extra blank lines
  $temp1 = str_replace("[br]", "<br />", $temp2);
  $temp2 = str_replace("[br /]", "<br />", $temp1);

  if(!empty($temp2) && is_string($temp2))
  {
    return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $temp2);
  }

  return $temp2;
}

function left($leftstring, $leftlength)
{
  return(substr($leftstring, 0, $leftlength));
}



function Reduce_below_30($longitude)
{
  $lng = $longitude;

  while ($lng >= 30)
  {
    $lng = $lng - 30;
  }

  return $lng;
}

function Convert_Longitude($longitude)
{
  $signs = array (0 => 'Ari', 'Tau', 'Gem', 'Can', 'Leo', 'Vir', 'Lib', 'Sco', 'Sag', 'Cap', 'Aqu', 'Pis');

  $sign_num = floor($longitude / 30);
  $pos_in_sign = $longitude - ($sign_num * 30);
  $deg = floor($pos_in_sign);
  $full_min = ($pos_in_sign - $deg) * 60;
  $min = floor($full_min);
  $full_sec = round(($full_min - $min) * 60);

  if ($deg < 10)
  {
    $deg = "0" . $deg;
  }

  if ($min < 10)
  {
    $min = "0" . $min;
  }

  if ($full_sec < 10)
  {
    $full_sec = "0" . $full_sec;
  }

  return $deg . " " . $signs[$sign_num] . " " . $min . "' " . $full_sec . chr(34);
}

function Find_Specific_Report_Paragraph($phrase_to_look_for, $file)
{
  $string = "";
  $len = strlen($phrase_to_look_for);

  //put entire file contents into an array, line by line
  $file_array = file($file);

  // look through each line searching for $phrase_to_look_for
  for($i = 0; $i < count($file_array); $i++)
  {
    if (left(trim($file_array[$i]), $len) == $phrase_to_look_for)
    {
      $flag = 0;
      while (trim($file_array[$i]) != "*")
      {
        if ($flag == 0)
        {
          $string .= "<b>" . $file_array[$i] . "</b>";
        }
        else
        {
          $string .= $file_array[$i];
        }
        $flag = 1;
        $i++;
      }
      break;
    }
  }

  return $string;
}

  $months = array (0 => 'Choose month', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
  $my_error = "";

  // check if the form has been submitted
  if (isset($_POST['submitted']))
  {
    // get all variables from form
    $name = safeEscapeString($_POST["name"]);

    $month = safeEscapeString($_POST["month"]);
    $day = safeEscapeString($_POST["day"]);
    $year = safeEscapeString($_POST["year"]);

    $hour = safeEscapeString($_POST["hour"]);
    $minute = safeEscapeString($_POST["minute"]);

    $timezone = safeEscapeString($_POST["timezone"]);

    $long_deg = safeEscapeString($_POST["long_deg"]);
    $long_min = safeEscapeString($_POST["long_min"]);
    $ew = safeEscapeString($_POST["ew"]);

    $lat_deg = safeEscapeString($_POST["lat_deg"]);
    $lat_min = safeEscapeString($_POST["lat_min"]);
    $ns = safeEscapeString($_POST["ns"]);

    include("validation_class.php");

    //error check
    $my_form = new Validate_fields;

    $my_form->check_4html = true;

    $my_form->add_text_field("Name", $name, "text", "y", 40);

    $my_form->add_text_field("Month", $month, "text", "y", 2);
    $my_form->add_text_field("Day", $day, "text", "y", 2);
    $my_form->add_text_field("Year", $year, "text", "y", 4);

    $my_form->add_text_field("Hour", $hour, "text", "y", 2);
    $my_form->add_text_field("Minute", $minute, "text", "y", 2);

    $my_form->add_text_field("Time zone", $timezone, "text", "y", 4);

    $my_form->add_text_field("Longitude degree", $long_deg, "text", "y", 3);
    

    $my_form->add_text_field("Latitude degree", $lat_deg, "text", "y", 2);
   

    // additional error checks on user-entered data
    if ($month != "" And $day != "" And $year != "")
    {
      if (!$date = checkdate(settype ($month, "integer"), settype ($day, "integer"), settype ($year, "integer")))
      {
        $my_error .= "The date of birth you entered is not valid.<br>";
      }
    }

    if (($year < 1900) Or ($year >= 2100))
    {
      $my_error .= "Please enter a year between 1900 and 2099.<br>";
    }

    if (($hour < 0) Or ($hour > 23))
    {
      $my_error .= "Birth hour must be between 0 and 23.<br>";
    }

    if (($minute < 0) Or ($minute > 59))
    {
      $my_error .= "Birth minute must be between 0 and 59.<br>";
    }

    if (($long_deg < 0) Or ($long_deg > 179))
    {
      $my_error .= "Longitude degrees must be between 0 and 179.<br>";
    }

    if (($long_min < 0) Or ($long_min > 59))
    {
      $my_error .= "Longitude minutes must be between 0 and 59.<br>";
    }

    if (($lat_deg < 0) Or ($lat_deg > 65))
    {
      $my_error .= "Latitude degrees must be between 0 and 65.<br>";
    }

    if (($lat_min < 0) Or ($lat_min > 59))
    {
      $my_error .= "Latitude minutes must be between 0 and 59.<br>";
    }

    if (($ew == '-1') And ($timezone > 2))
    {
      $my_error .= "You have marked West longitude but set an east time zone.<br>";
    }

    if (($ew == '1') And ($timezone < 0))
    {
      $my_error .= "You have marked East longitude but set a west time zone.<br>";
    }

    $validation_error = $my_form->validation();

    if ((!$validation_error) || ($my_error != ""))
    {
      $error = $my_form->create_msg();
      echo "<TABLE align='center' WIDTH='98%' BORDER='0' CELLSPACING='15' CELLPADDING='0'><tr><td><center><b>";
      echo "<font color='#ff0000' size=+2>Error! - The following error(s) occurred:</font><br>";

      if ($error)
      {
        echo $error . $my_error;
      }
      else
      {
        echo $error . "<br>" . $my_error;
      }

      echo "</font>";
      echo "<font color='#c020c0'";
      echo "<br>PLEASE RE-ENTER YOUR TIME ZONE DATA. THANK YOU.<br><br>";
      echo "</font>";
      echo "</b></center></td></tr></table>";
    }
    else
    {
      // no errors in filling out form, so process form
      //get today's date
      $date_now = date ("Y-m-d");

      // calculate astronomic data
      $swephsrc = $_SERVER['DOCUMENT_ROOT'] . "/charty";
      $sweph = $_SERVER['DOCUMENT_ROOT'] . "/charty";

      // Unset any variables not initialized elsewhere in the program
      unset($PATH,$out,$pl_name,$longitude,$house_pos);

      //assign data from database to local variables
      $inmonth = $month;
      $inday = $day;
      $inyear = $year;

      $inhours = $hour;
      $inmins = $minute;
      $insecs = "0";

      $intz = $timezone;

      $my_longitude = $ew * ($long_deg + ($long_min / 60));
      $my_latitude = $ns * ($lat_deg + ($lat_min / 60));

      if ($intz >= 0)
      {
        $whole = floor($intz);
        $fraction = $intz - floor($intz);
      }
      else
      {
        $whole = ceil($intz);
        $fraction = $intz - ceil($intz);
      }

      $inhours = $inhours - $whole;
      $inmins = $inmins - ($fraction * 60);

      // adjust date and time for minus hour due to time zone taking the hour negative
      if ($inyear >= 2000)
      {
        $utdatenow = strftime("%d.%m.20%y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
      }
      else
      {
        $utdatenow = strftime("%d.%m.19%y", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));
      }

      $utnow = strftime("%H:%M:%S", mktime($inhours, $inmins, $insecs, $inmonth, $inday, $inyear));

      putenv("PATH=$PATH:$swephsrc");

      // get 10 planets and all house cusps
      exec ("swetest -edir$sweph -b$utdatenow -ut$utnow -p0123456789 -eswe -house$my_longitude,$my_latitude, -fPlj -g, -head", $out);

      // Each line of output data from swetest is exploded into array $row, giving these elements:
      // 0 = planet name
      // 1 = longitude
      // 2 = house position
      // planets are index 0 - index 9, house cusps are index 10 - 21
      foreach ($out as $key => $line)
      {
        $row = explode(',',$line);
        $pl_name[$key] = $row[0];
        $longitude[$key] = $row[1];
        $house_pos[$key] = $row[3];
      };

      //get house positions of planets here
      for ($x = 1; $x <= 12; $x++)
      {
        for ($y = 0; $y <= 9; $y++)
        {
          $pl = $longitude[$y] + (1 / 36000);
          if ($x < 12 And $longitude[$x + 9] > $longitude[$x + 10])
          {
            If (($pl >= $longitude[$x + 9] And $pl < 360) Or ($pl < $longitude[$x + 10] And $pl >= 0))
            {
              $house_pos[$y] = $x;
              continue;
            }
          }

          if ($x == 12 And ($longitude[$x + 9] > $longitude[10]))
          {
            if (($pl >= $longitude[$x + 9] And $pl < 360) Or ($pl < $longitude[10] And $pl >= 0))
            {
              $house_pos[$y] = $x;
            }
            continue;
          }

          if (($pl >= $longitude[$x + 9]) And ($pl < $longitude[$x + 10]) And ($x < 12))
          {
            $house_pos[$y] = $x;
            continue;
          }

          if (($pl >= $longitude[$x + 9]) And ($pl < $longitude[10]) And ($x == 12))
          {
            $house_pos[$y] = $x;
          }
        }
      }

      //generate natal report here (and display natal data)
      echo "<center>";

      $existing_name = $name;

      echo "<FONT color='#ff0000' SIZE='5' FACE='Arial'><b>Name = $existing_name </b></font><br /><br />";

      $secs = "0";
      if ($timezone < 0)
      {
        $tz = $timezone;
      }
      else
      {
        $tz = "+" . $timezone;
      }

      if ($year >= 2000)
      {
        echo '<b>Data for ' . strftime("%A, %B %d, 20%y at %X (time zone = GMT $tz hours)</b><br /><br /><br />\n", mktime($hour, $minute, $secs, $month, $day, $year));
      }
      else
      {
        echo '<b>Data for ' . strftime("%A, %B %d, 19%y at %X (time zone = GMT $tz hours)</b><br /><br /><br />\n", mktime($hour, $minute, $secs, $month, $day, $year));
      }

      echo "</center>";

      $pl_name[0] = "Sun";
      $pl_name[1] = "Moon";
      $pl_name[2] = "Mercury";
      $pl_name[3] = "Venus";
      $pl_name[4] = "Mars";
      $pl_name[5] = "Jupiter";
      $pl_name[6] = "Saturn";
      $pl_name[7] = "Uranus";
      $pl_name[8] = "Neptune";
      $pl_name[9] = "Pluto";
      $pl_name[10] = "Ascendant";
      $pl_name[11] = "House 2";
      $pl_name[12] = "House 3";
      $pl_name[13] = "House 4";
      $pl_name[14] = "House 5";
      $pl_name[15] = "House 6";
      $pl_name[16] = "House 7";
      $pl_name[17] = "House 8";
      $pl_name[18] = "House 9";
      $pl_name[19] = "MC (Midheaven)";
      $pl_name[20] = "House 11";
      $pl_name[21] = "House 12";

      $sign_name[1] = "ARIES";
      $sign_name[2] = "TAURUS";
      $sign_name[3] = "GEMINI";
      $sign_name[4] = "CANCER";
      $sign_name[5] = "LEO";
      $sign_name[6] = "VIRGO";
      $sign_name[7] = "LIBRA";
      $sign_name[8] = "SCORPIO";
      $sign_name[9] = "SAGITTARIUS";
      $sign_name[10] = "CAPRICORN";
      $sign_name[11] = "AQUARIUS";
      $sign_name[12] = "PISCES";

      $hr_ob = $hour;
      $min_ob = $minute;

      $unknown_time = 0;
      if (($hr_ob == 12) And ($min_ob == 0))
      {
        $unknown_time = 1;        // this person has an unknown birth time
      }

      echo '<center><table width="61.8%" cellpadding="0" cellspacing="0" border="0">';
      echo '<tr><td><font face="Verdana" size="3">';

      //display philosophy of astrology
      echo "<center><font size='+1' color='#0000ff'><b>MY PHILOSOPHY OF ASTROLOGY</b></font></center>";

      $file = "natal_files/philo.txt";
      $fh = fopen($file, "r");
      $string = fread($fh, filesize($file));
      fclose($fh);

      $philo = nl2br($string);
      echo "<font size=2>" . $philo . "</font>";


      if ($unknown_time == 0)
      {
        //display rising sign interpretation
        //get header first
        echo "<center><font size='+1' color='#0000ff'><b>THE RISING SIGN OR ASCENDANT</b></font></center>";

        $file = "natal_files/ascsign.txt";
        $fh = fopen($file, "r");
        $string = fread($fh, filesize($file));
        fclose($fh);

        echo "<br>";
        echo "<font size=2>" . $string . "</font>";
        echo "<b>" . " YOUR ASCENDANT IS: <br><br>" . "</b>";

        $s_pos = floor($longitude[10] / 30) + 1;
        $phrase_to_look_for = $sign_name[$s_pos] . " rising";
        $file = "natal_files/rising.txt";
        $string = Find_Specific_Report_Paragraph($phrase_to_look_for, $file);
        $string = nl2br($string);

        echo "<font size=2>" . $string . "</font>";
      }

      //display planet in sign interpretation
      //get header first
      echo "<center><font size='+1' color='#0000ff'><b>SIGN POSITIONS OF PLANETS</b></font></center>";

      $file = "natal_files/sign.txt";
      $fh = fopen($file, "r");
      $string = fread($fh, filesize($file));
      fclose($fh);

      $string = nl2br($string);
      $sign_interp = $string;

      // loop through each planet
      for ($i = 0; $i <= 6; $i++)
      {
        $s_pos = floor($longitude[$i] / 30) + 1;

        $deg = Reduce_below_30($longitude[$i]);
        if ($unknown_time == 1 And $i == 1 And ($deg < 7.7 Or $deg > 22.3))
        {
          continue;     //if the Moon is too close to the beginning or the end of a sign, then do not include it
        }
        $phrase_to_look_for = $pl_name[$i] . " in";
        $file = "natal_files/sign_" . trim($s_pos) . ".txt";
        $string = Find_Specific_Report_Paragraph($phrase_to_look_for, $file);
        $string = nl2br($string);
        $sign_interp .= $string;
      }

      echo "<font size=2>" . $sign_interp . "</font>";


      if ($unknown_time == 0)
      {
        //display planet in house interpretation
        //get header first
        echo "<center><font size='+1' color='#0000ff'><b>HOUSE POSITIONS OF PLANETS</b></font></center>";

        $file = "natal_files/house.txt";
        $fh = fopen($file, "r");
        $string = fread($fh, filesize($file));
        fclose($fh);

        $string = nl2br($string);
        $house_interp = $string;

        // loop through each planet
        for ($i = 0; $i <= 9; $i++)
        {
          $h_pos = $house_pos[$i];
          $phrase_to_look_for = $pl_name[$i] . " in";
          $file = "natal_files/house_" . trim($h_pos) . ".txt";
          $string = Find_Specific_Report_Paragraph($phrase_to_look_for, $file);
          $string = nl2br($string);
          $house_interp .= $string;
        }

        echo "<font size=2>" . $house_interp . "</font>";
      }


      //display planetary aspect interpretations
      //get header first
      echo "<center><font size='+1' color='#0000ff'><b>PLANETARY ASPECTS</b></font></center>";

      $file = "natal_files/aspect.txt";
      $fh = fopen($file, "r");
      $string = fread($fh, filesize($file));
      fclose($fh);

      $string = nl2br($string);
      $p_aspect_interp = $string;

      echo "<font size=2>" . $p_aspect_interp . "</font>";

      // loop through each planet
      for ($i = 0; $i <= 9; $i++)
      {
        for ($j = $i + 1; $j <= 10; $j++)
        {
          if (($i == 1 Or $j == 1 Or $j == 10) And $unknown_time == 1)
          {
            continue;     // do not allow Moon aspects or Ascendant aspects if birth time is unknown
          }

          $da = Abs($longitude[$i] - $longitude[$j]);
          if ($da > 180)
          {
            $da = 360 - $da;
          }

          // set orb - 8 if Sun or Moon, 6 if not Sun or Moon
          if ($i == 0 Or $i == 1 Or $j == 0 Or $j == 1)
          {
            $orb = 8;
          }
          else
          {
            $orb = 6;
          }

          // are planets within orb?
          $q = 1;
          if ($da <= $orb)
          {
            $q = 2;
          }
          elseif (($da <= 60 + $orb) And ($da >= 60 - $orb))
          {
            $q = 3;
          }
          elseif (($da <= 90 + $orb) And ($da >= 90 - $orb))
          {
            $q = 4;
          }
          elseif (($da <= 120 + $orb) And ($da >= 120 - $orb))
          {
            $q = 5;
          }
          elseif ($da >= 180 - $orb)
          {
            $q = 6;
          }

          if ($q > 1)
          {
            if ($q == 2)
            {
              $aspect = " blending with ";
            }
            elseif ($q == 3 Or $q == 5)
            {
              $aspect = " harmonizing with ";
            }
            elseif ($q == 4 Or $q == 6)
            {
              $aspect = " discordant to ";
            }

            $phrase_to_look_for = $pl_name[$i] . $aspect . $pl_name[$j];
            $file = "natal_files/" . strtolower($pl_name[$i]) . ".txt";
            $string = Find_Specific_Report_Paragraph($phrase_to_look_for, $file);
            $string = nl2br($string);
            echo "<font size=2>" . $string . "</font>";
          }
        }
      }


      //display closing
      echo "<br><center><font size='+1' color='#0000ff'><b>CLOSING COMMENTS</b></font></center>";

      if ($unknown_time == 1)
      {
        $file = "natal_files/closing_unk.txt";
      }
      else
      {
        $file = "natal_files/closing.txt";
      }
      $fh = fopen($file, "r");
      $string = fread($fh, filesize($file));
      fclose($fh);

      $closing = nl2br($string);
      echo "<font size=2>" . $closing . "</font>";

      echo '</font></td></tr>';
      echo '</table></center>';

      $retrograde = "          ";

      echo "<center>";
      echo "<img border='0' src='chartwheel.php?in_test=0&rx=$retrograde&p0=$longitude[0]&p1=$longitude[1]&p2=$longitude[2]&p3=$longitude[3]&p4=$longitude[4]&p5=$longitude[5]&p6=$longitude[6]&p7=$longitude[7]&p8=$longitude[8]&p9=$longitude[9]' width='640' height='640'>";
      echo "</center>";

      echo "<br><br>";

      //display natal data
      echo '<center><table width="50%" cellpadding="0" cellspacing="0" border="0">',"\n";

      echo '<tr>';
      echo "<td><font color='#0000ff'><b> Name </b></font></td>";
      echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
      if ($unknown_time == 1)
      {
        echo "<td>&nbsp;</td>";
      }
      else
      {
        echo "<td><font color='#0000ff'><b> House<br>position </b></font></td>";
      }
      echo '</tr>';

      for ($i = 0; $i <= 9; $i++)
      {
        echo '<tr>';
        echo "<td>" . $pl_name[$i] . "</td>";
        echo "<td><font face='Courier New'>" . Convert_Longitude($longitude[$i]) . "</font></td>";
        if ($unknown_time == 1)
        {
          echo "<td>&nbsp;</td>";
        }
        else
        {
          $hse = floor($house_pos[$i]);
          if ($hse < 10)
          {
            echo "<td>&nbsp; " . $hse . "</td>";
          }
          else
          {
            echo "<td>" . $hse . "</td>";
          }
        }
        echo '</tr>';
      }

      echo '<tr>';
      echo "<td> &nbsp </td>";
      echo "<td> &nbsp </td>";
      echo "<td> &nbsp </td>";
      echo "<td> &nbsp </td>";
      echo '</tr>';

      if ($unknown_time == 0)
      {
        echo '<tr>';
        echo "<td><font color='#0000ff'><b> Name </b></font></td>";
        echo "<td><font color='#0000ff'><b> Longitude </b></font></td>";
        echo "<td> &nbsp </td>";
        echo '</tr>';

        for ($i = 10; $i <= 21; $i++)
        {
          echo '<tr>';
          if ($i == 10)
          {
            echo "<td>Ascendant </td>";
          }
          elseif ($i == 19)
          {
            echo "<td>MC (Midheaven) </td>";
          }
          else
          {
            echo "<td>House " . ($i - 9) . "</td>";
          }
          echo "<td><font face='Courier New'>" . Convert_Longitude($longitude[$i]) . "</font></td>";
          echo "<td> &nbsp </td>";
          echo '</tr>';
        }
      }

      echo '</table></center>',"\n";
      echo "<br /><br />";

      include ('footer_data_entry.html');
      exit();
    }
  }

?>




<?php

?>
